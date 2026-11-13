<?php

namespace App\Services;

use App\Exceptions\KeycloakDirectoryException;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class KeycloakUserDirectory
{
    private ?string $clientUuid = null;

    public function __construct(private KeycloakAdminService $admin) {}

    private function request(string $method, string $path, #[\SensitiveParameter] array $data = []): mixed
    {
        $token = $this->admin->getAdminToken();
        if (! $token) {
            throw new KeycloakDirectoryException;
        }
        $url = rtrim(config('services.keycloak.base_url'), '/').'/admin/realms/'
            .rawurlencode(config('services.keycloak.realms')).$path;
        try {
            $response = Http::withToken($token)->acceptJson()->timeout(10)
                ->send($method, $url, $method === 'GET' ? ['query' => $data] : ['json' => $data]);
        } catch (\Throwable $exception) {
            throw new KeycloakDirectoryException;
        }
        if (! $response->successful()) {
            throw new KeycloakDirectoryException($response->status());
        }

        return $response->json();
    }

    public function users(string $search, int $page, int $perPage = 10): array
    {
        $filter = $search === '' ? [] : ['search' => $search];
        $total = $this->request('GET', '/users/count', $filter);
        $users = $this->request('GET', '/users', $filter + [
            'first' => ($page - 1) * $perPage, 'max' => $perPage, 'briefRepresentation' => 'true',
        ]);
        if (! is_numeric($total) || ! is_array($users) || ! array_is_list($users)) {
            throw new KeycloakDirectoryException;
        }

        return ['total' => (int) $total, 'users' => $users];
    }

    public function allUsers(string $search): array
    {
        $first = $this->users($search, 1, 100);
        $users = $first['users'];
        for ($page = 2; count($users) < $first['total']; $page++) {
            $batch = $this->users($search, $page, 100);
            if (! $batch['users'] || $batch['total'] !== $first['total']) {
                throw new KeycloakDirectoryException;
            }
            $users = array_merge($users, $batch['users']);
        }

        return collect($users)->unique('id')->values()->all();
    }

    public function user(string $id): array
    {
        $user = $this->request('GET', '/users/'.rawurlencode($id));
        if (! is_array($user) || ($user['id'] ?? null) !== $id) {
            throw new KeycloakDirectoryException;
        }

        return $user;
    }

    public function update(string $id, array $payload): void
    {
        $this->request('PUT', '/users/'.rawurlencode($id), $payload);
    }

    public function delete(string $id): void
    {
        $this->request('DELETE', '/users/'.rawurlencode($id));
    }

    public function resetPassword(string $id, #[\SensitiveParameter] string $password): void
    {
        $this->request('PUT', '/users/'.rawurlencode($id).'/reset-password', [
            'type' => 'password', 'value' => $password, 'temporary' => false,
        ]);
    }

    public function setRole(string $id, string $role): void
    {
        $normalize = function ($name) {
            $name = strtolower(trim(str_replace(['_', '-'], ' ', $name)));
            return match ($name) {
                'analis pengetahuan', 'analyst', 'analis' => 'analisis pengetahuan',
                'superadmin' => 'super admin', 'admin ippd' => 'admin',
                'member', 'kreator pengetahuan' => 'anggota', default => $name,
            };
        };
        $aliases = array_map($normalize, User::ALL_ROLES);
        $aliases = array_merge($aliases, ['superadmin', 'admin ippd', 'analis pengetahuan', 'analyst', 'analis', 'member', 'kreator pengetahuan']);
        $roles = $this->request('GET', '/roles');
        $target = collect($roles)->first(fn ($item) => $normalize($item['name']) === $normalize($role));
        if (! $target) {
            throw new KeycloakDirectoryException(400);
        }
        // Read effective mappings first to resolve and validate the configured application client.
        $this->role($id);
        foreach (['realm', 'clients/'.rawurlencode($this->clientUuid)] as $scope) {
            $path = '/users/'.rawurlencode($id).'/role-mappings/'.$scope;
            $direct = $this->request('GET', $path);
            $remove = array_values(array_filter($direct, fn ($item) => in_array($normalize($item['name']), $aliases, true)));
            if ($remove) {
                $this->request('DELETE', $path, $remove);
            }
        }
        $this->request('POST', '/users/'.rawurlencode($id).'/role-mappings/realm', [array_intersect_key($target, array_flip(['id', 'name']))]);
        if ($this->role($id) !== $role) {
            throw new KeycloakDirectoryException(409);
        }
    }

    public function role(string $id): string
    {
        $roles = $this->request('GET', '/users/'.rawurlencode($id).'/role-mappings/realm/composite');
        // Include only this application's client roles, never roles of unrelated clients.
        if ($this->clientUuid === null) {
            $clients = $this->request('GET', '/clients', ['clientId' => config('services.keycloak.client_id')]);
            $client = collect($clients)->firstWhere('clientId', config('services.keycloak.client_id'));
            if (! is_array($client) || empty($client['id'])) {
                throw new KeycloakDirectoryException;
            }
            $this->clientUuid = $client['id'];
        }
        $clientRoles = $this->request('GET', '/users/'.rawurlencode($id).'/role-mappings/clients/'.rawurlencode($this->clientUuid).'/composite');
        if (! is_array($roles) || ! is_array($clientRoles)) {
            throw new KeycloakDirectoryException;
        }
        $names = collect(array_merge($roles, $clientRoles))->pluck('name')->map(fn ($name) => strtolower(trim(str_replace(['_', '-'], ' ', $name))))->all();
        foreach ([
            User::ROLE_SUPER_ADMIN => ['super admin', 'superadmin'],
            User::ROLE_ADMIN_PUSAT => ['admin pusat'],
            User::ROLE_ADMIN => ['admin', 'admin ippd'],
            User::ROLE_MODERATOR => ['moderator'],
            User::ROLE_ANALIS => ['analisis pengetahuan', 'analis pengetahuan', 'analyst', 'analis'],
        ] as $role => $aliases) {
            if (array_intersect($names, $aliases)) {
                return $role;
            }
        }

        return User::ROLE_ANGGOTA;
    }
}

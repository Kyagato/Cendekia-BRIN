<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeycloakAdminService
{
    protected string $baseUrl;
    protected string $realm;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.keycloak.base_url', ''), '/');
        $this->realm = config('services.keycloak.realms', 'master');
        $this->clientId = config('services.keycloak.client_id', '');
        $this->clientSecret = config('services.keycloak.client_secret', '');
    }

    /**
     * Memeriksa apakah konfigurasi Keycloak sudah terisi di .env.
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Mendapatkan Admin Access Token dari Keycloak menggunakan Client Credentials Grant.
     * Token di-cache di memory/redis/database agar tidak membebani server Keycloak di setiap request.
     */
    public function getAdminToken(): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('KeycloakAdminService: Konfigurasi Keycloak di .env belum lengkap.');
            return null;
        }

        $cacheKey = "keycloak_admin_token_{$this->realm}_{$this->clientId}";

        return Cache::remember($cacheKey, now()->addMinutes(4), function () {
            try {
                $tokenUrl = "{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token";

                $response = Http::asForm()->timeout(10)->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

                if (!$response->successful()) {
                    Log::error('KeycloakAdminService: Gagal mendapatkan token admin.', [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);
                    return null;
                }

                $data = $response->json();
                return $data['access_token'] ?? null;
            } catch (Exception $e) {
                Log::error('KeycloakAdminService: Exception saat mengambil admin token: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Mengambil semua daftar Realm Roles yang ada di Keycloak.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function getRealmRoles(): array
    {
        $token = $this->getAdminToken();
        if (!$token) {
            return [];
        }

        try {
            $url = "{$this->baseUrl}/admin/realms/{$this->realm}/roles";
            $response = Http::withToken($token)->timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('KeycloakAdminService: Gagal mengambil realm roles.', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            return [];
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception getRealmRoles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil detail satu Realm Role berdasarkan nama role.
     *
     * @return array{id: string, name: string}|null
     */
    public function getRealmRoleByName(string $roleName): ?array
    {
        $token = $this->getAdminToken();
        if (!$token) {
            return null;
        }

        try {
            $url = "{$this->baseUrl}/admin/realms/{$this->realm}/roles/" . rawurlencode($roleName);
            $response = Http::withToken($token)->timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            // Jika tidak ditemukan secara direct URL encode, cari dari list roles (case-insensitive / slug fallback)
            $allRoles = $this->getRealmRoles();
            foreach ($allRoles as $role) {
                if (strcasecmp($role['name'], $roleName) === 0 ||
                    strcasecmp(str_replace(' ', '_', strtolower($role['name'])), str_replace(' ', '_', strtolower($roleName))) === 0) {
                    return $role;
                }
            }

            return null;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception getRealmRoleByName: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mengambil daftar Realm Roles yang sedang aktif dimiliki oleh seorang user di Keycloak.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function getUserRealmRoles(string $keycloakUserId): array
    {
        $token = $this->getAdminToken();
        if (!$token) {
            return [];
        }

        try {
            $url = "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/role-mappings/realm";
            $response = Http::withToken($token)->timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("KeycloakAdminService: Gagal mengambil role untuk user {$keycloakUserId}.", [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            return [];
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception getUserRealmRoles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Memasang (assign) satu atau beberapa Realm Role ke user di Keycloak.
     *
     * @param array<int, array{id: string, name: string}> $rolesPayload
     */
    public function assignRealmRolesToUser(string $keycloakUserId, array $rolesPayload): bool
    {
        $token = $this->getAdminToken();
        if (!$token || empty($rolesPayload)) {
            return false;
        }

        try {
            $url = "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/role-mappings/realm";
            $response = Http::withToken($token)
                ->timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $rolesPayload);

            return $response->successful() || $response->status() === 204;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception assignRealmRolesToUser: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencabut (unassign / delete) Realm Role dari user di Keycloak.
     *
     * @param array<int, array{id: string, name: string}> $rolesPayload
     */
    public function removeRealmRolesFromUser(string $keycloakUserId, array $rolesPayload): bool
    {
        $token = $this->getAdminToken();
        if (!$token || empty($rolesPayload)) {
            return false;
        }

        try {
            $url = "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/role-mappings/realm";
            $response = Http::withToken($token)
                ->timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->send('DELETE', $url, [
                    'json' => $rolesPayload,
                ]);

            return $response->successful() || $response->status() === 204;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception removeRealmRolesFromUser: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * SINKRONISASI ROLE UTAMA:
     * Mengubah role user di Keycloak dengan aman:
     * 1. Mencari role baru di Keycloak.
     * 2. Mencari role lama milik user di Keycloak yang bertabrakan (hanya mencabut role aplikasi, TIDAK mencabut role internal sistem Keycloak).
     * 3. Mencabut role aplikasi lama dan memasang role baru.
     *
     * @param string $keycloakUserId UUID user di Keycloak
     * @param string $newRoleName Nama role baru (misal: 'Admin', 'Moderator', 'Anggota', dll.)
     * @return array{success: bool, message: string, keycloak_synced: bool}
     */
    public function syncUserRole(string $keycloakUserId, string $newRoleName): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'keycloak_synced' => false,
                'message' => 'Konfigurasi Keycloak belum lengkap di .env.',
            ];
        }

        // 1. Cari representasi role target di Keycloak
        $targetRole = $this->getRealmRoleByName($newRoleName);
        if (!$targetRole) {
            return [
                'success' => false,
                'keycloak_synced' => false,
                'message' => "Role '{$newRoleName}' belum dibuat di Realm Roles Keycloak.",
            ];
        }

        // 2. Ambil role user yang sedang terpasang di Keycloak saat ini
        $currentUserRoles = $this->getUserRealmRoles($keycloakUserId);

        // Standar daftar role aplikasi yang dikelola (agar tidak menghapus default roles Keycloak seperti 'default-roles-...', 'offline_access')
        $managedRoleNames = array_map('strtolower', User::ALL_ROLES);
        // Tambahkan variasi slug (e.g. 'super_admin', 'admin_pusat')
        foreach (User::ALL_ROLES as $r) {
            $managedRoleNames[] = str_replace(' ', '_', strtolower($r));
        }

        $rolesToRemove = [];
        foreach ($currentUserRoles as $role) {
            $rNameLower = strtolower($role['name'] ?? '');
            // Jika role ini termasuk role aplikasi dan BUKAN role target yang baru
            if (in_array($rNameLower, $managedRoleNames, true) && strcasecmp($role['name'], $targetRole['name']) !== 0) {
                $rolesToRemove[] = [
                    'id' => $role['id'],
                    'name' => $role['name'],
                ];
            }
        }

        // 3. Hapus role aplikasi lama jika ada
        if (!empty($rolesToRemove)) {
            $this->removeRealmRolesFromUser($keycloakUserId, $rolesToRemove);
        }

        // 4. Pasang role target yang baru ke user
        $assigned = $this->assignRealmRolesToUser($keycloakUserId, [
            [
                'id' => $targetRole['id'],
                'name' => $targetRole['name'],
            ],
        ]);

        if ($assigned) {
            return [
                'success' => true,
                'keycloak_synced' => true,
                'message' => "Role berhasil disinkronkan ke Keycloak sebagai '{$targetRole['name']}'.",
            ];
        }

        return [
            'success' => false,
            'keycloak_synced' => false,
            'message' => 'Gagal memasangkan role baru ke user di Keycloak.',
        ];
    }
}

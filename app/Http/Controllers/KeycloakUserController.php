<?php

namespace App\Http\Controllers;

use App\Exceptions\KeycloakDirectoryException;
use App\Models\User;
use App\Services\KeycloakUserAccess;
use App\Services\KeycloakUserDirectory;
use App\Services\KeycloakUserSyncService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class KeycloakUserController extends Controller
{
    public function __construct(private KeycloakUserDirectory $directory, private KeycloakUserAccess $access) {}

    public function index(Request $request)
    {
        $validated = $request->validate(['q' => ['nullable', 'string', 'max:255'], 'page' => ['nullable', 'integer', 'min:1', 'max:100000'], 'role' => ['nullable', Rule::in(User::ALL_ROLES)], 'sort' => ['nullable', Rule::in(['default', 'name_asc', 'name_desc', 'role'])]]);
        $page = (int) ($validated['page'] ?? 1);
        $sort = $validated['sort'] ?? 'default';
        $roleFilter = $validated['role'] ?? null;
        $globalSort = $sort !== 'default' || $roleFilter;
        $error = null;
        $rows = [];
        $total = 0;
        try {
            $search = trim($validated['q'] ?? '');
            $result = $globalSort ? ['users' => $this->directory->allUsers($search), 'total' => 0]
                : $this->directory->users($search, $page);
            $total = $result['total'];
            $locals = User::whereIn('keycloak_id', array_column($result['users'], 'id'))->get()->keyBy('keycloak_id');
            foreach ($result['users'] as $remote) {
                $role = $this->directory->role($remote['id']);
                $rows[] = [
                    'id' => $remote['id'], 'username' => $remote['username'] ?? '',
                    'name' => trim(($remote['firstName'] ?? '').' '.($remote['lastName'] ?? '')) ?: ($remote['username'] ?? '-'),
                    'email' => $remote['email'] ?? '-', 'role' => $role,
                    'enabled' => $remote['enabled'] ?? false,
                    'foto_profil' => $locals->get($remote['id'])?->foto_profil,
                    'can_edit' => $this->access->canEdit($request->user(), $remote, $role, $locals->get($remote['id'])),
                ];
            }
            if ($globalSort) {
                $collection = collect($rows);
                if ($roleFilter) {
                    $collection = $collection->where('role', $roleFilter);
                }
                $hierarchy = array_flip([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PUSAT, User::ROLE_ADMIN,
                    User::ROLE_ANALIS, User::ROLE_MODERATOR, User::ROLE_ANGGOTA]);
                $collection = $collection->sort(function ($a, $b) use ($sort, $hierarchy) {
                    if ($sort === 'role') {
                        $rank = ($hierarchy[$a['role']] ?? 99) <=> ($hierarchy[$b['role']] ?? 99);
                        if ($rank) {
                            return $rank;
                        }
                    }
                    $name = strnatcasecmp($a['name'], $b['name']);
                    if ($name) {
                        return $sort === 'name_desc' ? -$name : $name;
                    }

                    return strcmp($a['id'], $b['id']);
                })->values();
                $total = $collection->count();
                $rows = $collection->slice(($page - 1) * 10, 10)->values()->all();
            }
        } catch (KeycloakDirectoryException $exception) {
            $error = 'Daftar pengguna Keycloak belum dapat dimuat. Periksa koneksi dan izin akun layanan, lalu coba lagi.';
            $rows = [];
            $total = 0;
        }
        $users = new LengthAwarePaginator($rows, $total, 10, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return response()->view('admin.users.keycloak-index', compact('users', 'error'), $error ? 503 : 200);
    }

    private function editable(Request $request, string $keycloakId): array
    {
        $remote = $this->directory->user($keycloakId);
        $role = $this->directory->role($keycloakId);
        $local = User::where('keycloak_id', $keycloakId)->first();
        abort_unless($this->access->canEdit($request->user(), $remote, $role, $local), 403, 'Anda tidak dapat mengedit akun sendiri atau pengguna pada tingkat yang sama atau lebih tinggi.');

        return [$remote, $local];
    }

    public function edit(Request $request, string $keycloakId)
    {
        try {
            [$remote, $local] = $this->editable($request, $keycloakId);
        } catch (KeycloakDirectoryException $exception) {
            abort($exception->httpStatus === 404 ? 404 : 503, 'Data pengguna Keycloak belum dapat dimuat.');
        }

        $currentRole = $this->directory->role($keycloakId);
        $allowedRoles = $this->access->assignableRoles($request->user());

        return view('admin.users.keycloak-edit', compact('remote', 'local', 'currentRole', 'allowedRoles'));
    }

    public function update(Request $request, string $keycloakId, KeycloakUserSyncService $syncs)
    {
        try {
            [$remote, $local] = $this->editable($request, $keycloakId);
        } catch (KeycloakDirectoryException $exception) {
            abort($exception->httpStatus === 404 ? 404 : 503, 'Data pengguna Keycloak belum dapat dimuat.');
        }
        $data = $request->validate([
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($local?->id)],
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9._@-]+$/', Rule::unique('users')->ignore($local?->id)],
            'role' => ['sometimes', 'required', Rule::in($this->access->assignableRoles($request->user()))], 'enabled' => ['prohibited'], 'password' => ['prohibited'],
        ]);
        $fields = ['username' => trim($data['username']), 'firstName' => trim($data['firstName']), 'lastName' => trim($data['lastName'] ?? ''), 'email' => strtolower(trim($data['email']))];
        if (isset($data['role'])) {
            $fields['role'] = $data['role'];
        }
        try {
            $syncs->update($remote, $fields, $request->user());
        } catch (KeycloakDirectoryException $exception) {
            $message = match ($exception->httpStatus) {
                409 => 'Email atau username sudah digunakan pengguna lain di Keycloak.',
                400 => 'Keycloak menolak data. Periksa format dan kebijakan profil pengguna.',
                default => 'Pembaruan Keycloak belum dapat dikonfirmasi. Silakan hubungi administrator untuk memeriksa catatan sinkronisasi.',
            };

            return back()->withErrors(['keycloak' => $message])->withInput();
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['keycloak' => 'Pembaruan belum selesai disinkronkan. Hubungi administrator untuk memeriksa catatan sinkronisasi.'])->withInput();
        }

        return redirect()->route('admin.users.index')->with('success', 'Username, nama, dan email pengguna berhasil diperbarui di Keycloak dan tersinkron ke akun MojoPedia yang terhubung.');
    }

    public function updatePassword(Request $request, string $keycloakId, KeycloakUserSyncService $syncs)
    {
        try {
            $this->editable($request, $keycloakId);
        } catch (KeycloakDirectoryException $exception) {
            abort($exception->httpStatus === 404 ? 404 : 503, 'Data pengguna Keycloak belum dapat dimuat.');
        }
        $data = $request->validateWithBag('passwordUpdate', [
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed',
                function ($attribute, $value, $fail) {
                    if (strlen($value) > 72) {
                        $fail('Password maksimal 72 byte.');
                    }
                }],
        ]);
        try {
            $syncs->updatePassword($keycloakId, $data['password'], $request->user());
        } catch (KeycloakDirectoryException $exception) {
            return back()->withErrors(['password' => $exception->httpStatus === 400
                ? 'Keycloak menolak password. Pastikan memenuhi kebijakan password realm.'
                : 'Perubahan password Keycloak belum dapat dikonfirmasi. Periksa catatan sinkronisasi sebelum mencoba lagi.'], 'passwordUpdate');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['password' => 'Perubahan password belum selesai disinkronkan. Hubungi administrator untuk memeriksa catatan sinkronisasi.'], 'passwordUpdate');
        }

        return back()->with('success', 'Password berhasil diperbarui di Keycloak dan akun MojoPedia yang terhubung. Token API dan sesi lokal lama sudah dicabut.');
    }

    public function destroy(Request $request, string $keycloakId, KeycloakUserSyncService $syncs)
    {
        $request->validate(['confirm_delete' => ['required', 'accepted']]);
        try {
            [$remote] = $this->editable($request, $keycloakId);
        } catch (KeycloakDirectoryException $exception) {
            abort($exception->httpStatus === 404 ? 404 : 503, 'Data pengguna Keycloak belum dapat dimuat.');
        }
        try {
            $syncs->deleteUser($remote, $request->user());
        } catch (KeycloakDirectoryException $exception) {
            return back()->withErrors(['keycloak' => 'Penghapusan Keycloak belum dapat dikonfirmasi. Periksa catatan sinkronisasi sebelum mencoba lagi.']);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['keycloak' => 'Penghapusan belum selesai disinkronkan. Hubungi administrator untuk memeriksa catatan sinkronisasi.']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil dihapus dari Keycloak dan akun MojoPedia yang terhubung.');
    }
}

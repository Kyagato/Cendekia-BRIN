<?php

namespace App\Services;

use App\Exceptions\KeycloakDirectoryException;
use App\Models\AuditLog;
use App\Models\KeycloakUserSync;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KeycloakUserCreationService
{
    public function __construct(private KeycloakUserDirectory $directory, private KeycloakUserAccess $access) {}

    public function create(User $actor, array $identity, #[\SensitiveParameter] string $password, string $key): KeycloakUserSync
    {
        abort_unless($actor->isSuperAdmin() || $actor->isAdminPusat(), 403);
        abort_unless(in_array($identity['role'], $this->access->assignableRoles($actor), true), 403);

        $identity['username'] = strtolower(trim($identity['username']));

        return Cache::lock('keycloak-create:'.hash('sha256', $key), 60)->block(3, function () use ($actor, $identity, $password, $key) {
            $existing = KeycloakUserSync::where('request_key', $key)->first();
            if ($existing) {
                if ($existing->actor_id !== $actor->id || $existing->status !== 'synced') {
                    throw new \RuntimeException('Permintaan sudah diproses. Periksa catatan sinkronisasi.');
                }

                return $existing;
            }
            $sync = KeycloakUserSync::create([
                'request_key' => $key, 'keycloak_user_id' => 'pending:'.$key,
                'actor_id' => $actor->id, 'operation' => 'create', 'status' => 'pending',
                'before' => [], 'after' => $identity + ['emailVerified' => false, 'enabled' => true],
                'local_password_hash' => Hash::make($password),
            ]);
            try {
                $payload = $identity;
                unset($payload['role']);
                $id = $this->directory->create($payload + ['enabled' => false, 'emailVerified' => false,
                    'credentials' => [['type' => 'password', 'value' => $password, 'temporary' => false]]]);
                $sync->update(['keycloak_user_id' => $id, 'status' => 'reconciliation_required']);
                $this->directory->setClientRole($id, $identity['role']);
                $this->directory->update($id, $payload + ['enabled' => true, 'emailVerified' => false]);
                $this->finish($sync);
            } catch (\Throwable $exception) {
                $status = str_starts_with($sync->keycloak_user_id, 'pending:')
                    ? ($exception instanceof KeycloakDirectoryException && $exception->httpStatus !== null && $exception->httpStatus < 500 ? 'remote_failed' : 'requires_review')
                    : 'reconciliation_required';
                $sync->update(['status' => $status, 'local_password_hash' => $status === 'reconciliation_required' ? $sync->local_password_hash : null]);
                $this->audit($sync, 'KEYCLOAK_USER_CREATE_FAILED', $actor);
                throw $exception;
            }
            $this->audit($sync, 'KEYCLOAK_USER_CREATE', $actor);

            return $sync;
        });
    }

    public function finish(KeycloakUserSync $sync): void
    {
        // Unknown creation outcomes must never attach an existing account by username/email.
        if (str_starts_with($sync->keycloak_user_id, 'pending:')) {
            throw new \RuntimeException('Creation requires manual review.');
        }
        $remote = $this->directory->user($sync->keycloak_user_id);
        $remote['role'] = $this->directory->role($sync->keycloak_user_id);
        if (! $this->directory->hasClientRole($sync->keycloak_user_id, $sync->after['role'])) {
            throw new \RuntimeException('Client role belum terkonfirmasi.');
        }
        foreach ($sync->after as $field => $value) {
            if ($field === 'username' && strcasecmp($remote['username'] ?? '', $value) === 0) {
                continue;
            }
            if (($remote[$field] ?? ($field === 'emailVerified' ? false : null)) !== $value) {
                throw new \RuntimeException('Keycloak belum mengonfirmasi akun dan role yang diminta.');
            }
        }
        $after = $sync->after;
        $after['username'] = $remote['username'];
        $sync->after = $after;
        DB::transaction(function () use ($sync) {
            $local = User::where('keycloak_id', $sync->keycloak_user_id)->lockForUpdate()->first();
            if (! $local) {
                if (! $sync->local_password_hash) {
                    throw new \RuntimeException('Recovery hash unavailable.');
                }
                $local = new User;
                $local->forceFill([
                    'keycloak_id' => $sync->keycloak_user_id, 'username' => $sync->after['username'],
                    'name' => trim($sync->after['firstName'].' '.($sync->after['lastName'] ?? '')),
                    'email' => $sync->after['email'], 'email_verified_at' => null,
                    'role' => $sync->after['role'], 'password' => $sync->local_password_hash,
                ])->save();
            }
            $sync->update(['status' => 'synced', 'remote_confirmed' => true, 'local_password_hash' => null]);
        });
    }

    private function audit(KeycloakUserSync $sync, string $action, ?User $actor): void
    {
        AuditLog::record($action, 'Pembuatan pengguna Keycloak (referensi '.$sync->id.').', [
            'sync_id' => $sync->id, 'keycloak_user_id' => $sync->keycloak_user_id,
            'after' => $sync->after, 'sync_status' => $sync->status,
        ], $actor);
    }
}

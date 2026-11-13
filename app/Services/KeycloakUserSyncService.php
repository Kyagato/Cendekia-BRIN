<?php

namespace App\Services;

use App\Exceptions\KeycloakDirectoryException;
use App\Models\AuditLog;
use App\Models\KeycloakUserSync;
use App\Models\User;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KeycloakUserSyncService
{
    public function __construct(private KeycloakUserDirectory $directory) {}

    public function update(array $current, array $fields, User $actor): KeycloakUserSync
    {
        return $this->locked($current['id'], fn () => $this->performUpdate($current, $fields, $actor));
    }

    private function locked(string $id, callable $callback): mixed
    {
        $key = 'keycloak-user-sync:'.hash('sha256', config('services.keycloak.base_url').'|'.config('services.keycloak.realms').'|'.$id);
        try {
            return Cache::lock($key, 45)->block(3, $callback);
        } catch (LockTimeoutException $exception) {
            throw new \RuntimeException('Pengguna sedang diperbarui. Coba lagi.');
        }
    }

    private function performUpdate(array $current, array $fields, User $actor): KeycloakUserSync
    {
        $current = $this->directory->user($current['id']);
        $requestedRole = $fields['role'] ?? null;
        unset($fields['role']);
        $before = array_intersect_key($current, array_flip(['username', 'firstName', 'lastName', 'email', 'emailVerified']));
        $after = $fields + ['emailVerified' => strcasecmp($fields['email'], $current['email'] ?? '') === 0 && ($current['emailVerified'] ?? false)];
        if ($requestedRole !== null) {
            $before['role'] = $this->directory->role($current['id']);
            $after['role'] = $requestedRole;
        }
        $sync = KeycloakUserSync::create([
            'keycloak_user_id' => $current['id'], 'actor_id' => $actor->id,
            'before' => $before, 'after' => $after, 'status' => 'pending',
        ]);
        $payload = array_intersect_key($current, array_flip(['username', 'enabled', 'attributes', 'requiredActions']));
        try {
            $identity = $after;
            unset($identity['role']);
            $this->directory->update($current['id'], array_merge($payload, $identity));
            if ($requestedRole !== null) {
                $this->directory->setRole($current['id'], $requestedRole);
            }
        } catch (KeycloakDirectoryException $exception) {
            // A timeout/server error can occur after Keycloak has committed the change.
            $sync->update(['status' => $requestedRole === null && $exception->httpStatus !== null && $exception->httpStatus < 500
                ? 'remote_failed' : 'reconciliation_required']);
            $this->audit($sync, 'KEYCLOAK_USER_UPDATE_FAILED', $actor);
            throw $exception;
        }
        $confirmed = $this->directory->user($current['id']);
        if ($requestedRole !== null) {
            $confirmed['role'] = $this->directory->role($current['id']);
        }
        foreach ($after as $key => $value) {
            if (($confirmed[$key] ?? ($key === 'emailVerified' ? false : '')) !== $value) {
                $sync->update(['status' => 'requires_review']);
                $this->audit($sync, 'KEYCLOAK_USER_SYNC_CONFLICT', $actor);
                throw new \RuntimeException('Keycloak belum mengonfirmasi semua perubahan.');
            }
        }
        try {
            $this->applyLocal($sync);
        } catch (\Throwable $exception) {
            $sync->update(['status' => 'reconciliation_required']);
            $this->audit($sync, 'KEYCLOAK_USER_SYNC_FAILED', $actor);
            throw new \RuntimeException('Data Keycloak sudah diperbarui, tetapi sinkronisasi lokal belum selesai. Referensi: '.$sync->id);
        }
        $this->audit($sync, 'KEYCLOAK_USER_UPDATE', $actor);

        return $sync;
    }

    public function updatePassword(string $id, #[\SensitiveParameter] string $password, User $actor): KeycloakUserSync
    {
        return $this->locked($id, function () use ($id, $password, $actor) {
            // Keep only an encrypted hash for recovery, never the submitted password.
            $sync = KeycloakUserSync::create([
                'keycloak_user_id' => $id, 'actor_id' => $actor->id, 'before' => [], 'after' => [],
                'operation' => 'password', 'status' => 'pending',
                'local_password_hash' => Hash::make($password),
            ]);
            try {
                $this->directory->resetPassword($id, $password);
            } catch (KeycloakDirectoryException $exception) {
                $sync->update(['status' => $exception->httpStatus !== null && $exception->httpStatus < 500
                    ? 'remote_failed' : 'requires_review', 'local_password_hash' => null]);
                $this->audit($sync, 'KEYCLOAK_PASSWORD_UPDATE_FAILED', $actor);
                throw $exception;
            }
            // A confirmed remote change can be safely finished locally without resending credentials.
            $sync->update(['remote_confirmed' => true, 'status' => 'reconciliation_required']);
            try {
                $this->applyPasswordLocal($sync);
            } catch (\Throwable $exception) {
                $this->audit($sync, 'KEYCLOAK_PASSWORD_SYNC_FAILED', $actor);
                throw new \RuntimeException('Password Keycloak sudah berubah, sinkronisasi lokal belum selesai.');
            }
            $this->audit($sync, 'KEYCLOAK_PASSWORD_UPDATE', $actor);

            return $sync;
        });
    }

    private function applyPasswordLocal(KeycloakUserSync $sync): void
    {
        DB::transaction(function () use ($sync) {
            $local = User::where('keycloak_id', $sync->keycloak_user_id)->lockForUpdate()->first();
            if ($local) {
                if (! $sync->local_password_hash) {
                    throw new \RuntimeException('Recovery hash unavailable.');
                }
                $local->forceFill(['password' => $sync->local_password_hash])->save();
                $local->tokens()->delete();
                if (config('session.driver') === 'database') {
                    DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                        ->where('user_id', $local->id)->delete();
                }
            }
            $sync->update(['status' => 'synced', 'local_password_hash' => null]);
        });
    }

    public function applyLocal(KeycloakUserSync $sync): void
    {
        DB::transaction(function () use ($sync) {
            $local = User::where('keycloak_id', $sync->keycloak_user_id)->lockForUpdate()->first();
            if ($local) {
                $name = trim(($sync->after['firstName'] ?? '').' '.($sync->after['lastName'] ?? ''));
                $local->forceFill([
                    'role' => $sync->after['role'] ?? $local->role,
                    'username' => $sync->after['username'] ?? $local->username,
                    'name' => $name ?: $local->name,
                    'email' => $sync->after['email'],
                    'email_verified_at' => ($sync->after['emailVerified'] ?? false) ? ($local->email_verified_at ?? now()) : null,
                ])->save();
            }
            $sync->update(['status' => 'synced']);
        });
    }

    public function deleteUser(array $remote, User $actor): void
    {
        $this->locked($remote['id'], function () use ($remote, $actor) {
            $sync = KeycloakUserSync::create([
                'keycloak_user_id' => $remote['id'], 'actor_id' => $actor->id,
                'before' => array_intersect_key($remote, array_flip(['username', 'firstName', 'lastName', 'email'])),
                'after' => [], 'operation' => 'delete', 'status' => 'pending',
            ]);
            try {
                $this->directory->delete($remote['id']);
            } catch (KeycloakDirectoryException $exception) {
                $sync->update(['status' => $exception->httpStatus !== null && $exception->httpStatus < 500 ? 'remote_failed' : 'reconciliation_required']);
                $this->audit($sync, 'KEYCLOAK_USER_DELETE_FAILED', $actor);
                throw $exception;
            }
            $sync->update(['remote_confirmed' => true, 'status' => 'reconciliation_required']);
            $this->applyDeleteLocal($sync);
            $this->audit($sync, 'KEYCLOAK_USER_DELETE', $actor);
        });
    }

    private function applyDeleteLocal(KeycloakUserSync $sync): void
    {
        DB::transaction(function () use ($sync) {
            $local = User::where('keycloak_id', $sync->keycloak_user_id)->lockForUpdate()->first();
            if ($local) {
                $local->tokens()->delete();
                if (config('session.driver') === 'database') {
                    DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))->where('user_id', $local->id)->delete();
                }
                $local->delete();
            }
            $sync->update(['status' => 'synced']);
        });
        app(KnowledgeService::class)->invalidateStatsCache();
    }

    public function reconcile(KeycloakUserSync $sync): bool
    {
        return $this->locked($sync->keycloak_user_id, fn () => $this->performReconcile($sync));
    }

    private function performReconcile(KeycloakUserSync $sync): bool
    {
        $sync->refresh();
        if (! in_array($sync->status, ['pending', 'reconciliation_required'], true)) {
            return $sync->status === 'synced';
        }
        if ($sync->operation === 'delete') {
            try {
                $this->directory->user($sync->keycloak_user_id);
            } catch (KeycloakDirectoryException $exception) {
                if ($exception->httpStatus !== 404) {
                    throw $exception;
                }
                $this->applyDeleteLocal($sync);
                $this->audit($sync, 'KEYCLOAK_USER_DELETE_RECONCILED', $sync->actor);

                return true;
            }
            $sync->update(['status' => 'requires_review']);

            return false;
        }
        if ($sync->operation === 'password') {
            $newer = KeycloakUserSync::where('keycloak_user_id', $sync->keycloak_user_id)
                ->where('operation', 'password')->where('id', '>', $sync->id)
                ->where('remote_confirmed', true)->exists();
            if ($newer) {
                $sync->update(['status' => 'requires_review', 'local_password_hash' => null]);
                $this->audit($sync, 'KEYCLOAK_PASSWORD_SYNC_CONFLICT', $sync->actor);

                return false;
            }
            if (! $sync->remote_confirmed) {
                $sync->update(['status' => 'requires_review', 'local_password_hash' => null]);
                $this->audit($sync, 'KEYCLOAK_PASSWORD_SYNC_CONFLICT', $sync->actor);

                return false;
            }
            $this->applyPasswordLocal($sync);
            $this->audit($sync, 'KEYCLOAK_PASSWORD_RECONCILED', $sync->actor);

            return true;
        }
        $remote = $this->directory->user($sync->keycloak_user_id);
        if (isset($sync->after['role'])) {
            $remote['role'] = $this->directory->role($sync->keycloak_user_id);
        }
        foreach ($sync->after as $key => $value) {
            if (($remote[$key] ?? ($key === 'emailVerified' ? false : '')) !== $value) {
                $sync->update(['status' => 'requires_review']);
                $this->audit($sync, 'KEYCLOAK_USER_SYNC_CONFLICT', $sync->actor);

                return false;
            }
        }
        $this->applyLocal($sync);
        $this->audit($sync, 'KEYCLOAK_USER_RECONCILED', $sync->actor);

        return true;
    }

    private function audit(KeycloakUserSync $sync, string $action, ?User $actor): void
    {
        AuditLog::record($action, 'Pembaruan identitas pengguna Keycloak (referensi '.$sync->id.').', [
            'sync_id' => $sync->id, 'keycloak_user_id' => $sync->keycloak_user_id,
            'before' => $sync->before, 'after' => $sync->after, 'sync_status' => $sync->status,
        ], $actor);
    }
}

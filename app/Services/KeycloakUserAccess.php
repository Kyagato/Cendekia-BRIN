<?php

namespace App\Services;

use App\Models\User;

class KeycloakUserAccess
{
    public function assignableRoles(User $actor): array
    {
        if ($actor->isSuperAdmin()) {
            return User::ALL_ROLES;
        }
        if ($actor->isAdminPusat()) {
            return [User::ROLE_ADMIN, User::ROLE_ANALIS, User::ROLE_MODERATOR, User::ROLE_ANGGOTA];
        }

        return $actor->isAdmin() ? [User::ROLE_ANALIS, User::ROLE_MODERATOR, User::ROLE_ANGGOTA] : [];
    }

    public function canEdit(User $actor, array $remote, string $remoteRole, ?User $local): bool
    {
        if (! $actor->isAdmin() || ! empty($remote['serviceAccountClientId']) || str_starts_with($remote['username'] ?? '', 'service-account-')) {
            return false;
        }
        if ($actor->keycloak_id === ($remote['id'] ?? null) || $actor->id === $local?->id
            || strcasecmp($actor->email, $remote['email'] ?? '') === 0) {
            return false;
        }
        $rank = fn ($role) => match ($role) {
            User::ROLE_SUPER_ADMIN => 3, User::ROLE_ADMIN_PUSAT => 2, User::ROLE_ADMIN => 1, default => 0,
        };

        return $actor->isSuperAdmin() || $rank($actor->role) > max($rank($remoteRole), $rank($local?->role));
    }
}

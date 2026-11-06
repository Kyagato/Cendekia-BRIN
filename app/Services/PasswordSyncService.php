<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordSyncService
{
    public function __construct(private KeycloakAdminService $keycloak) {}

    public function update(User $user, #[\SensitiveParameter] string $password, string $errorBag = 'default'): void
    {
        $hash = Hash::make($password);

        DB::transaction(function () use ($user, $password, $hash, $errorBag) {
            // Persist locally inside a transaction; roll back if the identity provider refuses.
            $user->password = $hash;
            $user->save();
            $user->tokens()->delete();

            if ($user->keycloak_id && !$this->keycloak->updateUserPassword($user->keycloak_id, $password)) {
                throw ValidationException::withMessages([
                    'password' => 'Kata sandi belum diubah. Pembaruan Keycloak gagal; periksa koneksi, kebijakan kata sandi, dan izin layanan Keycloak lalu coba lagi.',
                ])->errorBag($errorBag);
            }
        });
    }
}

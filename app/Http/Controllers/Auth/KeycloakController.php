<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Inertia\Inertia;
use Exception;

class KeycloakController extends Controller
{
    /**
     * Redirect user ke halaman login Keycloak SSO.
     */
    public function redirect()
    {
        // Bersihkan sesi lokal lama jika masih ada sebelum diarahkan ke Keycloak
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        $baseUrl = config('services.keycloak.base_url');
        $clientId = config('services.keycloak.client_id');

        // Tampilkan peringatan jika konfigurasi belum diisi
        if (empty($baseUrl) || empty($clientId)) {
            return response()->make('
                <div style="font-family: system-ui, -apple-system, sans-serif; padding: 50px 20px; text-align: center; max-width: 600px; margin: 0 auto;">
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 24px; border-radius: 12px;">
                        <h2 style="margin-top: 0; font-size: 20px;">Konfigurasi Keycloak Belum Diisi di .env</h2>
                        <p style="font-size: 14px; color: #7f1d1d; line-height: 1.6;">
                            Silakan buka file <code>.env</code> dan pastikan variabel berikut sudah terisi:
                        </p>
                        <pre style="background: #1e293b; color: #f8fafc; padding: 16px; border-radius: 8px; text-align: left; font-size: 13px; overflow-x: auto;">
KEYCLOAK_BASE_URL=http://localhost:8080
KEYCLOAK_REALM=Mojopedia
KEYCLOAK_CLIENT_ID=mojopedia-app
KEYCLOAK_CLIENT_SECRET=your-client-secret
KEYCLOAK_REDIRECT_URI=http://localhost:8000/auth/keycloak/callback</pre>
                    </div>
                </div>
            ', 500);
        }

        try {
            $redirectResponse = Socialite::driver('keycloak')
                ->with(['prompt' => 'login'])
                ->redirect();
            $targetUrl = $redirectResponse->getTargetUrl();

            if (request()->header('X-Inertia') || request()->wantsJson()) {
                return Inertia::location($targetUrl);
            }

            return $redirectResponse;
        } catch (Exception $e) {
            return response()->make('
                <div style="font-family: system-ui, -apple-system, sans-serif; padding: 50px 20px; text-align: center; max-width: 600px; margin: 0 auto;">
                    <div style="background: #fff1f2; border: 1px solid #fda4af; color: #9f1239; padding: 24px; border-radius: 12px;">
                        <h2 style="margin-top: 0; font-size: 20px;">Gagal Menghubungi Keycloak Server</h2>
                        <p style="font-size: 14px; line-height: 1.6;">' . e($e->getMessage()) . '</p>
                    </div>
                </div>
            ', 500);
        }
    }

    /**
     * Redirect user ke halaman registrasi Keycloak SSO.
     */
    public function register()
    {
        $baseUrl = config('services.keycloak.base_url');
        $clientId = config('services.keycloak.client_id');

        // Tampilkan peringatan jika konfigurasi belum diisi
        if (empty($baseUrl) || empty($clientId)) {
            return response()->make('
                <div style="font-family: system-ui, -apple-system, sans-serif; padding: 50px 20px; text-align: center; max-width: 600px; margin: 0 auto;">
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 24px; border-radius: 12px;">
                        <h2 style="margin-top: 0; font-size: 20px;">Konfigurasi Keycloak Belum Diisi di .env</h2>
                        <p style="font-size: 14px; color: #7f1d1d; line-height: 1.6;">
                            Silakan buka file <code>.env</code> dan pastikan variabel berikut sudah terisi:
                        </p>
                        <pre style="background: #1e293b; color: #f8fafc; padding: 16px; border-radius: 8px; text-align: left; font-size: 13px; overflow-x: auto;">
KEYCLOAK_BASE_URL=http://localhost:8080
KEYCLOAK_REALM=Mojopedia
KEYCLOAK_CLIENT_ID=mojopedia-app
KEYCLOAK_CLIENT_SECRET=your-client-secret
KEYCLOAK_REDIRECT_URI=http://localhost:8000/auth/keycloak/callback</pre>
                    </div>
                </div>
            ', 500);
        }

        try {
            $redirectResponse = Socialite::driver('keycloak')
                ->with(['prompt' => 'create'])
                ->redirect();
            $targetUrl = $redirectResponse->getTargetUrl();

            if (request()->header('X-Inertia') || request()->wantsJson()) {
                return Inertia::location($targetUrl);
            }

            return $redirectResponse;
        } catch (Exception $e) {
            return response()->make('
                <div style="font-family: system-ui, -apple-system, sans-serif; padding: 50px 20px; text-align: center; max-width: 600px; margin: 0 auto;">
                    <div style="background: #fff1f2; border: 1px solid #fda4af; color: #9f1239; padding: 24px; border-radius: 12px;">
                        <h2 style="margin-top: 0; font-size: 20px;">Gagal Menghubungi Keycloak Server</h2>
                        <p style="font-size: 14px; line-height: 1.6;">' . e($e->getMessage()) . '</p>
                    </div>
                </div>
            ', 500);
        }
    }

    /**
     * Callback setelah user berhasil login di Keycloak.
     */
    public function callback()
    {
        try {
            try {
                $keycloakUser = Socialite::driver('keycloak')->user();
            } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
                // Fallback stateless jika session state hilang karena redirect cross-port (localhost:8080 <-> localhost:8000)
                $keycloakUser = Socialite::driver('keycloak')->stateless()->user();
            }

            // Cocokkan berdasarkan keycloak_id atau email
            $user = User::where('keycloak_id', $keycloakUser->getId())
                ->orWhere('email', $keycloakUser->getEmail())
                ->first();

            // Ekstrak role dari Keycloak (realm_access.roles, roles, resource_access.*.roles)
            $rawUser = $keycloakUser->getRaw();
            $kcRoles = array_merge(
                $rawUser['realm_access']['roles'] ?? [],
                $rawUser['roles'] ?? []
            );

            // Ekstrak client roles jika role dipasang pada level client di Keycloak
            if (!empty($rawUser['resource_access']) && is_array($rawUser['resource_access'])) {
                foreach ($rawUser['resource_access'] as $clientAccess) {
                    if (!empty($clientAccess['roles']) && is_array($clientAccess['roles'])) {
                        $kcRoles = array_merge($kcRoles, $clientAccess['roles']);
                    }
                }
            }

            // Normalisasi daftar role untuk pencocokan case-insensitive
            $normalizedRoles = array_map(function ($role) {
                return strtolower(trim(str_replace(['_', '-'], ' ', $role)));
            }, $kcRoles);

            $assignedRole = null;
            if (
                in_array('super admin', $normalizedRoles, true) ||
                in_array('superadmin', $normalizedRoles, true) ||
                strtolower($keycloakUser->getEmail()) === 'superadmin@brin.go.id' ||
                ($user && strtolower($user->email) === 'superadmin@brin.go.id')
            ) {
                $assignedRole = User::ROLE_SUPER_ADMIN;
            } elseif (in_array('admin pusat', $normalizedRoles, true)) {
                $assignedRole = User::ROLE_ADMIN_PUSAT;
            } elseif (
                in_array('admin', $normalizedRoles, true) ||
                in_array('admin ippd', $normalizedRoles, true)
            ) {
                $assignedRole = User::ROLE_ADMIN;
            } elseif (in_array('moderator', $normalizedRoles, true)) {
                $assignedRole = User::ROLE_MODERATOR;
            } elseif (
                in_array('analisis pengetahuan', $normalizedRoles, true) ||
                in_array('analis pengetahuan', $normalizedRoles, true)
            ) {
                $assignedRole = User::ROLE_ANALIS;
            } elseif (
                in_array('anggota', $normalizedRoles, true) ||
                in_array('kreator pengetahuan', $normalizedRoles, true)
            ) {
                $assignedRole = User::ROLE_ANGGOTA;
            }

            // Proteksi Super Admin: Jika user di DB sudah Super Admin, cegah demosi ke Anggota karena default roles Keycloak
            if ($user && $user->isSuperAdmin() && ($assignedRole === null || $assignedRole === User::ROLE_ANGGOTA)) {
                $assignedRole = User::ROLE_SUPER_ADMIN;
            }

            if ($user) {
                $updateData = [
                    'keycloak_id' => $keycloakUser->getId(),
                    'name' => $keycloakUser->getName() ?? $user->name,
                ];
                if (!$user->email_verified_at) {
                    $updateData['email_verified_at'] = now();
                }
                if ($assignedRole) {
                    $updateData['role'] = $assignedRole;
                }
                $user->forceFill($updateData)->save();
            } else {
                $user = new User([
                    'name' => $keycloakUser->getName() ?? $keycloakUser->getNickname() ?? 'Keycloak User',
                    'email' => $keycloakUser->getEmail(),
                    'keycloak_id' => $keycloakUser->getId(),
                    'role' => $assignedRole ?? User::ROLE_ANGGOTA,
                ]);
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect('/');
        } catch (Exception $e) {
            // Jika user sebenarnya sudah berhasil login
            if (Auth::check()) {
                return redirect('/');
            }

            $errorMessage = $e->getMessage();
            if (empty($errorMessage)) {
                $errorMessage = 'Sesi validasi telah selesai atau halaman dimuat ulang. Anda sudah terautentikasi.';
            }

            \Illuminate\Support\Facades\Log::error('Keycloak Callback Error: ' . get_class($e) . ' - ' . $errorMessage);

            return response()->make('
                <div style="font-family: system-ui, -apple-system, sans-serif; padding: 50px 20px; text-align: center; max-width: 600px; margin: 0 auto;">
                    <div style="background: #fff1f2; border: 1px solid #fda4af; color: #9f1239; padding: 24px; border-radius: 12px;">
                        <h2 style="margin-top: 0; font-size: 20px;">Gagal Autentikasi Keycloak</h2>
                        <p style="font-size: 14px; line-height: 1.6;">' . e($errorMessage) . '</p>
                        <a href="/" style="display: inline-block; margin-top: 12px; padding: 8px 16px; background: #e11d48; color: #fff; text-decoration: none; border-radius: 6px;">Kembali ke Beranda</a>
                    </div>
                </div>
            ', 500);
        }
    }

    /**
     * Logout dari Laravel dan Keycloak sekaligus.
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        try {
            $clientId = config('services.keycloak.client_id');
            $keycloakLogoutUrl = Socialite::driver('keycloak')->getLogoutUrl(url('/'), $clientId);
            return redirect($keycloakLogoutUrl);
        } catch (Exception $e) {
            return redirect('/');
        }
    }
}

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
            $redirectResponse = Socialite::driver('keycloak')->redirect();
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
            $keycloakUser = Socialite::driver('keycloak')->user();

            // Cocokkan berdasarkan keycloak_id atau email
            $user = User::where('keycloak_id', $keycloakUser->getId())
                ->orWhere('email', $keycloakUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'keycloak_id' => $keycloakUser->getId(),
                    'name' => $keycloakUser->getName() ?? $user->name,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $keycloakUser->getName() ?? $keycloakUser->getNickname() ?? 'Keycloak User',
                    'email' => $keycloakUser->getEmail(),
                    'keycloak_id' => $keycloakUser->getId(),
                    'role' => 'Anggota',
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect('/');
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Keycloak Callback Error: ' . $e->getMessage());
            return response()->make('
                <div style="font-family: system-ui, -apple-system, sans-serif; padding: 50px 20px; text-align: center; max-width: 600px; margin: 0 auto;">
                    <div style="background: #fff1f2; border: 1px solid #fda4af; color: #9f1239; padding: 24px; border-radius: 12px;">
                        <h2 style="margin-top: 0; font-size: 20px;">Gagal Autentikasi Keycloak</h2>
                        <p style="font-size: 14px; line-height: 1.6;">' . e($e->getMessage()) . '</p>
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

<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Keycloak\KeycloakExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Keycloak SSO Socialite Provider
        Event::listen(
            SocialiteWasCalled::class,
            KeycloakExtendSocialite::class.'@handle'
        );

        // Audit Logging untuk Aktivitas Autentikasi
        Event::listen(Login::class, function (Login $event) {
            AuditLog::record(
                'LOGIN',
                "Pengguna '{$event->user->name}' ({$event->user->email}) berhasil login ke sistem.",
                ['guard' => $event->guard, 'role' => $event->user->role],
                $event->user
            );
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLog::record(
                    'LOGOUT',
                    "Pengguna '{$event->user->name}' ({$event->user->email}) telah keluar (logout) dari sistem.",
                    ['guard' => $event->guard],
                    $event->user
                );
            }
        });

        Event::listen(Failed::class, function (Failed $event) {
            $email = $event->credentials['email'] ?? 'unknown';
            AuditLog::record(
                'LOGIN_FAILED',
                "Percobaan login gagal untuk akun email: '{$email}'.",
                ['email' => $email],
                $event->user
            );
        });
    }
}

<?php

namespace App\Providers;

use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Keycloak\KeycloakExtendSocialite;

class AppServiceProvider extends ServiceProvider
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
        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\Keycloak\KeycloakExtendSocialite::class.'@handle'
        );

        // Audit Logging untuk Aktivitas Autentikasi
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            \App\Models\AuditLog::record(
                'LOGIN',
                "Pengguna '{$event->user->name}' ({$event->user->email}) berhasil login ke sistem.",
                ['guard' => $event->guard, 'role' => $event->user->role],
                $event->user
            );
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                \App\Models\AuditLog::record(
                    'LOGOUT',
                    "Pengguna '{$event->user->name}' ({$event->user->email}) telah keluar (logout) dari sistem.",
                    ['guard' => $event->guard],
                    $event->user
                );
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            $email = $event->credentials['email'] ?? 'unknown';
            \App\Models\AuditLog::record(
                'LOGIN_FAILED',
                "Percobaan login gagal untuk akun email: '{$email}'.",
                ['email' => $email],
                $event->user
            );
        });

        // Rate Limiter untuk Login API (maksimal 5 percobaan per menit per email/IP)
        RateLimiter::for('api-login', function (Request $request) {
            $key = (string) $request->input('email') . '|' . $request->ip();
            return Limit::perMinute(5)->by($key)->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak percobaan login. Silakan tunggu 1 menit sebelum mencoba kembali.',
                ], 429);
            });
        });

        // Rate Limiter untuk Pencarian & Autocomplete (maksimal 30 request per menit per IP)
        RateLimiter::for('search-limit', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip())->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak permintaan pencarian. Silakan perlambat jeda pencarian Anda (Maksimal 30 request/menit).',
                ], 429);
            });
        });

        // Rate Limiter untuk Permintaan OTP Lupa Password (maksimal 3 percobaan per menit per email/IP)
        RateLimiter::for('auth-otp', function (Request $request) {
            $key = (string) $request->input('email', $request->ip());
            return Limit::perMinute(3)->by($key)->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terlalu banyak permintaan pengiriman kode OTP. Silakan tunggu 1 menit.',
                ], 429);
            });
        });

        // Rate Limiter Global untuk API Publik (maksimal 60 request per menit per IP/Token)
        RateLimiter::for('api-public', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return Limit::perMinute(60)->by($key)->response(function () {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Batas kuota akses API terlampaui (60 request/menit). Silakan tunggu sejenak.',
                ], 429);
            });
        });

        // ============================================================
        // GATES — Otorisasi Berbasis Role untuk MojoPedia
        // ============================================================

        // ----- Gate: Akses Panel Admin -----
        // Super Admin & Admin Pusat bisa mengakses seluruh panel admin.
        Gate::define('access-admin-panel', function (User $user) {
            return in_array($user->role, ['Super Admin', 'Admin Pusat']);
        });

        // ----- Gate: Mengelola User & Role -----
        // Hanya Super Admin & Admin Pusat yang boleh CRUD user dan assign role.
        Gate::define('manage-users', function (User $user) {
            return in_array($user->role, ['Super Admin', 'Admin Pusat']);
        });

        // ----- Gate: Mengelola Konfigurasi Sistem -----
        // Super Admin, Admin Pusat, dan Admin.
        Gate::define('manage-settings', function (User $user) {
            return in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin']);
        });

        // ----- Gate: Mengelola Kategori & FAQ -----
        // Admin bisa CRUD kategori dan FAQ.
        Gate::define('manage-categories', function (User $user) {
            return in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin']);
        });

        // ----- Gate: Membuat Konten/Pengetahuan -----
        // Anggota, Kreator Pengetahuan, Moderator, dan Admin bisa MEMBUAT konten baru.
        Gate::define('create-knowledge', function (User $user) {
            return in_array($user->role, [
                'Super Admin', 'Admin Pusat', 'Admin', 'Anggota', 'Kreator Pengetahuan', 'Moderator',
            ]);
        });

        // ----- Gate: Mengedit Konten Sendiri -----
        // Anggota, Kreator, Analis & Moderator hanya bisa edit konten miliknya. Admin bisa edit semua.
        Gate::define('edit-knowledge', function (User $user, Knowledge $knowledge) {
            if ($user->isAdmin()) {
                return true;
            }
            // User hanya bisa edit konten miliknya sendiri
            if (in_array($user->role, ['Anggota', 'Kreator Pengetahuan', 'Moderator', 'Analisis Pengetahuan', 'Analis Pengetahuan']) && $knowledge->user_id === $user->id) {
                return true;
            }
            return false;
        });

        // ----- Gate: Menghapus Konten -----
        // Anggota, Kreator, Analis & Moderator hanya bisa hapus konten miliknya. Admin bisa hapus semua.
        Gate::define('delete-knowledge', function (User $user, Knowledge $knowledge) {
            if ($user->isAdmin()) {
                return true;
            }
            if (in_array($user->role, ['Anggota', 'Kreator Pengetahuan', 'Moderator', 'Analisis Pengetahuan', 'Analis Pengetahuan']) && $knowledge->user_id === $user->id) {
                return true;
            }
            return false;
        });



        // ----- Gate: Validasi/Approve Konten -----
        // Hanya Analisis Pengetahuan dan Admin yang bisa approve/reject.
        Gate::define('validate-knowledge', function (User $user) {
            return in_array($user->role, [
                'Super Admin', 'Admin Pusat', 'Admin', 'Analisis Pengetahuan',
            ]);
        });

        // ----- Gate: Mengelola Forum Diskusi -----
        // Moderator khusus mengelola forum. Seluruh Admin juga bisa.
        Gate::define('manage-forum', function (User $user) {
            return $user->isAdmin() || $user->isModerator();
        });

        // ----- Gate: Melihat Laporan & Analitik -----
        // Admin dan Analisis Pengetahuan.
        Gate::define('view-reports', function (User $user) {
            return in_array($user->role, [
                'Super Admin', 'Admin Pusat', 'Admin', 'Analisis Pengetahuan',
            ]);
        });

        // ----- Gate: Aksi Umum untuk Anggota (Profil, Favorit, Komentar) -----
        // Semua user yang login bisa melakukan ini.
        Gate::define('member-actions', function (User $user) {
            return $user->role !== 'Guest';
        });

        // Keycloak SSO Socialite Provider
        Event::listen(SocialiteWasCalled::class, KeycloakExtendSocialite::class);
    }
}

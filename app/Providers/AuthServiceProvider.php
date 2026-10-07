<?php

namespace App\Providers;

use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
        // ============================================================
        // GATES — Otorisasi Berbasis Role untuk MojoPedia (Tersentralisasi)
        // ============================================================

        // 1. Akses Panel Admin
        Gate::define('access-admin-panel', fn (User $user) => $user->isSuperAdmin() || $user->isAdminPusat());

        // 2. Mengelola User & Role
        Gate::define('manage-users', fn (User $user) => $user->isSuperAdmin() || $user->isAdminPusat());

        // 3. Mengelola Konfigurasi Sistem
        Gate::define('manage-settings', fn (User $user) => $user->isAdmin());

        // 4. Mengelola Kategori & FAQ
        Gate::define('manage-categories', fn (User $user) => $user->isAdmin());

        // 5. Membuat Konten Baru
        Gate::define('create-knowledge', fn (User $user) => $user->isAdmin() || $user->isMember() || $user->isModerator());

        // 6. Mengedit Konten (Admin bisa edit semua, author mengedit milik sendiri)
        Gate::define('edit-knowledge', function (User $user, Knowledge $knowledge) {
            if ($user->isAdmin()) {
                return true;
            }
            return ($user->isMember() || $user->isModerator() || $user->isAnalyst()) && $knowledge->user_id === $user->id;
        });

        // 7. Menghapus Konten (Admin bisa hapus semua, author menghapus milik sendiri)
        Gate::define('delete-knowledge', function (User $user, Knowledge $knowledge) {
            if ($user->isAdmin()) {
                return true;
            }
            return ($user->isMember() || $user->isModerator() || $user->isAnalyst()) && $knowledge->user_id === $user->id;
        });

        // 8. Validasi / Verifikasi Pengetahuan
        Gate::define('validate-knowledge', fn (User $user) => $user->isAdmin() || $user->isAnalyst());

        // 9. Mengelola Forum Diskusi
        Gate::define('manage-forum', fn (User $user) => $user->isAdmin() || $user->isModerator());

        // 10. Melihat Laporan & Analitik
        Gate::define('view-reports', fn (User $user) => $user->isAdmin() || $user->isAnalyst());

        // 11. Aksi Interaktif Member (Profil, Favorit, Komentar)
        Gate::define('member-actions', fn (User $user) => $user->role !== 'Guest');
    }
}

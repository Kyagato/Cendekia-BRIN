<?php

namespace App\Providers;

use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // ============================================================
        // GATES — Otorisasi Berbasis Role untuk Cendekia BRIN
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
        // Super Admin, Admin Pusat, dan Admin IPPD (untuk instansinya).
        Gate::define('manage-settings', function (User $user) {
            return in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD']);
        });

        // ----- Gate: Mengelola Kategori & FAQ -----
        // Admin bisa CRUD kategori dan FAQ.
        Gate::define('manage-categories', function (User $user) {
            return in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD']);
        });

        // ----- Gate: Membuat Konten/Pengetahuan -----
        // Hanya Kreator Pengetahuan dan Admin yang bisa MEMBUAT konten baru.
        Gate::define('create-knowledge', function (User $user) {
            return in_array($user->role, [
                'Super Admin', 'Admin Pusat', 'Admin IPPD', 'Kreator Pengetahuan',
            ]);
        });

        // ----- Gate: Mengedit Konten Sendiri -----
        // Kreator hanya bisa edit konten miliknya. Admin bisa edit semua.
        Gate::define('edit-knowledge', function (User $user, Knowledge $knowledge) {
            if (in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD'])) {
                return true;
            }
            // Kreator Pengetahuan hanya bisa edit miliknya sendiri
            if ($user->role === 'Kreator Pengetahuan' && $knowledge->user_id === $user->id) {
                return true;
            }
            return false;
        });

        // ----- Gate: Menghapus Konten -----
        // Kreator hanya bisa hapus konten miliknya. Admin bisa hapus semua.
        Gate::define('delete-knowledge', function (User $user, Knowledge $knowledge) {
            if (in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD'])) {
                return true;
            }
            if ($user->role === 'Kreator Pengetahuan' && $knowledge->user_id === $user->id) {
                return true;
            }
            return false;
        });

        // ----- Gate: Validasi/Approve Konten -----
        // Hanya Analisis Pengetahuan dan Admin yang bisa approve/reject.
        Gate::define('validate-knowledge', function (User $user) {
            return in_array($user->role, [
                'Super Admin', 'Admin Pusat', 'Admin IPPD', 'Analisis Pengetahuan',
            ]);
        });

        // ----- Gate: Mengelola Forum Diskusi -----
        // Moderator khusus mengelola forum. Admin juga bisa.
        Gate::define('manage-forum', function (User $user) {
            return in_array($user->role, [
                'Super Admin', 'Admin Pusat', 'Moderator',
            ]);
        });

        // ----- Gate: Melihat Laporan & Analitik -----
        // Admin dan Analisis Pengetahuan.
        Gate::define('view-reports', function (User $user) {
            return in_array($user->role, [
                'Super Admin', 'Admin Pusat', 'Admin IPPD', 'Analisis Pengetahuan',
            ]);
        });

        // ----- Gate: Aksi Umum untuk Anggota (Profil, Favorit, Komentar) -----
        // Semua user yang login bisa melakukan ini.
        Gate::define('member-actions', function (User $user) {
            return $user->role !== 'Guest';
        });
    }
}

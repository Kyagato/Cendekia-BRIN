<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// =================================================================
// PUBLIC ROUTES — Bisa diakses tanpa login (termasuk Guest)
// =================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/kategori', [HomeController::class, 'category'])->name('category.index');
Route::get('/kategori/{id}', [HomeController::class, 'categoryShow'])->name('category.show');
Route::get('/forum', [HomeController::class, 'forum'])->name('forum.index');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

// Search API (publik) & Halaman Pencarian Utama
Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'apiSearch'])->name('search.api');
Route::get('/cari', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');

// =================================================================
// AUTHENTICATED ROUTES — Semua user yang sudah login
// =================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ----- Dashboard Redirect -----
    Route::get('/dashboard', function () {
        return redirect()->route('knowledge.index');
    })->name('dashboard');

    // ----- Profil (semua user login) -----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ----- Dark Mode Toggle -----
    Route::post('/toggle-dark-mode', [HomeController::class, 'toggleDarkMode'])->name('toggle.darkmode');

    // =============================================================
    // ROLE: KREATOR PENGETAHUAN + ADMIN
    // Membuat, mengedit, dan menghapus konten pengetahuan
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD,Kreator Pengetahuan'])->group(function () {
        Route::get('/knowledge/create', [KnowledgeController::class, 'create'])->name('knowledge.create');
        Route::post('/knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
        Route::get('/knowledge/{knowledge}/edit', [KnowledgeController::class, 'edit'])->name('knowledge.edit');
        Route::put('/knowledge/{knowledge}', [KnowledgeController::class, 'update'])->name('knowledge.update');
        Route::delete('/knowledge/{knowledge}', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');
    });

    // =============================================================
    // ROLE: ANGGOTA — Bisa melihat repositori (read-only)
    // Semua user login minimal bisa melihat daftar knowledge
    // =============================================================
    Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
    Route::get('/knowledge/{knowledge}', [KnowledgeController::class, 'show'])->name('knowledge.show');

    // =============================================================
    // ROLE: ANALISIS PENGETAHUAN + ADMIN
    // Validasi, approve, atau reject konten
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD,Analisis Pengetahuan'])->group(function () {
        Route::get('/validasi', function () {
            return view('dashboard'); // TODO: Buat halaman validasi konten
        })->name('validasi.index');
        Route::patch('/validasi/{knowledge}/approve', function () {
            // TODO: Logika approve konten
        })->name('validasi.approve');
        Route::patch('/validasi/{knowledge}/reject', function () {
            // TODO: Logika reject konten
        })->name('validasi.reject');
    });

    // =============================================================
    // ROLE: ANGGOTA & SEMUA MEMBER
    // Forum Diskusi
    // =============================================================
    Route::get('/forum/create', [App\Http\Controllers\ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [App\Http\Controllers\ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{thread}', [App\Http\Controllers\ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{thread}/reply', [App\Http\Controllers\ForumController::class, 'storeReply'])->name('forum.reply');

    // =============================================================
    // ROLE: MODERATOR + ADMIN
    // Mengelola forum diskusi
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Moderator'])->group(function () {
        Route::get('/forum/manage', function () {
            return view('dashboard'); // TODO: Buat halaman manajemen forum
        })->name('forum.manage');
        Route::delete('/forum/{thread}', [App\Http\Controllers\ForumController::class, 'destroy'])->name('forum.destroy');
        Route::delete('/forum/reply/{reply}', [App\Http\Controllers\ForumController::class, 'destroyReply'])->name('forum.reply.destroy');
        Route::patch('/forum/{thread}/pin', [App\Http\Controllers\ForumController::class, 'pin'])->name('forum.pin');
        Route::patch('/forum/{thread}/lock', [App\Http\Controllers\ForumController::class, 'lock'])->name('forum.lock');
    });

    // =============================================================
    // ROLE: ADMIN IPPD + ADMIN PUSAT + SUPER ADMIN
    // Mengelola kategori, FAQ, laporan, dan pengaturan instansi
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD'])->group(function () {
        Route::get('/admin/kategori', function () {
            return view('dashboard'); // TODO: CRUD Kategori
        })->name('admin.kategori');
        Route::get('/admin/faq', function () {
            return view('dashboard'); // TODO: CRUD FAQ
        })->name('admin.faq');
        Route::get('/admin/laporan', function () {
            return view('dashboard'); // TODO: Halaman laporan & analitik
        })->name('admin.laporan');
    });

    // =============================================================
    // ROLE: SUPER ADMIN + ADMIN PUSAT ONLY
    // Panel administrator tertinggi — kelola user, role, konfigurasi
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat'])->group(function () {
        Route::get('/admin/users', function () {
            return view('dashboard'); // TODO: CRUD User
        })->name('admin.users');
        Route::get('/admin/roles', function () {
            return view('dashboard'); // TODO: Manajemen Role
        })->name('admin.roles');
        Route::get('/admin/settings', function () {
            return view('dashboard'); // TODO: Konfigurasi Aplikasi
        })->name('admin.settings');
    });
});

require __DIR__.'/auth.php';

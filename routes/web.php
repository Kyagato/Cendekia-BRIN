<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\KnowledgeValidationController;
use App\Http\Controllers\ProfileController;
use App\Models\Knowledge;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// =================================================================
// PUBLIC ROUTES — Bisa diakses tanpa login (termasuk Guest)
// =================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/kategori', [HomeController::class, 'category'])->name('category.index');
Route::get('/kategori/{id}', [HomeController::class, 'categoryShow'])->name('category.show');
Route::get('/forum', [HomeController::class, 'forum'])->name('forum.index');
Route::get('/forum/{thread}', [App\Http\Controllers\ForumController::class, 'show'])->name('forum.show')->whereNumber('thread');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

// Search API (publik) & Halaman Pencarian Utama
Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'apiSearch'])
    ->name('search.api')
    ->middleware('throttle:search-limit');
Route::get('/api/search/autocomplete', [App\Http\Controllers\SearchController::class, 'autocomplete'])
    ->name('search.autocomplete')
    ->middleware('throttle:search-limit');
Route::get('/cari', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');

// Detail Pengetahuan (Layout Publik — untuk Beranda & Kategori)
Route::get('/knowledge/{id}', [HomeController::class, 'knowledgeShow'])->name('knowledge.show')->whereNumber('id');

// Profil Publik Pengguna (POV Pengunjung & User Lain)
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');

// =================================================================
// AUTHENTICATED ROUTES — Semua user yang sudah login
// =================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ----- Dashboard Redirect -----
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (!$user) return redirect('/login');

        if ($user->isAdmin()) {
            return redirect()->route('admin.statistik');
        }

        if ($user->isAnalyst() || $user->isMember()) {
            return redirect()->route('knowledge.index');
        } elseif ($user->isModerator()) {
            return redirect()->route('moderator.forum.approval');
        }

        return redirect()->route('knowledge.index');
    })->name('dashboard');


    // ----- Profil (semua user login) -----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ----- Dark Mode Toggle -----
    Route::post('/toggle-dark-mode', [HomeController::class, 'toggleDarkMode'])->name('toggle.darkmode');

    // ----- Bookmark / Favorit Pengetahuan -----
    Route::post('/knowledge/{knowledge}/bookmark', [\App\Http\Controllers\BookmarkController::class, 'toggle'])->name('knowledge.bookmark');
    Route::get('/bookmarks', [\App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');

    // ----- Like & Komentar Pengetahuan -----
    Route::post('/knowledge/{knowledge}/like', [\App\Http\Controllers\KnowledgeInteractionController::class, 'toggleLike'])->name('knowledge.like');
    Route::post('/knowledge/{knowledge}/comment', [\App\Http\Controllers\KnowledgeInteractionController::class, 'storeComment'])->name('knowledge.comment');
    Route::delete('/knowledge/comment/{comment}', [\App\Http\Controllers\KnowledgeInteractionController::class, 'destroyComment'])->name('knowledge.comment.destroy');

    // =============================================================
    // ROLE: MANAJEMEN KONTEN PENGETAHUAN
    // Super Admin, Admin Pusat, Admin, Analisis Pengetahuan, Kreator Pengetahuan
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin,Analisis Pengetahuan,Analis Pengetahuan,Anggota,Kreator Pengetahuan,Moderator'])->group(function () {
        Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
        Route::get('/knowledge/create', [KnowledgeController::class, 'create'])->name('knowledge.create');
        Route::post('/knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
        Route::get('/knowledge/trash', [KnowledgeController::class, 'trash'])->name('knowledge.trash');
        Route::post('/knowledge/{id}/restore', [KnowledgeController::class, 'restore'])->name('knowledge.restore');
        Route::delete('/knowledge/{id}/force-delete', [KnowledgeController::class, 'forceDelete'])->name('knowledge.forceDelete');
        Route::get('/knowledge/{knowledge}/edit', [KnowledgeController::class, 'edit'])->name('knowledge.edit');
        Route::put('/knowledge/{knowledge}', [KnowledgeController::class, 'update'])->name('knowledge.update');
        Route::delete('/knowledge/{knowledge}', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');
    });



    // Detail Knowledge (Dashboard Admin Preview — layout admin)
    Route::get('/dashboard/knowledge/{id}', [KnowledgeController::class, 'show'])->name('admin.knowledge.show');

    // =============================================================
    // ROLE: ANALISIS PENGETAHUAN + ADMIN
    // Validasi, approve, atau reject konten
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin,Analisis Pengetahuan,Analis Pengetahuan'])->group(function () {
        Route::get('/validasi', [KnowledgeValidationController::class, 'index'])->name('validasi.index');
        Route::get('/validasi/{knowledge}', [KnowledgeValidationController::class, 'show'])->name('validasi.show');
        Route::put('/validasi/{knowledge}', [KnowledgeValidationController::class, 'update'])->name('validasi.update');
        Route::patch('/validasi/{knowledge}/approve', [KnowledgeValidationController::class, 'approve'])->name('validasi.approve');
        Route::patch('/validasi/{knowledge}/reject', [KnowledgeValidationController::class, 'reject'])->name('validasi.reject');
    });


    // =============================================================
    // ROLE: ANGGOTA & SEMUA MEMBER
    // Forum Diskusi
    Route::get('/forum/create', [App\Http\Controllers\ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [App\Http\Controllers\ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{thread}/edit', [App\Http\Controllers\ForumController::class, 'edit'])->name('forum.edit')->whereNumber('thread');
    Route::put('/forum/{thread}', [App\Http\Controllers\ForumController::class, 'update'])->name('forum.update')->whereNumber('thread');
    Route::delete('/forum/{thread}', [App\Http\Controllers\ForumController::class, 'destroy'])->name('forum.destroy');
    Route::post('/forum/{thread}/reply', [App\Http\Controllers\ForumController::class, 'storeReply'])->name('forum.reply');

    // Dashboard Forum Manajemen & Soft Deletes Milik Sendiri
    Route::prefix('dashboard/forum')->name('dashboard.forum.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DashboardForumController::class, 'index'])->name('index');
        Route::get('/trash', [\App\Http\Controllers\DashboardForumController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [\App\Http\Controllers\DashboardForumController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\DashboardForumController::class, 'forceDelete'])->name('forceDelete');
    });

    // =============================================================
    // ROLE: MODERATOR + ADMIN
    // Mengelola forum diskusi
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin,Moderator'])->group(function () {
        Route::get('/forum/manage', function () {
            return redirect()->route('moderator.forum.approval');
        })->name('forum.manage');

        Route::delete('/forum/reply/{reply}', [App\Http\Controllers\ForumController::class, 'destroyReply'])->name('forum.reply.destroy');
        Route::patch('/forum/{thread}/pin', [App\Http\Controllers\ForumController::class, 'pin'])->name('forum.pin');
        Route::patch('/forum/{thread}/lock', [App\Http\Controllers\ForumController::class, 'lock'])->name('forum.lock');

        // ---- Moderasi Approval Forum ----
        Route::prefix('moderator')->name('moderator.')->group(function () {
            Route::get('/forum/approval', [App\Http\Controllers\ModeratorForumController::class, 'index'])->name('forum.approval');
            Route::patch('/forum/{thread}/approve', [App\Http\Controllers\ModeratorForumController::class, 'approve'])->name('forum.approve');
            Route::patch('/forum/{thread}/reject', [App\Http\Controllers\ModeratorForumController::class, 'reject'])->name('forum.reject');
        });
    });

    // =============================================================
    // ROLE: SUPER ADMIN + ADMIN PUSAT + ADMIN
    // Panel administrator — kelola statistik, users, FAQ
    // =============================================================
    Route::prefix('admin')->name('admin.')->middleware(['role:Super Admin,Admin Pusat,Admin'])->group(function () {
        Route::get('/statistik', [App\Http\Controllers\StatisticController::class, 'index'])->name('statistik');
        Route::resource('/users', App\Http\Controllers\UserController::class);
        Route::resource('/faq', App\Http\Controllers\FaqController::class);
        Route::post('/faq-section', [App\Http\Controllers\FaqController::class, 'storeSection'])->name('faq.storeSection');
        Route::put('/faq-section/update', [App\Http\Controllers\FaqController::class, 'updateSection'])->name('faq.updateSection');
        Route::delete('/faq-section', [App\Http\Controllers\FaqController::class, 'destroySection'])->name('faq.destroySection');
    });
});

require __DIR__.'/auth.php';

// =================================================================
// KEYCLOAK SSO AUTHENTICATION
// =================================================================
use App\Http\Controllers\Auth\KeycloakController;

// Callback setelah user berhasil login di Keycloak
Route::get('/auth/keycloak/callback', [KeycloakController::class, 'callback'])->name('keycloak.callback');

// Logout dari Laravel + Keycloak sekaligus
Route::post('/keycloak/logout', [KeycloakController::class, 'logout'])->name('keycloak.logout');

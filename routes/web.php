<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\ProfileController;
use App\Models\Knowledge;
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
Route::get('/api/search/autocomplete', [App\Http\Controllers\SearchController::class, 'autocomplete'])->name('search.autocomplete');
Route::get('/cari', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');

// =================================================================
// AUTHENTICATED ROUTES — Semua user yang sudah login
// =================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ----- Dashboard Redirect -----
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (!$user) return redirect('/login');

        if (in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD']) || $user->email === 'superadmin@brin.go.id') {
            return redirect()->route('admin.statistik');
        }

        if (in_array($user->role, ['Analisis Pengetahuan', 'Analis Pengetahuan', 'Anggota', 'Kreator Pengetahuan'])) {
            return redirect()->route('knowledge.index');
        } elseif (in_array($user->role, ['Moderator'])) {
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

    // =============================================================
    // ROLE: MANAJEMEN KONTEN PENGETAHUAN
    // Super Admin, Admin Pusat, Admin IPPD, Analisis Pengetahuan, Kreator Pengetahuan
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD,Analisis Pengetahuan,Analis Pengetahuan,Anggota,Kreator Pengetahuan,Moderator'])->group(function () {
        Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
        Route::get('/knowledge/create', [KnowledgeController::class, 'create'])->name('knowledge.create');
        Route::post('/knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
        Route::get('/knowledge/{knowledge}/edit', [KnowledgeController::class, 'edit'])->name('knowledge.edit');
        Route::put('/knowledge/{knowledge}', [KnowledgeController::class, 'update'])->name('knowledge.update');
        Route::delete('/knowledge/{knowledge}', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');
    });



    // Detail Knowledge (Read-only untuk publik/member)
    Route::get('/knowledge/{knowledge}', [KnowledgeController::class, 'show'])->name('knowledge.show');

    // =============================================================
    // ROLE: ANALISIS PENGETAHUAN + ADMIN
    // Validasi, approve, atau reject konten
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD,Analisis Pengetahuan,Analis Pengetahuan'])->group(function () {
        Route::get('/validasi', function (\Illuminate\Http\Request $request) {
            $query = Knowledge::with(['user', 'category'])->where('status', '!=', 'Draft')->latest();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            $knowledges = $query->paginate(10)->withQueryString();
            return view('knowledge.validasi_index', compact('knowledges'));
        })->name('validasi.index');

        Route::get('/validasi/{knowledge}', function (Knowledge $knowledge) {
            $categories = \App\Models\Category::all();
            return view('knowledge.validasi', compact('knowledge', 'categories'));
        })->name('validasi.show');

        Route::put('/validasi/{knowledge}', function (\Illuminate\Http\Request $request, Knowledge $knowledge) {
            $validated = $request->validate([
                'judul'          => 'required|string|max:255',
                'tipe'           => 'required|in:Teks,Gambar,Video,Audio',
                'url_teks'       => 'nullable|string',
                'penulis'        => 'nullable|string|max:255',
                'kolaborator'    => 'nullable|string|max:255',
                'deskripsi'      => 'nullable|string',
                'detail'         => 'nullable|string',
                'category_id'    => 'nullable|exists:categories,id',
                'tanggal_terbit' => 'nullable|date',
                'file'           => 'nullable|file|max:10240',
            ]);

            if ($request->hasFile('file')) {
                $validated['file_path'] = $request->file('file')->store('knowledge_files', 'public');
            }

            $knowledge->update($validated);

            if ($request->has('tags')) {
                $tagsInput = explode(',', $request->tags ?? '');
                $tagIds = [];
                foreach ($tagsInput as $tagName) {
                    $trimmed = trim($tagName);
                    if ($trimmed) {
                        $tag = \App\Models\Tag::firstOrCreate(['nama_label' => $trimmed]);
                        $tagIds[] = $tag->id;
                    }
                }
                $knowledge->tags()->sync($tagIds);
            }

            return back()->with('success', 'Data pengetahuan berhasil diperbarui.');
        })->name('validasi.update');


        Route::patch('/validasi/{knowledge}/approve', function (Knowledge $knowledge) {
            $knowledge->update(['status' => 'Disetujui']);
            return back()->with('success', 'Konten berhasil disetujui.');
        })->name('validasi.approve');

        Route::patch('/validasi/{knowledge}/reject', function (Knowledge $knowledge) {
            $knowledge->update(['status' => 'Ditolak']);
            return redirect()->route('validasi.index')->with('success', 'Konten berhasil ditolak.');
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
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD,Moderator'])->group(function () {
        Route::get('/forum/manage', function () {
            return redirect()->route('moderator.forum.approval');
        })->name('forum.manage');

        Route::delete('/forum/{thread}', [App\Http\Controllers\ForumController::class, 'destroy'])->name('forum.destroy');
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
    // =============================================================
    // ROLE: SUPER ADMIN + ADMIN PUSAT + ADMIN IPPD
    // Manajemen CRUD Pengguna
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD'])->group(function () {
        Route::resource('/admin/users', App\Http\Controllers\UserController::class, ['as' => 'admin']);
    });

    // =============================================================
    // ROLE: SUPER ADMIN + ADMIN PUSAT + ADMIN IPPD
    // Panel administrator — kelola statistik, role, konfigurasi
    // =============================================================
    Route::middleware(['role:Super Admin,Admin Pusat,Admin IPPD'])->group(function () {
        Route::get('/admin/statistik', [App\Http\Controllers\StatisticController::class, 'index'])->name('admin.statistik');
        Route::get('/admin/roles', function () {
            return view('dashboard'); // TODO: Manajemen Role
        })->name('admin.roles');
        Route::get('/admin/settings', function () {
            return view('dashboard'); // TODO: Konfigurasi Aplikasi
        })->name('admin.settings');
    });
});

require __DIR__.'/auth.php';

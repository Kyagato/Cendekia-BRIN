<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ForumThread;
use App\Models\Knowledge;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * API: Pencarian cepat (autocomplete/live search dari navbar & homepage)
     * Endpoint: GET /api/search?q=...&tipe=...&kategori=...
     */
    public function apiSearch(Request $request)
    {
        $query = Knowledge::query()
            ->where('status', 'Disetujui')
            ->with(['category', 'user', 'tags']);

        // Pencarian teks (judul, deskripsi, tag, user, instansi)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('tags', function ($tagQ) use ($search) {
                      $tagQ->where('nama_label', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('instansi', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter Kategori (by ID)
        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        $results = $query->latest()->take(10)->get();

        return response()->json($results);
    }

    /**
     * Halaman Hasil Pencarian Lengkap (Full Page)
     * Route: GET /cari?q=...&tipe=...&kategori=...&label=...&sort=...
     */
    public function index(Request $request)
    {
        $query = Knowledge::query()
            ->where('status', 'Disetujui')
            ->with(['category', 'user', 'tags']);

        // Pencarian teks terintegrasi
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('tags', function ($tagQ) use ($search) {
                      $tagQ->where('nama_label', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('instansi', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Tipe Konten
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        // Filter Label/Tag
        if ($request->filled('label')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('nama_label', $request->label);
            });
        }

        // Filter Instansi
        if ($request->filled('instansi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('instansi', $request->instansi);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'terbaru');
        match ($sort) {
            'terpopuler' => $query->orderByDesc('views_count'),
            'az' => $query->orderBy('judul'),
            'za' => $query->orderByDesc('judul'),
            default => $query->latest(),
        };

        $results = $query->paginate(12)->appends($request->all());

        // Data untuk sidebar filter
        $categories = Category::withCount([
            'knowledge' => fn($q) => $q->where('status', 'Disetujui')
        ])->get();

        $popularTags = Tag::withCount('knowledge')
            ->orderByDesc('knowledge_count')
            ->take(20)
            ->get();

        // Pencarian forum (jika ada keyword)
        $forumResults = collect();
        if ($request->filled('q')) {
            $forumResults = ForumThread::with(['user', 'category'])
                ->withCount('replies')
                ->where('judul', 'like', "%{$request->q}%")
                ->orWhere('konten', 'like', "%{$request->q}%")
                ->latest()
                ->take(5)
                ->get();
        }

        return view('pages.search', compact(
            'results', 'categories', 'popularTags', 'forumResults'
        ));
    }
}

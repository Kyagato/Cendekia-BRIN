<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ForumThread;
use App\Models\Knowledge;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    /**
     * API: Autocomplete saran judul pengetahuan
     * Endpoint: GET /api/search/autocomplete?q=...
     * Hanya mencocokkan judul yang DIMULAI dengan huruf yang diketik user
     */
    public function autocomplete(Request $request)
    {
        if (!$request->filled('q') || strlen($request->q) < 1) {
            return response()->json([]);
        }

        $search = $request->q;

        $results = Knowledge::where('status', 'Disetujui')
            ->where('judul', 'like', "{$search}%")
            ->select('id', 'judul', 'tipe', 'category_id')
            ->with('category:id,nama_kategori')
            ->orderBy('judul')
            ->take(8)
            ->get();

        return response()->json($results);
    }

    /**
     * API: Pencarian cepat (autocomplete/live search dari navbar & homepage)
     * Endpoint: GET /api/search?q=...&tipe=...&kategori=...
     */
    public function apiSearch(Request $request)
    {
        $results = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Disetujui')
            ->filter($request->all())
            ->take(10)
            ->get();

        return response()->json($results);
    }

    /**
     * Halaman Hasil Pencarian Lengkap (Full Page)
     * Route: GET /cari?q=...&tipe=...&kategori=...&label=...&sort=...
     */
    public function index(Request $request)
    {
        $results = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Disetujui')
            ->filter($request->all())
            ->paginate(12)
            ->appends($request->all());

        // Data untuk sidebar filter
        $categories = Category::withCount([
            'knowledge' => fn($q) => $q->where('status', 'Disetujui')
        ])->get();

        $popularTags = Tag::withCount(['knowledge' => fn($q) => $q->where('status', 'Disetujui')])
            ->has('knowledge')
            ->where('nama_label', 'not like', '%{%')
            ->where('nama_label', 'not like', '%[%')
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

        return Inertia::render('Search/Index', [
            'results' => $results,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'forumResults' => $forumResults,
            'filters' => [
                'q' => $request->input('q', ''),
                'tipe' => $request->input('tipe', ''),
                'kategori' => $request->input('kategori', ''),
                'label' => $request->input('label', ''),
                'instansi' => $request->input('instansi', ''),
                'sort' => $request->input('sort', 'terbaru'),
            ],
        ]);
    }
}

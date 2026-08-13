<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Faq;
use App\Models\ForumThread;
use App\Models\Knowledge;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredKnowledges = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Disetujui')
            ->where('unggulan', true)
            ->latest()
            ->take(6)
            ->get();

        $mostViewed = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Disetujui')
            ->orderByDesc('views_count')
            ->take(6)
            ->get();

        $latest = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Disetujui')
            ->latest()
            ->take(8)
            ->get();

        $popularCategories = Category::withCount(['knowledge' => fn($q) => $q->where('status', 'Disetujui')])
            ->orderByDesc('knowledge_count')
            ->take(8)
            ->get();

        $popularTags = Tag::withCount('knowledge')
            ->orderByDesc('knowledge_count')
            ->take(12)
            ->get();

        return view('welcome', compact('featuredKnowledges', 'mostViewed', 'latest', 'popularCategories', 'popularTags'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function category(Request $request)
    {
        $categories = Category::withCount(['knowledge' => fn($q) => $q->where('status', 'Disetujui')])->get();
        $tags = Tag::withCount('knowledge')->orderByDesc('knowledge_count')->take(20)->get();

        $query = Knowledge::with(['category', 'user', 'tags'])->where('status', 'Disetujui');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        if ($request->filled('instansi')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('instansi', $request->instansi);
            });
        }

        if ($request->filled('label')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('nama_label', $request->label);
            });
        }

        if ($request->filled('q')) {
            $query->where('judul', 'like', '%' . $request->q . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->q . '%');
        }

        $knowledge = $query->latest()->paginate(12);

        return view('pages.category', compact('categories', 'tags', 'knowledge'));
    }

    public function categoryShow($id)
    {
        $category = Category::findOrFail($id);
        $knowledge = Knowledge::where('category_id', $id)
            ->where('status', 'Disetujui')
            ->with(['user', 'tags'])
            ->latest()
            ->paginate(12);

        return view('pages.category-show', compact('category', 'knowledge'));
    }

    public function faq(Request $request)
    {
        $faqs = Faq::orderBy('kategori_faq')->orderBy('urutan')->get()->groupBy('kategori_faq');
        return view('pages.faq', compact('faqs'));
    }

    public function forum()
    {
        $threads = ForumThread::with(['user', 'category'])
            ->withCount('replies')
            ->latest()
            ->paginate(15);

        return view('pages.forum', compact('threads'));
    }

    public function search(Request $request)
    {
        $query = Knowledge::where('status', 'Disetujui');

        if ($request->filled('q')) {
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        $results = $query->take(10)->get();

        return response()->json($results);
    }

    public function toggleDarkMode(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->dark_mode = $request->input('dark_mode', !$user->dark_mode);
            $user->save();

            return response()->json(['status' => 'success', 'dark_mode' => $user->dark_mode]);
        }

        return response()->json(['status' => 'unauthenticated'], 401);
    }
}

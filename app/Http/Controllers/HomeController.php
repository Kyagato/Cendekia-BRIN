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

        $popularTags = Tag::withCount(['knowledge' => fn($q) => $q->where('status', 'Disetujui')])
            ->has('knowledge')
            ->where('nama_label', 'not like', '%{%')
            ->where('nama_label', 'not like', '%[%')
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
        $tags = Tag::withCount(['knowledge' => fn($q) => $q->where('status', 'Disetujui')])
            ->has('knowledge')
            ->where('nama_label', 'not like', '%{%')
            ->where('nama_label', 'not like', '%[%')
            ->orderByDesc('knowledge_count')
            ->take(20)
            ->get();

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
        $faqs = Faq::whereNotNull('pertanyaan')->orderBy('kategori_faq')->orderBy('urutan')->get()->groupBy('kategori_faq');
        return view('pages.faq', compact('faqs'));
    }

    public function forum(Request $request)
    {
        $sort = $request->get('sort', 'terbaru');

        $query = ForumThread::with(['user', 'category'])
            ->withCount('replies')
            ->where('status', 'approved');

        switch ($sort) {
            case 'populer_umum':
                // Populer Umum: berdasarkan gabungan views_count + replies_count
                $query->orderByRaw('(views_count + (SELECT COUNT(*) FROM forum_replies WHERE forum_replies.thread_id = forum_threads.id)) DESC');
                break;

            case 'populer_tayangan':
                // Populer berdasarkan Tayangan: berdasarkan views_count
                $query->orderByDesc('views_count');
                break;

            case 'populer_komentar':
                // Populer berdasarkan Komentar: berdasarkan replies_count
                $query->orderByDesc('replies_count');
                break;

            case 'terbaru':
            default:
                $query->latest();
                break;
        }

        $threads = $query->paginate(15)->withQueryString();

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

    /**
     * Tampilan publik detail pengetahuan (layout public, bukan dashboard).
     * Dipanggil dari beranda, kategori, dan halaman pencarian.
     */
    public function knowledgeShow($id)
    {
        $knowledge = Knowledge::with(['category', 'user', 'tags', 'threads.user'])->findOrFail($id);

        // Hanya pengetahuan yang sudah Disetujui yang boleh dilihat publik
        if ($knowledge->status !== 'Disetujui') {
            $user = auth()->user();
            $canView = $user && (
                $user->id === $knowledge->user_id ||
                in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD', 'Analisis Pengetahuan', 'Analis Pengetahuan', 'Kreator Pengetahuan']) ||
                $user->email === 'superadmin@brin.go.id'
            );
            if (!$canView) {
                abort(404);
            }
        }

        $knowledge->increment('views_count');

        // Estimasi waktu baca (200 kata/menit)
        $fullText = ($knowledge->deskripsi ?? '') . ' ' . ($knowledge->detail ?? '');
        $wordCount = str_word_count(strip_tags($fullText));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        return view('pages.knowledge-show', compact('knowledge', 'readingTime'));
    }

    public function toggleDarkMode(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Accept value from JSON body (fetch) or form input
            $input = $request->json('dark_mode');
            if ($input === null) {
                $input = $request->input('dark_mode');
            }

            if ($input !== null) {
                $user->dark_mode = (bool) $input;
            } else {
                $user->dark_mode = !$user->dark_mode;
            }

            $user->save();

            return response()->json(['status' => 'success', 'dark_mode' => (bool) $user->dark_mode]);
        }

        return response()->json(['status' => 'unauthenticated'], 401);
    }
}

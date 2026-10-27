<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Faq;
use App\Models\ForumThread;
use App\Models\Knowledge;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $homepageData = Cache::remember('homepage_data', 600, function () {
            $cardColumns = [
                'id',
                'user_id',
                'category_id',
                'judul',
                'penulis',
                'deskripsi',
                'tipe',
                'file_path',
                'status',
                'views_count',
                'created_at',
                'updated_at',
                'unggulan',
            ];

            return [
                'featuredKnowledges' => Knowledge::with(['category:id,nama_kategori', 'user:id,name,foto_profil', 'tags:id,nama_label'])
                    ->select($cardColumns)
                    ->where('status', 'Disetujui')
                    ->where('unggulan', true)
                    ->latest()
                    ->take(6)
                    ->get(),
                'mostViewed' => Knowledge::with(['category:id,nama_kategori', 'user:id,name,foto_profil', 'tags:id,nama_label'])
                    ->select($cardColumns)
                    ->where('status', 'Disetujui')
                    ->orderByDesc('views_count')
                    ->take(6)
                    ->get(),
                'latest' => Knowledge::with(['category:id,nama_kategori', 'user:id,name,foto_profil', 'tags:id,nama_label'])
                    ->select($cardColumns)
                    ->where('status', 'Disetujui')
                    ->latest()
                    ->take(8)
                    ->get(),
                'popularCategories' => Category::withCount(['knowledge' => fn($q) => $q->where('status', 'Disetujui')])
                    ->orderByDesc('knowledge_count')
                    ->take(8)
                    ->get(),
                'popularTags' => Tag::withCount(['knowledge' => fn($q) => $q->where('status', 'Disetujui')])
                    ->has('knowledge')
                    ->where('nama_label', 'not like', '%{%')
                    ->where('nama_label', 'not like', '%[%')
                    ->orderByDesc('knowledge_count')
                    ->take(12)
                    ->get(),
            ];
        });

        // Attach is_bookmarked info if user logged in
        $userId = auth()->id();
        $userBookmarkIds = $userId ? \App\Models\Bookmark::where('user_id', $userId)->pluck('knowledge_id')->flip()->toArray() : [];

        $attachBookmark = function ($collection) use ($userBookmarkIds) {
            return $collection->map(function ($originalItem) use ($userBookmarkIds) {
                $item = clone $originalItem;
                $item->is_bookmarked = isset($userBookmarkIds[$item->id]);
                return $item;
            });
        };

        $featuredKnowledges = $attachBookmark($homepageData['featuredKnowledges']);
        $mostViewed = $attachBookmark($homepageData['mostViewed']);
        $latest = $attachBookmark($homepageData['latest']);
        $popularCategories = $homepageData['popularCategories'];
        $popularTags = $homepageData['popularTags'];

        return Inertia::render('Home', compact('featuredKnowledges', 'mostViewed', 'latest', 'popularCategories', 'popularTags'));
    }

    public function about()
    {
        return Inertia::render('About');
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

        $knowledge = Knowledge::with(['category', 'user', 'tags'])
            ->where('status', 'Disetujui')
            ->filter($request->all())
            ->paginate(12)
            ->withQueryString();

        $filters = [
            'q' => $request->input('q', '') ?: '',
            'tipe' => $request->input('tipe', '') ?: '',
            'kategori' => $request->input('kategori', '') ?: '',
            'label' => $request->input('label', '') ?: '',
            'instansi' => $request->input('instansi', '') ?: '',
            'sort' => $request->input('sort', 'terbaru'),
        ];

        return Inertia::render('Category/Index', [
            'categories' => $categories,
            'tags' => $tags,
            'knowledge' => $knowledge,
            'filters' => $filters,
        ]);
    }

    public function categoryShow($id)
    {
        Category::findOrFail($id);
        return redirect()->route('category.index', ['kategori' => $id]);
    }

    public function faq(Request $request)
    {
        $faqs = Faq::whereNotNull('pertanyaan')->orderBy('kategori_faq')->orderBy('urutan')->get()->groupBy('kategori_faq');
        return Inertia::render('Faq', compact('faqs'));
    }

    public function forum(Request $request)
    {
        $sort = $request->get('sort', 'terbaru');

        $query = ForumThread::approved()
            ->with(['user', 'category'])
            ->withCount('replies');

        switch ($sort) {
            case 'populer_umum':
                // Populer Umum: berdasarkan gabungan views_count + replies_count (menggunakan replies_count dari withCount)
                $query->orderByRaw('(views_count + replies_count) DESC');
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

        return Inertia::render('Forum/Index', compact('threads', 'sort'));
    }

    /**
     * Tampilan publik detail pengetahuan (layout public, bukan dashboard).
     * Dipanggil dari beranda, kategori, dan halaman pencarian.
     */
    public function knowledgeShow($id)
    {
        $knowledge = Knowledge::with(['category', 'user', 'tags', 'threads.user'])
            ->withCount(['bookmarks', 'likes', 'allComments'])
            ->findOrFail($id);

        // Hanya pengetahuan yang sudah Disetujui yang boleh dilihat publik
        if ($knowledge->status !== 'Disetujui') {
            $user = auth()->user();
            $canView = $user && (
                $user->id === $knowledge->user_id ||
                $user->isAdmin() ||
                $user->isAnalyst()
            );
            if (!$canView) {
                abort(404);
            }
        }

        // Debounce view count: hanya hitung 1 tayangan per pengunjung (User ID atau IP) per artikel dalam 2 jam (7200 detik)
        $viewerKey = auth()->check() ? 'user_' . auth()->id() : 'ip_' . request()->ip();
        $viewCacheKey = "knowledge_viewed_{$knowledge->id}_{$viewerKey}";

        if (Cache::add($viewCacheKey, true, 7200)) {
            $knowledge->increment('views_count');
        }

        // Estimasi waktu baca (200 kata/menit)
        $fullText = ($knowledge->deskripsi ?? '') . ' ' . ($knowledge->detail ?? '');
        $wordCount = str_word_count(strip_tags($fullText));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        // Bookmark status & count (memanfaatkan bookmarks_count dari withCount)
        $currentUser = auth()->user();
        $isBookmarked = $currentUser ? $knowledge->isBookmarkedBy($currentUser) : false;
        $bookmarksCount = $knowledge->bookmarks_count;

        // Likes & Comments (memanfaatkan likes_count & all_comments_count dari withCount)
        $isLiked = $currentUser ? $knowledge->isLikedBy($currentUser) : false;
        $likesCount = $knowledge->likes_count;
        $comments = $knowledge->comments()->get();
        $commentsCount = $knowledge->all_comments_count;

        return view('pages.knowledge-show', compact(
            'knowledge',
            'readingTime',
            'isBookmarked',
            'bookmarksCount',
            'isLiked',
            'likesCount',
            'comments',
            'commentsCount'
        ));
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

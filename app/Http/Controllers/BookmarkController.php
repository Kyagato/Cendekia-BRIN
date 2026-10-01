<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Knowledge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark status (Bookmark / Unbookmark).
     */
    public function toggle(Knowledge $knowledge): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan login terlebih dahulu untuk menyimpan artikel.',
            ], 401);
        }

        $existing = Bookmark::where('user_id', $user->id)
            ->where('knowledge_id', $knowledge->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
            $message = 'Artikel berhasil dihapus dari daftar tersimpan.';
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'knowledge_id' => $knowledge->id,
            ]);
            $bookmarked = true;
            $message = 'Artikel berhasil disimpan ke daftar tersimpan.';
        }

        \App\Models\AuditLog::record(
            $bookmarked ? 'BOOKMARK_ADD' : 'BOOKMARK_REMOVE',
            ($bookmarked ? 'Menyimpan' : 'Menghapus') . " bookmark artikel: '{$knowledge->judul}' (ID: {$knowledge->id})",
            ['knowledge_id' => $knowledge->id, 'judul' => $knowledge->judul],
            $user
        );

        $totalBookmarks = $knowledge->bookmarks()->count();

        return response()->json([
            'status' => 'success',
            'bookmarked' => $bookmarked,
            'total_bookmarks' => $totalBookmarks,
            'message' => $message,
        ]);
    }

    /**
     * Tampilkan halaman Bookmark / Pengetahuan Tersimpan milik user yang sedang login di Dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->bookmarkedKnowledge()
            ->with(['category', 'tags', 'user'])
            ->where('knowledge.status', 'Disetujui');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $knowledges = $query->latest('bookmarks.created_at')
            ->paginate(12)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($knowledges);
        }

        return \Inertia\Inertia::render('Bookmark/Index', [
            'knowledges' => $knowledges,
            'filters' => [
                'q' => $request->q ?? '',
                'tipe' => $request->tipe ?? '',
            ],
        ]);
    }
}

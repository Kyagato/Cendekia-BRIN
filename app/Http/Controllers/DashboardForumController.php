<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardForumController extends Controller
{
    /**
     * Menampilkan daftar forum topik milik user yang sedang login di dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = ForumThread::with([
            'user',
            'category',
            'knowledge.category',
            'replies' => function ($q) {
                $q->with('user')->latest();
            }
        ])
            ->withCount('replies')
            ->where('user_id', $user->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        $threads = $query->paginate(10)->withQueryString();

        $counts = [
            'total'    => ForumThread::where('user_id', $user->id)->count(),
            'approved' => ForumThread::where('user_id', $user->id)->where('status', 'approved')->count(),
            'pending'  => ForumThread::where('user_id', $user->id)->where('status', 'pending')->count(),
            'rejected' => ForumThread::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'trashed'  => ForumThread::onlyTrashed()->where('user_id', $user->id)->count(),
        ];

        $categories = \App\Models\Category::all();
        $knowledges = \App\Models\Knowledge::with('category')
            ->where('status', 'Disetujui')
            ->orderBy('judul')
            ->get();

        return view('forum.dashboard_index', compact('threads', 'counts', 'categories', 'knowledges'));
    }

    /**
     * Menampilkan daftar forum topik milik user yang ada di tong sampah (Soft-deleted).
     */
    public function trash(Request $request): View
    {
        $user = Auth::user();

        $query = ForumThread::onlyTrashed()
            ->with(['category', 'knowledge'])
            ->withCount('replies')
            ->where('user_id', $user->id)
            ->latest('deleted_at');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        $trashedThreads = $query->paginate(10)->withQueryString();

        return view('forum.dashboard_trash', compact('trashedThreads'));
    }

    /**
     * Memulihkan topik forum dari tong sampah (Restore).
     */
    public function restore(int $id): RedirectResponse
    {
        $user = Auth::user();

        $thread = ForumThread::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $thread->restore();

        AuditLog::record(
            'FORUM_RESTORE',
            "Memulihkan topik diskusi dari tong sampah: '{$thread->judul}' (ID: {$thread->id})",
            ['thread_id' => $thread->id, 'judul' => $thread->judul]
        );

        return redirect()->route('dashboard.forum.trash')->with('success', 'Topik diskusi berhasil dipulihkan.');
    }

    /**
     * Menghapus permanen topik forum dari tong sampah.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $user = Auth::user();

        $thread = ForumThread::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $title = $thread->judul;
        $threadId = $thread->id;

        // Cascade hapus balasan jika ada
        $thread->replies()->forceDelete();
        $thread->forceDelete();

        AuditLog::record(
            'FORUM_FORCE_DELETE',
            "Menghapus permanen topik diskusi: '{$title}' (ID: {$threadId})",
            ['thread_id' => $threadId, 'judul' => $title]
        );

        return redirect()->route('dashboard.forum.trash')->with('success', 'Topik diskusi berhasil dihapus permanen.');
    }
}

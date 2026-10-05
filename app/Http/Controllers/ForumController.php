<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use Inertia\Inertia;

class ForumController extends Controller
{
    // Halaman list forum sudah ada di HomeController@forum

    // 1. Form Buat Thread (Untuk semua member login)
    public function create(Request $request)
    {
        $categories = Category::all();
        $knowledges = \App\Models\Knowledge::with('category')
            ->where('status', 'Disetujui')
            ->orderBy('judul')
            ->get();

        $linkedKnowledge = null;
        if ($request->filled('knowledge_id')) {
            $linkedKnowledge = \App\Models\Knowledge::find($request->knowledge_id);
        }

        return Inertia::render('Forum/Create', [
            'categories' => $categories,
            'knowledges' => $knowledges,
            'linkedKnowledge' => $linkedKnowledge,
            'ref' => $request->query('ref', ''),
        ]);
    }

    // 2. Simpan Thread
    public function store(Request $request)
    {
        $request->validate([
            'judul'        => 'required|string|max:255',
            'konten'       => 'required|string',
            'category_id'  => 'required|exists:categories,id',
            'knowledge_id' => 'nullable|exists:knowledge,id',
        ]);

        $user   = Auth::user();
        $status = $user->isAdmin() ? 'approved' : 'pending';

        $thread = ForumThread::create([
            'user_id'      => $user->id,
            'category_id'  => $request->category_id,
            'judul'        => $request->judul,
            'konten'       => $request->konten,
            'knowledge_id' => $request->knowledge_id,
            'status'       => $status,
            'approved_by'  => $status === 'approved' ? $user->id : null,
            'approved_at'  => $status === 'approved' ? now() : null,
        ]);

        \App\Models\AuditLog::record(
            'FORUM_CREATE',
            "Membuat topik diskusi: '{$thread->judul}' (Status: {$status})",
            ['thread_id' => $thread->id, 'judul' => $thread->judul, 'category_id' => $thread->category_id]
        );

        $message = $status === 'approved'
            ? 'Topik diskusi berhasil dibuat dan langsung tayang.'
            : 'Topik diskusi berhasil dibuat dan menunggu persetujuan moderator.';

        if ($request->input('ref') === 'dashboard') {
            return redirect()->route('dashboard.forum.index')->with('success', $message);
        }

        return redirect()->route('forum.show', $thread->id)->with('success', $message);
    }

    // 3. Tampilkan Thread Detail + Replies
    public function show(int $id)
    {
        $user = Auth::user();

        // Cari thread, termasuk di tong sampah jika pemilik atau moderator/admin
        $threadQuery = ForumThread::withTrashed();

        if ($user && ($user->isAdmin() || $user->isModerator())) {
            $thread = $threadQuery->findOrFail($id);
        } elseif ($user) {
            $thread = $threadQuery->where(function ($q) use ($user) {
                $q->whereNull('deleted_at')
                  ->orWhere('user_id', $user->id);
            })->findOrFail($id);
        } else {
            $thread = ForumThread::where('status', 'approved')->findOrFail($id);
        }

        // Increment view count hanya jika tidak di tong sampah
        if (!$thread->trashed()) {
            $thread->increment('views_count');
        }

        $thread->load(['user', 'category', 'knowledge.category']);

        // Load only top-level replies (no parent), with their nested replies & user info
        $replies = $thread->replies()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'replies.replies.user'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Forum/Show', compact('thread', 'replies'));
    }

    // 4. Tambah Balasan (Reply) — supports nested replies
    public function storeReply(Request $request, ForumThread $thread)
    {
        if ($thread->is_locked) {
            return back()->with('error', 'Topik ini sudah dikunci, Anda tidak bisa menambahkan balasan.');
        }

        $request->validate([
            'konten'      => 'required|string',
            'parent_id'   => 'nullable|exists:forum_replies,id',
            'mention_user'=> 'nullable|string|max:255',
        ]);

        ForumReply::create([
            'thread_id'    => $thread->id,
            'user_id'      => Auth::id(),
            'konten'       => $request->konten,
            'parent_id'    => $request->parent_id ?: null,
            'mention_user' => $request->mention_user ?: null,
        ]);

        return back()->with('success', 'Balasan berhasil ditambahkan.');
    }

    // ==========================================
    // MODERATOR ACTIONS
    // ==========================================

    public function destroy(ForumThread $thread)
    {
        // Izinkan pemilik thread atau moderator/admin untuk menghapus
        if (!Auth::user()->isAdmin() && !Auth::user()->isModerator() && $thread->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus topik ini.');
        }

        $title = $thread->judul;
        $id = $thread->id;
        $thread->delete();

        \App\Models\AuditLog::record(
            'FORUM_DELETE',
            "Menghapus topik diskusi ke tong sampah: '{$title}' (ID: {$id})",
            ['thread_id' => $id, 'judul' => $title]
        );

        if (request()->has('from_dashboard')) {
            return redirect()->route('dashboard.forum.index')->with('success', 'Topik diskusi berhasil dipindahkan ke tong sampah.');
        }

        return redirect()->route('forum.index')->with('success', 'Topik berhasil dipindahkan ke tong sampah.');
    }

    public function destroyReply(ForumReply $reply)
    {
        Gate::authorize('manage-forum');
        $replyId = $reply->id;
        $reply->delete();

        \App\Models\AuditLog::record(
            'FORUM_REPLY_DELETE',
            "Menghapus balasan forum (ID: {$replyId})",
            ['reply_id' => $replyId]
        );

        return back()->with('success', 'Balasan berhasil dihapus.');
    }

    public function pin(ForumThread $thread)
    {
        Gate::authorize('manage-forum');
        $thread->update(['is_pinned' => !$thread->is_pinned]);
        $status = $thread->is_pinned ? 'dipin' : 'dilepas pinnya';

        \App\Models\AuditLog::record(
            'FORUM_PIN',
            "Mengubah status pin topik diskusi: '{$thread->judul}' menjadi {$status}",
            ['thread_id' => $thread->id, 'is_pinned' => $thread->is_pinned]
        );

        return back()->with('success', "Topik berhasil $status.");
    }

    public function lock(ForumThread $thread)
    {
        Gate::authorize('manage-forum');
        $thread->update(['is_locked' => !$thread->is_locked]);
        $status = $thread->is_locked ? 'dikunci' : 'dibuka kuncinya';

        \App\Models\AuditLog::record(
            'FORUM_LOCK',
            "Mengubah status kunci topik diskusi: '{$thread->judul}' menjadi {$status}",
            ['thread_id' => $thread->id, 'is_locked' => $thread->is_locked]
        );

        return back()->with('success', "Topik berhasil $status.");
    }
}

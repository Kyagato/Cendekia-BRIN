<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ForumController extends Controller
{
    // Halaman list forum sudah ada di HomeController@forum

    // 1. Form Buat Thread (Untuk semua member login)
    public function create()
    {
        $categories = Category::all();
        return view('pages.forum-create', compact('categories'));
    }

    // 2. Simpan Thread
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $thread = ForumThread::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'judul' => $request->judul,
            'konten' => $request->konten,
        ]);

        return redirect()->route('forum.show', $thread->id)->with('success', 'Topik diskusi berhasil dibuat.');
    }

    // 3. Tampilkan Thread Detail + Replies
    public function show(ForumThread $thread)
    {
        // Increment view count
        $thread->increment('views_count');
        
        $thread->load(['user', 'category']);
        
        // Load only top-level replies (no parent), with their nested replies & user info
        $replies = $thread->replies()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'replies.replies.user'])
            ->latest()
            ->paginate(15);
        
        return view('pages.forum-show', compact('thread', 'replies'));
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
        Gate::authorize('manage-forum');
        $thread->delete();
        return redirect()->route('forum.index')->with('success', 'Topik berhasil dihapus.');
    }

    public function destroyReply(ForumReply $reply)
    {
        Gate::authorize('manage-forum');
        $reply->delete();
        return back()->with('success', 'Balasan berhasil dihapus.');
    }

    public function pin(ForumThread $thread)
    {
        Gate::authorize('manage-forum');
        $thread->update(['is_pinned' => !$thread->is_pinned]);
        $status = $thread->is_pinned ? 'dipin' : 'dilepas pinnya';
        return back()->with('success', "Topik berhasil $status.");
    }

    public function lock(ForumThread $thread)
    {
        Gate::authorize('manage-forum');
        $thread->update(['is_locked' => !$thread->is_locked]);
        $status = $thread->is_locked ? 'dikunci' : 'dibuka kuncinya';
        return back()->with('success', "Topik berhasil $status.");
    }
}

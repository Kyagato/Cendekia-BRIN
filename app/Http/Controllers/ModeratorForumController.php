<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeratorForumController extends Controller
{
    /**
     * List forum threads by status filter (default: pending).
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        // Validate status filter
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            $status = 'pending';
        }

        $threads = ForumThread::with(['user', 'category', 'approvedBy'])
            ->withCount('replies')
            ->where('status', $status)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => ForumThread::where('status', 'pending')->count(),
            'approved' => ForumThread::where('status', 'approved')->count(),
            'rejected' => ForumThread::where('status', 'rejected')->count(),
        ];

        return view('moderator.forum.approval', compact('threads', 'status', 'counts'));
    }

    /**
     * Approve a forum thread.
     */
    public function approve(ForumThread $thread)
    {
        $thread->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => null,
        ]);

        return back()->with('success', "Topik \"" . $thread->judul . "\" berhasil disetujui dan sekarang tayang di forum publik.");
    }

    /**
     * Reject a forum thread with a rejection note.
     */
    public function reject(Request $request, ForumThread $thread)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:1000',
        ]);

        $thread->update([
            'status'         => 'rejected',
            'approved_by'    => Auth::id(),
            'approved_at'    => now(),
            'rejection_note' => $request->rejection_note,
        ]);

        return back()->with('success', "Topik \"" . $thread->judul . "\" telah ditolak.");
    }
}

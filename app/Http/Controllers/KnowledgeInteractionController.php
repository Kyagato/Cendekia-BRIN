<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Knowledge;
use App\Models\KnowledgeComment;
use App\Models\KnowledgeLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeInteractionController extends Controller
{
    /**
     * Memberikan atau membatalkan Like (Rating apresiasi) pada artikel pengetahuan.
     */
    public function toggleLike(Knowledge $knowledge): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Silakan masuk ke akun Anda terlebih dahulu untuk memberikan like.',
            ], 401);
        }

        $existing = KnowledgeLike::where('knowledge_id', $knowledge->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
            $message = 'Batal menyukai artikel ini.';

            AuditLog::record(
                'KNOWLEDGE_UNLIKE',
                "Membatalkan suka pada artikel: '{$knowledge->judul}' (ID: {$knowledge->id})",
                ['knowledge_id' => $knowledge->id, 'judul' => $knowledge->judul],
                $user
            );
        } else {
            KnowledgeLike::create([
                'knowledge_id' => $knowledge->id,
                'user_id'      => $user->id,
            ]);
            $liked = true;
            $message = 'Menyukai artikel ini!';

            AuditLog::record(
                'KNOWLEDGE_LIKE',
                "Menyukai artikel: '{$knowledge->judul}' (ID: {$knowledge->id})",
                ['knowledge_id' => $knowledge->id, 'judul' => $knowledge->judul],
                $user
            );
        }

        $totalLikes = $knowledge->likes()->count();

        return response()->json([
            'status'      => 'success',
            'liked'       => $liked,
            'total_likes' => $totalLikes,
            'message'     => $message,
        ]);
    }

    /**
     * Menyimpan komentar atau balasan komentar pada artikel pengetahuan.
     */
    public function storeComment(Request $request, Knowledge $knowledge): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'unauthenticated', 'message' => 'Silakan login terlebih dahulu.'], 401);
            }
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'konten'    => 'required|string|max:3000',
            'parent_id' => 'nullable|exists:knowledge_comments,id',
        ], [
            'konten.required' => 'Komentar tidak boleh kosong.',
            'konten.max'      => 'Komentar maksimal 3000 karakter.',
        ]);

        $comment = KnowledgeComment::create([
            'knowledge_id' => $knowledge->id,
            'user_id'      => $user->id,
            'parent_id'    => $validated['parent_id'] ?? null,
            'konten'       => trim($validated['konten']),
        ]);

        AuditLog::record(
            'KNOWLEDGE_COMMENT',
            "Memberikan komentar pada artikel: '{$knowledge->judul}' (ID: {$knowledge->id})",
            [
                'knowledge_id' => $knowledge->id,
                'judul'        => $knowledge->judul,
                'comment_id'   => $comment->id,
                'is_reply'     => !empty($comment->parent_id),
            ],
            $user
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Komentar berhasil dikirim.',
                'comment' => $comment->load('user'),
            ]);
        }

        return back()->with('success', 'Komentar Anda berhasil dikirim.');
    }

    /**
     * Menghapus komentar pengetahuan (oleh pembuat komentar atau admin).
     */
    public function destroyComment(KnowledgeComment $comment): RedirectResponse
    {
        $user = Auth::user();

        if (!$user || ($user->id !== $comment->user_id && !$user->isAdmin())) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus komentar ini.');
        }

        $knowledgeId = $comment->knowledge_id;
        $comment->delete();

        AuditLog::record(
            'KNOWLEDGE_COMMENT_DELETE',
            "Menghapus komentar pada artikel pengetahuan ID: {$knowledgeId}",
            ['knowledge_id' => $knowledgeId, 'comment_id' => $comment->id],
            $user
        );

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}

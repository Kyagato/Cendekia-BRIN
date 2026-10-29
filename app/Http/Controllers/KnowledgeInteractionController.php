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
use Illuminate\Validation\Rule;

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
            'konten'       => 'required|string|max:3000',
            'parent_id'    => [
                'nullable',
                Rule::exists('knowledge_comments', 'id')->where(fn ($query) => $query->where('knowledge_id', $knowledge->id)),
            ],
            'mention_user' => 'nullable|string|max:255',
        ], [
            'konten.required'  => 'Komentar tidak boleh kosong.',
            'konten.max'       => 'Komentar maksimal 3000 karakter.',
            'parent_id.exists' => 'Komentar induk tidak valid atau tidak ditemukan pada artikel ini.',
        ]);

        $comment = KnowledgeComment::create([
            'knowledge_id' => $knowledge->id,
            'user_id'      => $user->id,
            'parent_id'    => $validated['parent_id'] ?? null,
            'mention_user' => !empty($validated['mention_user']) ? strip_tags(trim($validated['mention_user'])) : null,
            'konten'       => strip_tags(trim($validated['konten'])),
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

        if ($request->wantsJson() || $request->ajax()) {
            $html = '';
            if (!empty($comment->parent_id)) {
                $parentComment = KnowledgeComment::find($comment->parent_id);
                if ($parentComment && !empty($parentComment->parent_id)) {
                    $html = view('partials.knowledge-comment-subreply-item', [
                        'subReply' => $comment,
                        'parent'   => $parentComment
                    ])->render();
                } else {
                    $html = view('partials.knowledge-comment-reply-item', [
                        'reply'  => $comment,
                        'parent' => $parentComment
                    ])->render();
                }
            } else {
                $html = view('partials.knowledge-comment-item', ['comment' => $comment])->render();
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Komentar Anda berhasil dikirim.',
                'comment' => $comment->load('user'),
                'html'    => $html,
            ]);
        }

        $redirectUrl = strtok(url()->previous(), '#') . '#komentar';
        return redirect()->to($redirectUrl)->with('success', 'Komentar Anda berhasil dikirim.');
    }

    /**
     * Memperbarui komentar pengetahuan (hanya oleh pembuat komentar).
     */
    public function updateComment(Request $request, KnowledgeComment $comment): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->id !== $comment->user_id) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit komentar ini.');
        }

        $validated = $request->validate([
            'konten' => 'required|string|max:3000',
        ], [
            'konten.required' => 'Komentar tidak boleh kosong.',
            'konten.max'      => 'Komentar maksimal 3000 karakter.',
        ]);

        $cleanContent = strip_tags(trim($validated['konten']));
        $comment->update(['konten' => $cleanContent]);

        AuditLog::record(
            'KNOWLEDGE_COMMENT_UPDATE',
            "Memperbarui komentar pada artikel pengetahuan ID: {$comment->knowledge_id}",
            ['knowledge_id' => $comment->knowledge_id, 'comment_id' => $comment->id],
            $user
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Komentar berhasil diperbarui.',
                'konten'   => $cleanContent,
                'comment'  => $comment->load('user'),
                'isEdited' => $comment->updated_at->gt($comment->created_at),
            ]);
        }

        $redirectUrl = strtok(url()->previous(), '#') . '#komentar';
        return redirect()->to($redirectUrl)->with('success', 'Komentar berhasil diperbarui.');
    }

    /**
     * Menghapus komentar pengetahuan (oleh pembuat komentar, 3 role admin, atau moderator).
     */
    public function destroyComment(Request $request, KnowledgeComment $comment): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->isAdmin() && !$user->isModerator() && $user->id !== $comment->user_id)) {
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Komentar berhasil dihapus.',
            ]);
        }

        $redirectUrl = strtok(url()->previous(), '#') . '#komentar';
        return redirect()->to($redirectUrl)->with('success', 'Komentar berhasil dihapus.');
    }
}

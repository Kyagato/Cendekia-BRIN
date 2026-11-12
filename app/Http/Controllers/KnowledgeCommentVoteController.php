<?php

namespace App\Http\Controllers;

use App\Models\Knowledge;
use App\Models\KnowledgeComment;
use App\Models\KnowledgeCommentVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KnowledgeCommentVoteController extends Controller
{
    public function __invoke(Request $request, KnowledgeComment $comment): JsonResponse
    {
        $data = $request->validate(['value' => ['required', 'integer', 'in:-1,0,1']]);
        $user = $request->user();

        DB::transaction(function () use ($comment, $data, $user) {
            $knowledge = Knowledge::whereKey($comment->knowledge_id)->lockForUpdate()->first();
            abort_unless($knowledge && $knowledge->canBeViewedBy($user), 404);
            abort_unless(KnowledgeComment::whereKey($comment->id)->exists(), 404);
            $key = ['knowledge_comment_id' => $comment->id, 'user_id' => $user->id];
            if ((int) $data['value'] === 0) {
                KnowledgeCommentVote::where($key)->delete();
            } else {
                KnowledgeCommentVote::updateOrCreate($key, ['value' => (int) $data['value']]);
            }
        });

        $summary = KnowledgeComment::withVoteSummary()->findOrFail($comment->id);

        return response()->json([
            'upvotes' => (int) $summary->upvotes,
            'downvotes' => (int) $summary->downvotes,
            'user_vote' => (int) $summary->user_vote,
        ]);
    }
}

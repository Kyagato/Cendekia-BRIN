<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumReplyVote;
use App\Models\ForumThread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForumReplyVoteController extends Controller
{
    public function __invoke(Request $request, ForumReply $reply): JsonResponse
    {
        $data = $request->validate(['value' => ['required', 'integer', 'in:-1,0,1']]);
        $userId = $request->user()->id;

        DB::transaction(function () use ($reply, $userId, $data) {
            // Serialize votes against thread moderation and duplicate requests.
            $thread = ForumThread::whereKey($reply->thread_id)->lockForUpdate()->first();
            abort_unless($thread && $thread->isApproved(), 404);
            abort_if($thread->is_locked, 403, 'Topik dikunci, suara tidak dapat diubah.');
            abort_unless(ForumReply::whereKey($reply->id)->exists(), 404);

            $key = ['forum_reply_id' => $reply->id, 'user_id' => $userId];
            if ((int) $data['value'] === 0) {
                ForumReplyVote::where($key)->delete();
            } else {
                ForumReplyVote::updateOrCreate($key, ['value' => (int) $data['value']]);
            }
        });

        $summary = ForumReply::withVoteSummary()->findOrFail($reply->id);

        return response()->json([
            'upvotes' => (int) $summary->upvotes,
            'downvotes' => (int) $summary->downvotes,
            'user_vote' => (int) $summary->user_vote,
        ]);
    }
}

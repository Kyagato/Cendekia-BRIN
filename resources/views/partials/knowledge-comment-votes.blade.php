<div class="inline-flex items-center gap-1" role="group" aria-label="Suara komentar"
     x-data="knowledgeCommentVote({{ $voteComment->id }}, {{ (int) ($voteComment->user_vote ?? 0) }}, {{ (int) ($voteComment->upvotes ?? 0) }}, {{ (int) ($voteComment->downvotes ?? 0) }})">
    <button type="button" @click="submit(1)" :disabled="pending" :aria-pressed="vote === 1" aria-label="Upvote" title="Upvote"
        class="inline-flex items-center gap-1 rounded-md px-1.5 py-1 text-xs font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-blue-500 disabled:opacity-50 cursor-pointer"
        :class="vote === 1 ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40' : 'text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m-6 6 6-6 6 6" /></svg>
        <span x-text="upvotes">{{ (int) ($voteComment->upvotes ?? 0) }}</span>
    </button>
    <button type="button" @click="submit(-1)" :disabled="pending" :aria-pressed="vote === -1" aria-label="Downvote" title="Downvote"
        class="inline-flex items-center gap-1 rounded-md px-1.5 py-1 text-xs font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-red-500 disabled:opacity-50 cursor-pointer"
        :class="vote === -1 ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40' : 'text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-6-6 6 6 6-6" /></svg>
        <span x-text="downvotes">{{ (int) ($voteComment->downvotes ?? 0) }}</span>
    </button>
    <span x-show="error" x-text="error" role="alert" class="text-xs text-red-600 dark:text-red-400"></span>
</div>

<div id="comment-{{ $reply->id }}" class="bg-slate-50 dark:bg-slate-750/50 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700 space-y-2">
    <div class="flex items-start justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-200">
                @if($reply->user && $reply->user->foto_profil)
                    <img src="{{ asset('storage/' . $reply->user->foto_profil) }}" alt="{{ $reply->user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($reply->user?->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div>
                <span class="font-bold text-xs text-slate-900 dark:text-white">
                    {{ $reply->user?->name ?? 'Anonim' }}
                </span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 block">
                    {{ $reply->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->isAdmin()))
            <button type="button" 
                    @click="deleteComment({{ $reply->id }})" 
                    class="text-xs text-slate-400 hover:text-rose-600 transition p-1" 
                    title="Hapus balasan">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        @endif
    </div>
    <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed pl-1 sm:pl-2">
        {{ $reply->konten }}
    </div>
</div>

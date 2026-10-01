<div id="comment-{{ $comment->id }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm space-y-3">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-sm text-slate-700 dark:text-slate-200">
                @if($comment->user && $comment->user->foto_profil)
                    <img src="{{ asset('storage/' . $comment->user->foto_profil) }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($comment->user?->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm text-slate-900 dark:text-white">
                        {{ $comment->user?->name ?? 'Anonim' }}
                    </span>
                    @if($comment->user && in_array($comment->user->role, ['Super Admin', 'Admin Pusat', 'Admin']))
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300 font-semibold">Admin</span>
                    @endif
                </div>
                <span class="text-xs text-slate-400 dark:text-slate-500">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        {{-- Aksi Hapus Komentar jika pemilik atau admin --}}
        @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->isAdmin()))
            <button type="button" 
                    @click="deleteComment({{ $comment->id }})" 
                    class="text-xs text-slate-400 hover:text-rose-600 transition p-1" 
                    title="Hapus komentar">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        @endif
    </div>

    <div class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed pl-1 sm:pl-3">
        {{ $comment->konten }}
    </div>

    {{-- Tombol Balas --}}
    @auth
    <div class="pl-1 sm:pl-3 pt-1">
        <button type="button" 
                @click="setReply({{ $comment->id }}, '{{ addslashes($comment->user?->name ?? 'Anonim') }}')"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 transition cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
            </svg>
            Balas
        </button>
    </div>
    @endauth

    {{-- Balasan Bersarang (Replies) --}}
    <div id="replies-container-{{ $comment->id }}" class="ml-4 sm:ml-8 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 space-y-3 {{ ($comment->replies && $comment->replies->count() > 0) ? '' : 'hidden' }}">
        @if($comment->replies && $comment->replies->count() > 0)
            @foreach($comment->replies as $reply)
                @include('partials.knowledge-comment-reply-item', ['reply' => $reply])
            @endforeach
        @endif
    </div>
</div>

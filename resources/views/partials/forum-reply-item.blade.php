{{-- Partial: partials/forum-reply-item.blade.php
     Variables: $reply (ForumReply), $depth (int), $thread (ForumThread)
--}}
<div class="flex gap-3">
    {{-- Avatar --}}
    <div class="shrink-0 w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm overflow-hidden">
        @if($reply->user->foto_profil ?? null)
            <img src="{{ asset('storage/' . $reply->user->foto_profil) }}" class="w-full h-full object-cover rounded-full" alt="{{ $reply->user->name }}">
        @else
            {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
        @endif
    </div>

    <div class="flex-grow min-w-0">
        {{-- Header row: name + time + delete --}}
        <div class="flex flex-wrap justify-between items-start gap-1 mb-1">
            <div>
                <span class="font-semibold text-slate-800 dark:text-slate-100 text-sm">
                    {{ $reply->user->name ?? 'Anonymous' }}
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-normal ml-1.5">
                    {{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}
                </span>
            </div>

            @can('manage-forum')
            <form action="{{ route('forum.reply.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus balasan ini?');" class="shrink-0">
                @csrf @method('DELETE')
                <button type="submit" class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400 transition p-0.5 rounded">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </form>
            @endcan
        </div>

        {{-- @mention badge if this is a nested reply --}}
        @if($reply->mention_user)
        <div class="text-xs text-primary-600 dark:text-primary-400 font-semibold mb-1">
            &#64;{{ $reply->mention_user }}
        </div>
        @endif

        {{-- Reply content --}}
        <div class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed">
            {!! nl2br(e($reply->konten)) !!}
        </div>

        {{-- Balas button (only for authenticated users and non-locked threads) --}}
        @auth
            @if(!$thread->is_locked)
            <button
                @click="setReply({{ $reply->id }}, '{{ addslashes($reply->user->name ?? 'User') }}')"
                class="mt-2 text-xs font-semibold text-slate-400 dark:text-slate-500 hover:text-primary-600 dark:hover:text-primary-400 transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                Balas
            </button>
            @endif
        @endauth
    </div>
</div>

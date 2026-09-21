{{-- Partial: partials/forum-reply-item.blade.php
     Variables: $reply (ForumReply), $depth (int), $thread (ForumThread)
--}}
<div class="flex gap-3">
    {{-- Avatar --}}
    @if($reply->user)
        <a href="{{ route('users.show', $reply->user->id) }}" class="shrink-0 group" title="Lihat Profil {{ $reply->user->name }}">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm overflow-hidden group-hover:ring-2 group-hover:ring-primary-500 transition">
                @if($reply->user->foto_profil)
                    <img src="{{ asset('storage/' . $reply->user->foto_profil) }}" class="w-full h-full object-cover rounded-full" alt="{{ $reply->user->name }}">
                @else
                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                @endif
            </div>
        </a>
    @else
        <div class="shrink-0 w-9 h-9 rounded-full bg-slate-400 flex items-center justify-center text-white font-bold text-sm">
            U
        </div>
    @endif

    <div class="flex-grow min-w-0">
        {{-- Header row: name + time + delete --}}
        <div class="flex flex-wrap justify-between items-start gap-1 mb-1">
            <div class="relative">
                @if($reply->user)
                    <span class="relative inline-block"
                          x-data="{ openReplyPreview: false }"
                          @mouseenter="openReplyPreview = true"
                          @mouseleave="openReplyPreview = false">
                        <a href="{{ route('users.show', $reply->user->id) }}" class="font-semibold text-slate-800 dark:text-slate-100 text-sm hover:text-primary-600 dark:hover:text-primary-400 hover:underline transition">
                            {{ $reply->user->name }}
                        </a>

                        {{-- Hover Popover Card --}}
                        <div x-show="openReplyPreview"
                             x-cloak
                             @mouseenter="openReplyPreview = true"
                             @mouseleave="openReplyPreview = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute left-0 top-full mt-1.5 z-50 w-72 bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-2xl border border-slate-200 dark:border-slate-700 text-left cursor-default">
                            <div class="flex items-center gap-3 mb-3">
                                @if($reply->user->foto_profil)
                                    <img src="{{ asset('storage/' . $reply->user->foto_profil) }}"
                                         class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-lg flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-bold text-sm text-slate-900 dark:text-white truncate">
                                        {{ $reply->user->name }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $reply->user->role ?? 'Anggota' }}
                                    </div>
                                    @if($reply->user->instansi)
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate">
                                        {{ $reply->user->instansi }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('users.show', $reply->user->id) }}"
                               class="block w-full py-2 text-center text-xs font-semibold bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900/60 text-blue-600 dark:text-blue-300 rounded-xl transition">
                                Lihat Profil Lengkap &rarr;
                            </a>
                        </div>
                    </span>
                @else
                    <span class="font-semibold text-slate-800 dark:text-slate-100 text-sm">
                        Anonymous
                    </span>
                @endif
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

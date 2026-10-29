<div id="comment-{{ $comment->id }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm space-y-3">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-sm text-slate-700 dark:text-slate-200 overflow-hidden">
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
                <div class="flex items-center gap-1.5">
                    <span class="text-xs text-slate-400 dark:text-slate-500">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 italic comment-edited-label {{ ($comment->updated_at && $comment->updated_at->gt($comment->created_at)) ? '' : 'hidden' }}">(diedit)</span>
                </div>
            </div>
        </div>

        {{-- 3-Dots Kebab Menu di Posisi Paling Kanan --}}
        @php
            $canEdit = auth()->check() && auth()->id() === $comment->user_id;
            $canDelete = auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->isAdmin() || auth()->user()->isModerator());
            $hasMenu = $canEdit || $canDelete;
        @endphp

        @if($hasMenu)
        <div class="relative shrink-0" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
            <button type="button" 
                    @click.stop="menuOpen = !menuOpen" 
                    class="p-1 rounded-md text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                    :class="{ 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200': menuOpen }"
                    title="Opsi komentar" 
                    aria-label="Opsi komentar">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="menuOpen" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 top-full mt-1 w-32 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 z-50 text-left"
                 style="display: none;">
                @if($canEdit)
                <button type="button" 
                        @click.stop="menuOpen = false; startEditComment({{ $comment->id }})" 
                        class="w-full flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit</span>
                </button>
                @endif

                @if($canDelete)
                <button type="button" 
                        @click.stop="menuOpen = false; deleteComment({{ $comment->id }})" 
                        class="w-full flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus</span>
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Edit Form vs View Mode -->
    <div id="comment-edit-box-{{ $comment->id }}" class="hidden mt-2 bg-slate-50 dark:bg-slate-750 p-3 rounded-xl border border-blue-200 dark:border-blue-900/50">
        <div class="text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1.5 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Edit Komentar Anda</span>
        </div>
        <textarea id="comment-edit-input-{{ $comment->id }}" 
                  rows="3" 
                  class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition resize-none shadow-xs"
                  placeholder="Tuliskan perubahan komentar...">{{ $comment->konten }}</textarea>
        <div class="flex items-center justify-end gap-2 mt-2">
            <button type="button" 
                    @click="cancelEditComment({{ $comment->id }})" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition cursor-pointer">
                Batal
            </button>
            <button type="button" 
                    @click="submitEditComment({{ $comment->id }})" 
                    class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-bold transition shadow-sm active:scale-95 cursor-pointer">
                Simpan
            </button>
        </div>
    </div>

    <div id="comment-body-{{ $comment->id }}">
        <div class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed pl-1 sm:pl-3 comment-content">
            <span class="comment-text-span">{{ $comment->konten }}</span>
        </div>

        {{-- Tombol Balas --}}
        @auth
        <div class="pl-1 sm:pl-3 pt-2">
            <button type="button" 
                    @click="setReply({{ $comment->id }}, '{{ addslashes($comment->user?->name ?? 'Anonim') }}', 'replies-container-{{ $comment->id }}', false)"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Balas
            </button>
        </div>
        @endauth
    </div>

    {{-- Balasan Bersarang (Replies) --}}
    <div id="replies-container-{{ $comment->id }}" class="ml-4 sm:ml-8 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 space-y-3 {{ ($comment->replies && $comment->replies->count() > 0) ? '' : 'hidden' }}">
        @if($comment->replies && $comment->replies->count() > 0)
            @foreach($comment->replies as $reply)
                @include('partials.knowledge-comment-reply-item', ['reply' => $reply, 'parent' => $comment])
            @endforeach
        @endif
    </div>
</div>

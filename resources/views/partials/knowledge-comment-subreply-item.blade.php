<div id="comment-{{ $subReply->id }}" class="bg-white/80 dark:bg-slate-800/80 rounded-xl p-3 border border-slate-200/90 dark:border-slate-700/80 space-y-2 transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-800">
    <div class="flex items-start justify-between gap-2">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-200">
                @if($subReply->user && $subReply->user->foto_profil)
                    <img src="{{ asset('storage/' . $subReply->user->foto_profil) }}" alt="{{ $subReply->user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($subReply->user?->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div>
                <div>
                    <span class="font-bold text-xs text-slate-900 dark:text-white">
                        {{ $subReply->user?->name ?? 'Anonim' }}
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                        {{ $subReply->created_at->diffForHumans() }}
                    </span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic comment-edited-label {{ ($subReply->updated_at && $subReply->updated_at->gt($subReply->created_at)) ? '' : 'hidden' }}">(diedit)</span>
                </div>
            </div>
        </div>

        {{-- 3-Dots Kebab Menu di Posisi Paling Kanan --}}
        @php
            $canEdit = auth()->check() && auth()->id() === $subReply->user_id;
            $canDelete = auth()->check() && (auth()->id() === $subReply->user_id || auth()->user()->isAdmin() || auth()->user()->isModerator());
            $hasMenu = $canEdit || $canDelete;
        @endphp

        @if($hasMenu)
        <div class="relative shrink-0" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
            <button type="button" 
                    @click.stop="menuOpen = !menuOpen" 
                    class="p-1 rounded-md text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition cursor-pointer"
                    :class="{ 'bg-slate-200/60 dark:bg-slate-700 text-slate-700 dark:text-slate-200': menuOpen }"
                    title="Opsi balasan" 
                    aria-label="Opsi balasan">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
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
                        @click.stop="menuOpen = false; startEditComment({{ $subReply->id }})" 
                        class="w-full flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit</span>
                </button>
                @endif

                @if($canDelete)
                <button type="button" 
                        @click.stop="menuOpen = false; deleteComment({{ $subReply->id }})" 
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
    <div id="comment-edit-box-{{ $subReply->id }}" class="hidden mt-2 bg-slate-100 dark:bg-slate-800/80 p-2.5 rounded-lg border border-blue-200 dark:border-blue-900/50">
        <div class="text-[11px] font-semibold text-slate-700 dark:text-slate-200 mb-1 flex items-center gap-1">
            <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Edit Balasan Anda</span>
        </div>
        <textarea id="comment-edit-input-{{ $subReply->id }}" 
                  rows="2" 
                  class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition resize-none shadow-xs"
                  placeholder="Tuliskan perubahan balasan...">{{ $subReply->konten }}</textarea>
        <div class="flex items-center justify-end gap-1.5 mt-1.5">
            <button type="button" 
                    @click="cancelEditComment({{ $subReply->id }})" 
                    class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700 transition cursor-pointer">
                Batal
            </button>
            <button type="button" 
                    @click="submitEditComment({{ $subReply->id }})" 
                    class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-md text-[11px] font-bold transition shadow-sm active:scale-95 cursor-pointer">
                Simpan
            </button>
        </div>
    </div>

    <div id="comment-body-{{ $subReply->id }}">
        <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed pl-1 sm:pl-2 comment-content">
            @if(!empty($subReply->mention_user))
                <span class="font-semibold text-primary-600 dark:text-primary-400 mr-1">
                    {{ '@' . $subReply->mention_user }}
                </span>
            @endif
            <span class="comment-text-span">{{ $subReply->konten }}</span>
        </div>

        {{-- Tombol Balas pada sub-balasan Level 2 (dibalas lagi tetap di Level 2 di bawah parent yang sama, tidak menjorok lagi!) --}}
        @auth
        <div class="pl-1 sm:pl-2 pt-1">
            <button type="button" 
                    @click="setReply({{ $parent->id }}, '{{ addslashes($subReply->user?->name ?? 'Anonim') }}', 'replies-container-{{ $parent->id }}', true)"
                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 transition cursor-pointer">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                </svg>
                Balas
            </button>
        </div>
        @endauth
    </div>
</div>

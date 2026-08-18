@extends('layouts.public')
@section('title', $thread->judul)

@section('content')
<section class="pt-32 pb-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Top Action Bar (Always visible) --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Forum
            </a>

            @can('manage-forum')
            <div class="flex items-center gap-2">
                <form action="{{ route('forum.pin', $thread->id) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md font-semibold border transition {{ $thread->is_pinned ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 border-yellow-300 dark:border-yellow-700' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                        {{ $thread->is_pinned ? 'Unpin' : 'Pin' }}
                    </button>
                </form>
                <form action="{{ route('forum.lock', $thread->id) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md font-semibold border transition {{ $thread->is_locked ? 'bg-orange-100 dark:bg-orange-900/40 text-orange-800 dark:text-orange-300 border-orange-300 dark:border-orange-700' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        {{ $thread->is_locked ? 'Buka Kunci' : 'Kunci' }}
                    </button>
                </form>
                <form action="{{ route('forum.destroy', $thread->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus topik ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md font-semibold bg-red-600 hover:bg-red-700 text-white transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
            @endcan
        </div>

        {{-- Thread Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden mb-8 {{ $thread->is_pinned ? 'ring-2 ring-yellow-400 dark:ring-yellow-500' : '' }}">
            <div class="p-6 sm:p-8">
                {{-- Meta badges --}}
                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-4">
                    @if($thread->is_pinned)
                    <span class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" /></svg>
                        Pinned
                    </span>
                    @endif
                    @if($thread->is_locked)
                    <span class="bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                        Dikunci
                    </span>
                    @endif
                    @if($thread->category)
                    <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded text-xs font-medium">
                        {{ $thread->category->nama_kategori }}
                    </span>
                    @endif
                    {{-- Date (no clock icon) --}}
                    <span class="text-xs">
                        {{ \Carbon\Carbon::parse($thread->created_at)->translatedFormat('d M Y, H:i') }}
                    </span>
                    {{-- Views count — inline with date --}}
                    <span class="flex items-center gap-1 text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        {{ $thread->views_count }} tayangan
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 mb-6">{{ $thread->judul }}</h1>

                <div class="prose prose-slate dark:prose-invert max-w-none mb-8 text-slate-700 dark:text-slate-300">
                    {!! nl2br(e($thread->konten)) !!}
                </div>

                {{-- Author --}}
                <div class="flex items-center gap-3 pt-5 border-t border-slate-100 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-inner shrink-0">
                        @if($thread->user->foto_profil ?? null)
                            <img src="{{ asset('storage/' . $thread->user->foto_profil) }}" class="w-full h-full object-cover rounded-full">
                        @else
                            {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 text-sm">{{ $thread->user->name ?? 'Anonymous' }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $thread->user->instansi ?? 'BRIN' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Replies Header --}}
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">
            {{ $replies->total() }} Balasan
        </h3>

        {{-- Main Reply Form --}}
        @if(!$thread->is_locked)
            @auth
            <div x-data="replySystem()" class="space-y-4 mb-8">

                {{-- Each top-level reply --}}
                @foreach($replies as $reply)
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">
                    @include('partials.forum-reply-item', ['reply' => $reply, 'depth' => 0, 'thread' => $thread])

                    {{-- Nested (child) replies --}}
                    @if($reply->replies->count() > 0)
                    <div class="mt-4 ml-10 space-y-3 border-l-2 border-slate-100 dark:border-slate-700 pl-4">
                        @foreach($reply->replies as $childReply)
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-100 dark:border-slate-600 p-4">
                            @include('partials.forum-reply-item', ['reply' => $childReply, 'depth' => 1, 'thread' => $thread])

                            {{-- Grandchild replies --}}
                            @if($childReply->replies->count() > 0)
                            <div class="mt-3 ml-8 space-y-2 border-l-2 border-slate-100 dark:border-slate-700 pl-3">
                                @foreach($childReply->replies as $grandReply)
                                <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-600 p-3">
                                    @include('partials.forum-reply-item', ['reply' => $grandReply, 'depth' => 2, 'thread' => $thread])
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $replies->links() }}
                </div>

                {{-- Floating reply form (shown when replying to a comment) --}}
                <div x-show="activeReplyId !== null" x-transition class="sticky bottom-4 bg-white dark:bg-slate-800 border border-primary-300 dark:border-primary-600 rounded-xl shadow-xl p-4 mt-4" style="display: none;">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">
                            Membalas: <span x-text="replyingToName" class="text-slate-700 dark:text-slate-300"></span>
                        </span>
                        <button @click="cancelReply()" class="text-xs text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition">Batal ✕</button>
                    </div>
                    <form :action="'{{ route('forum.reply', $thread->id) }}'" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" :value="activeReplyId">
                        <input type="hidden" name="mention_user" :value="replyingToName">
                        <textarea name="konten" x-ref="replyTextarea" rows="3" required
                            placeholder="Tulis balasan Anda..."
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-primary-500 focus:ring-primary-500 text-sm transition resize-none"></textarea>
                        <div class="flex justify-end mt-2">
                            <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">
                                Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Root-level reply form (shown when not replying to a specific comment) --}}
                <div x-show="activeReplyId === null" x-transition class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">
                    <h4 class="font-bold text-slate-800 dark:text-slate-100 mb-3 text-sm">Tambahkan Balasan</h4>
                    <form action="{{ route('forum.reply', $thread->id) }}" method="POST">
                        @csrf
                        <textarea name="konten" rows="4" required
                            placeholder="Tuliskan jawaban atau pendapat Anda di sini..."
                            class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-primary-500 focus:ring-primary-500 transition text-sm resize-none"></textarea>
                        <div class="text-right mt-3">
                            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition text-sm">
                                Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @else
            {{-- Replies list (no form for guests) --}}
            <div class="space-y-4 mb-8">
                @foreach($replies as $reply)
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">
                    @include('partials.forum-reply-item', ['reply' => $reply, 'depth' => 0, 'thread' => $thread])
                    @if($reply->replies->count() > 0)
                    <div class="mt-4 ml-10 space-y-3 border-l-2 border-slate-100 dark:border-slate-700 pl-4">
                        @foreach($reply->replies as $childReply)
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-100 dark:border-slate-600 p-4">
                            @include('partials.forum-reply-item', ['reply' => $childReply, 'depth' => 1, 'thread' => $thread])
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
                <div class="mt-4">{{ $replies->links() }}</div>
            </div>

            <div class="bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 text-center">
                <p class="text-slate-600 dark:text-slate-400 mb-3 text-sm">Silakan masuk ke akun Anda untuk ikut berdiskusi.</p>
                <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-slate-800 dark:bg-primary-600 hover:bg-slate-900 dark:hover:bg-primary-700 text-white font-medium rounded-lg text-sm transition">Masuk / Login</a>
            </div>
            @endauth
        @else
            {{-- Replies list when locked --}}
            <div class="space-y-4 mb-6">
                @foreach($replies as $reply)
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">
                    @include('partials.forum-reply-item', ['reply' => $reply, 'depth' => 0, 'thread' => $thread])
                    @if($reply->replies->count() > 0)
                    <div class="mt-4 ml-10 space-y-3 border-l-2 border-slate-100 dark:border-slate-700 pl-4">
                        @foreach($reply->replies as $childReply)
                        <div class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-100 dark:border-slate-600 p-4">
                            @include('partials.forum-reply-item', ['reply' => $childReply, 'depth' => 1, 'thread' => $thread])
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
                <div class="mt-4">{{ $replies->links() }}</div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-6 py-4 rounded-xl text-center font-medium flex items-center justify-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                Diskusi ini telah ditutup oleh Moderator. Anda tidak dapat menambahkan balasan baru.
            </div>
        @endif
        
    </div>
</section>

<script>
function replySystem() {
    return {
        activeReplyId: null,
        replyingToName: '',
        setReply(replyId, userName) {
            this.activeReplyId = replyId;
            this.replyingToName = userName;
            this.$nextTick(() => {
                this.$refs.replyTextarea && this.$refs.replyTextarea.focus();
            });
            // Scroll to the reply form
            setTimeout(() => {
                document.querySelector('[x-ref="replyTextarea"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        },
        cancelReply() {
            this.activeReplyId = null;
            this.replyingToName = '';
        }
    }
}
</script>
@endsection


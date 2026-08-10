@extends('layouts.public')
@section('title', $thread->judul)

@section('content')
<section class="py-12 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
            <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary-600 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Forum
            </a>
            
            @can('manage-forum')
            <div class="flex items-center gap-2">
                <form action="{{ route('forum.pin', $thread->id) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-sm px-3 py-1.5 rounded-md font-medium border {{ $thread->is_pinned ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                        {{ $thread->is_pinned ? 'Unpin' : 'Pin' }}
                    </button>
                </form>
                <form action="{{ route('forum.lock', $thread->id) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-sm px-3 py-1.5 rounded-md font-medium border {{ $thread->is_locked ? 'bg-red-100 text-red-800 border-red-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                        {{ $thread->is_locked ? 'Buka Kunci' : 'Kunci' }}
                    </button>
                </form>
                <form action="{{ route('forum.destroy', $thread->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus topik ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm px-3 py-1.5 rounded-md font-medium bg-red-600 text-white hover:bg-red-700">
                        Hapus
                    </button>
                </form>
            </div>
            @endcan
        </div>

        <!-- Thread Utama -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8 {{ $thread->is_pinned ? 'ring-2 ring-yellow-400' : '' }}">
            <div class="p-6 sm:p-8">
                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 mb-4">
                    @if($thread->is_pinned)
                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" /></svg>
                        Pinned
                    </span>
                    @endif
                    @if($thread->is_locked)
                    <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                        Dikunci
                    </span>
                    @endif
                    @if($thread->category)
                    <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded">
                        {{ $thread->category->nama_kategori }}
                    </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ \Carbon\Carbon::parse($thread->created_at)->translatedFormat('d M Y, H:i') }}
                    </span>
                    <span class="flex items-center gap-1 ml-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        {{ $thread->views_count }} tayangan
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-6">{{ $thread->judul }}</h1>

                <div class="prose prose-slate max-w-none mb-8">
                    {!! nl2br(e($thread->konten)) !!}
                </div>

                <!-- Author Info -->
                <div class="flex items-center gap-3 pt-6 border-t border-slate-100">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-secondary-500 rounded-full flex items-center justify-center text-white font-bold shadow-inner">
                        {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-slate-800">{{ $thread->user->name ?? 'Anonymous' }}</div>
                        <div class="text-xs text-slate-500">{{ $thread->user->instansi ?? 'BRIN' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jawaban / Balasan -->
        <h3 class="text-lg font-bold text-slate-800 mb-4">{{ $replies->total() }} Balasan</h3>
        
        <div class="space-y-4 mb-8">
            @foreach($replies as $reply)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex gap-4">
                <!-- Avatar -->
                <div class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center text-slate-600 font-bold shrink-0">
                    {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                </div>
                
                <div class="flex-grow">
                    <div class="flex flex-wrap justify-between items-center gap-2 mb-2">
                        <div class="font-semibold text-slate-800">
                            {{ $reply->user->name ?? 'Anonymous' }}
                            <span class="text-xs text-slate-400 font-normal ml-2">{{ \Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}</span>
                        </div>
                        
                        @can('manage-forum')
                        <form action="{{ route('forum.reply.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus balasan ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                    
                    <div class="text-slate-700">
                        {!! nl2br(e($reply->konten)) !!}
                    </div>
                </div>
            </div>
            @endforeach
            
            <div class="mt-4">
                {{ $replies->links() }}
            </div>
        </div>

        <!-- Form Tambah Balasan -->
        @if(!$thread->is_locked)
            @auth
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h4 class="font-bold text-slate-800 mb-4">Tambahkan Balasan</h4>
                <form action="{{ route('forum.reply', $thread->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="konten" rows="4" required placeholder="Tuliskan jawaban atau pendapat Anda di sini..." class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-primary-500 transition"></textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition">
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="bg-slate-100 rounded-xl border border-slate-200 p-6 text-center">
                <p class="text-slate-600 mb-3">Silakan masuk ke akun Anda untuk ikut berdiskusi.</p>
                <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white font-medium rounded-lg">Masuk / Login</a>
            </div>
            @endauth
        @else
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl text-center font-medium flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                Diskusi ini telah ditutup oleh Moderator. Anda tidak dapat menambahkan balasan baru.
            </div>
        @endif
        
    </div>
</section>
@endsection

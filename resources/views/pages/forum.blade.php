@extends('layouts.public')
@section('title', 'Forum Diskusi')

@section('content')
<!-- Header -->
<section class="hero-gradient py-12">
    <div class="container mx-auto px-4">
        <nav class="flex text-sm text-white/70 mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center hover:text-white transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <span class="text-white font-medium ml-1">Forum</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-4xl font-bold text-white mb-2">Forum Diskusi SPBE</h1>
        <p class="text-lg text-white/80">Ruang kolaborasi, tanya jawab, dan berbagi pengetahuan antar pengguna.</p>
    </div>
</section>

<section class="py-8 container mx-auto px-4 max-w-5xl">
    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <!-- Filters -->
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            @php
                $sort = request('sort', 'terbaru');
            @endphp
            <a href="{{ url('/forum?sort=terbaru') }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ $sort == 'terbaru' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                Terbaru
            </a>
            <a href="{{ url('/forum?sort=terpopuler') }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ $sort == 'terpopuler' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                Terpopuler
            </a>
            <a href="{{ url('/forum?sort=belum_dijawab') }}" class="px-4 py-2 rounded-full text-sm font-medium transition {{ $sort == 'belum_dijawab' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                Belum Dijawab
            </a>
        </div>
        
        <!-- Action Button -->
        <div class="w-full md:w-auto text-right">
            @auth
                <a href="{{ url('/forum/create') }}" class="inline-flex justify-center items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg font-semibold transition shadow-md w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Buat Topik Baru
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 px-5 py-2.5 rounded-lg font-semibold transition shadow-sm w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                    Login untuk Berdiskusi
                </a>
            @endauth
        </div>
    </div>

    <!-- Thread List -->
    <div class="space-y-4">
        @if(isset($threads) && $threads->count() > 0)
            @foreach($threads as $thread)
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border {{ $thread->is_pinned ? 'border-yellow-400 dark:border-yellow-600' : 'border-slate-100 dark:border-slate-700' }} card-hover transition">
                <div class="flex items-start gap-4">
                    <!-- Avatar placeholder -->
                    <div class="hidden sm:flex w-12 h-12 bg-gradient-to-br from-primary-400 to-secondary-500 rounded-full items-center justify-center text-white font-bold text-lg shrink-0 shadow-inner">
                        {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                    </div>
                    
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            @if($thread->is_pinned)
                            <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" /></svg>
                                Pinned
                            </span>
                            @endif
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white truncate">
                                <a href="{{ url('/forum/'.$thread->id) }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ $thread->judul }}</a>
                            </h3>
                        </div>
                        
                        <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-2 mb-3">
                            {{ Str::limit(strip_tags($thread->konten), 100) }}
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                            <span class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-300">
                                <div class="sm:hidden w-5 h-5 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-bold text-[10px]">
                                    {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                                </div>
                                {{ $thread->user->name ?? 'Anonymous' }}
                            </span>
                            
                            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full hidden sm:block"></span>
                            
                            @if($thread->category)
                            <span class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-slate-600 dark:text-slate-300">
                                {{ $thread->category->nama_kategori }}
                            </span>
                            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full hidden sm:block"></span>
                            @endif
                            
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $thread->created_at ? \Carbon\Carbon::parse($thread->created_at)->diffForHumans() : '' }}
                            </span>
                            
                            <div class="flex items-center gap-3 ml-auto">
                                <span class="flex items-center gap-1 {{ ($thread->replies_count > 0) ? 'text-primary-600 dark:text-primary-400 font-semibold' : '' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    {{ $thread->replies_count ?? 0 }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    {{ $thread->views_count ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $threads->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <svg class="mx-auto h-16 w-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Belum ada topik diskusi</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6">Jadilah yang pertama memulai diskusi di forum ini!</p>
                @auth
                    <a href="{{ url('/forum/create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">
                        Buat Topik Baru
                    </a>
                @endauth
            </div>
        @endif
    </div>
</section>
@endsection

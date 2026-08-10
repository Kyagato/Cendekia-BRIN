@extends('layouts.public')
@section('title', $category->nama_kategori ?? 'Kategori')

@section('content')
<!-- Header -->
<section class="hero-gradient py-12 mb-8">
    <div class="container mx-auto px-4">
        <nav class="flex text-sm text-white/70 mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center hover:text-white transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <a href="{{ url('/kategori') }}" class="hover:text-white transition">Kategori</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        <span class="text-white font-medium ml-1">{{ $category->nama_kategori ?? 'Detail' }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white border border-white/30">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-white">{{ $category->nama_kategori ?? 'Kategori' }}</h1>
                <p class="text-white/80 mt-1">{{ $knowledge->total() ?? 0 }} dokumen tersedia</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-8 container mx-auto px-4">
    @if(isset($knowledge) && $knowledge->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
            @foreach($knowledge as $item)
                @include('components.knowledge-card', ['item' => $item])
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $knowledge->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Kategori Masih Kosong</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6 px-4">Belum ada pengetahuan yang dipublikasikan pada kategori ini.</p>
            <a href="{{ url('/kategori') }}" class="inline-flex justify-center items-center gap-2 bg-primary-100 dark:bg-primary-900/50 hover:bg-primary-200 dark:hover:bg-primary-900 text-primary-700 dark:text-primary-400 px-6 py-2.5 rounded-lg font-semibold transition">
                Kembali ke Semua Kategori
            </a>
        </div>
    @endif
</section>
@endsection

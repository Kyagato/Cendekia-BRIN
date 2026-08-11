@extends('layouts.public')
@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="relative pt-32 pb-24 hero-gradient overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-secondary-500/20 rounded-full blur-3xl mix-blend-multiply opacity-50 dark:opacity-30"></div>
        <div class="absolute top-1/4 -right-24 w-[30rem] h-[30rem] bg-primary-400/20 rounded-full blur-3xl mix-blend-multiply opacity-50 dark:opacity-30"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight drop-shadow-sm">
            <span class="text-black dark:text-white">Cendekia BRIN</span>
        </h1>
        <h2 class="text-2xl md:text-3xl font-semibold text-white/90 mb-6 drop-shadow">
            Sistem Informasi Manajemen Pengetahuan
        </h2>
        <p class="text-lg md:text-xl text-white/80 max-w-3xl mx-auto mb-10 leading-relaxed font-light">
            Platform terpadu untuk mengelola, berbagi, dan menemukan pengetahuan, dokumen, serta informasi strategis di lingkungan Badan Riset dan Inovasi Nasional.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ url('/kategori') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-primary-700 hover:bg-primary-50 rounded-xl font-bold transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Jelajahi Pengetahuan
            </a>
            <a href="{{ url('/tentang') }}" class="w-full sm:w-auto px-8 py-4 bg-transparent border-2 border-white text-white hover:bg-white/10 rounded-xl font-bold transition">
                Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="relative z-20 -mt-10 mb-16 container mx-auto px-4" 
    x-data="{ 
        query: '', 
        results: [], 
        loading: false, 
        showResults: false,
        async fetchResults() {
            if(this.query.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }
            this.loading = true;
            try {
                const res = await fetch('/api/search?q=' + encodeURIComponent(this.query));
                this.results = await res.json();
                this.showResults = true;
            } catch(e) {
                console.error(e);
            }
            this.loading = false;
        }
    }">
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('search.index') }}" method="GET" class="glass bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl p-2 rounded-2xl shadow-2xl border border-white/20 dark:border-slate-700 flex items-center relative">
            <div class="pl-4 text-slate-500 dark:text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" 
                   name="q"
                   x-model="query" 
                   @input.debounce.300ms="fetchResults" 
                   @focus="if(query.length > 0) showResults = true" 
                   @click.away="showResults = false"
                   placeholder="Cari pengetahuan, dokumen, atau topik..." 
                   class="w-full bg-transparent border-none focus:ring-0 text-slate-800 dark:text-slate-100 px-4 py-3 text-lg placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none">
            
            <div x-show="loading" class="pr-4 text-primary-600 dark:text-primary-400" style="display: none;">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-xl font-semibold transition shadow-md hidden sm:block">
                Cari
            </button>
        </form>

        <!-- Search Results Dropdown -->
        <div x-show="showResults" x-transition class="absolute left-0 right-0 max-w-4xl mx-auto mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden z-30" style="display: none;">
            <div class="max-h-96 overflow-y-auto">
                <template x-if="results.length === 0 && !loading">
                    <div class="p-4 text-center text-slate-500 dark:text-slate-400">Tidak ada hasil ditemukan</div>
                </template>
                <template x-for="item in results" :key="item.id">
                    <a :href="'/knowledge/' + item.id" class="flex items-start p-4 hover:bg-slate-50 dark:hover:bg-slate-750 border-b border-slate-50 dark:border-slate-700 transition">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full mr-3 mt-0.5 whitespace-nowrap"
                              :class="{
                                  'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300': item.tipe === 'Teks',
                                  'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': item.tipe === 'Video',
                                  'bg-secondary-100 text-secondary-700 dark:bg-secondary-900 dark:text-secondary-300': item.tipe === 'Gambar',
                                  'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300': item.tipe === 'Audio'
                              }" x-text="item.tipe">
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white" x-text="item.judul"></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" x-text="item.kategori?.nama_kategori"></p>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>
</section>

<!-- Paling Banyak Dilihat Section -->
<section class="py-12 container mx-auto px-4 scroll-reveal">
    <div class="flex items-center gap-3 mb-8">
        <div class="h-8 w-2 bg-primary-500 rounded-full"></div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Paling Banyak Dilihat</h2>
    </div>

    @if($mostViewed->isEmpty())
        <div class="text-center py-12 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
            <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-slate-500 dark:text-slate-400">Belum ada konten saat ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($mostViewed as $item)
                @include('components.knowledge-card', ['item' => $item])
            @endforeach
        </div>
    @endif
</section>

<!-- Terbaru Section with Tabs -->
<section class="py-12 bg-slate-50 dark:bg-slate-900/50 scroll-reveal" x-data="{ activeTab: 'Semua' }">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="flex items-center gap-3">
                <div class="h-8 w-2 bg-secondary-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Pengetahuan Terbaru</h2>
            </div>
            
            <!-- Tabs -->
            <div class="flex flex-wrap gap-2 bg-white dark:bg-slate-800 p-1.5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
                @foreach(['Semua', 'Teks', 'Video', 'Gambar', 'Audio'] as $tab)
                    <button @click="activeTab = '{{ $tab }}'"
                            :class="activeTab === '{{ $tab }}' ? 'bg-primary-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($latest as $item)
                <div x-show="activeTab === 'Semua' || activeTab === '{{ $item->tipe }}'" x-transition.opacity.duration.300ms>
                    @include('components.knowledge-card', ['item' => $item])
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-10">
            <a href="{{ url('/kategori') }}" class="inline-flex items-center text-primary-600 dark:text-primary-400 font-semibold hover:underline">
                Lihat Semua Pengetahuan
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </div>
</section>

<!-- Kategori & Label Populer -->
<section class="py-12 container mx-auto px-4 scroll-reveal">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Kategori Populer -->
        <div class="lg:col-span-3">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-2 bg-purple-500 rounded-full"></div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Kategori Populer</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($popularCategories as $cat)
                <a href="{{ url('/kategori/' . $cat->id) }}" class="group bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 card-hover flex items-center gap-4 transition-all hover:border-primary-300 dark:hover:border-primary-600 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-primary-50 to-transparent dark:via-primary-900/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400 shrink-0 relative z-10">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                    </div>
                    <div class="relative z-10 overflow-hidden">
                        <h3 class="font-semibold text-slate-800 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $cat->nama_kategori }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $cat->knowledge_count }} dokumen</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Label Populer -->
        <div class="lg:col-span-1">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-2 bg-yellow-500 rounded-full"></div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Label Populer</h2>
            </div>
            
            <div class="flex flex-wrap gap-2">
                @foreach($popularTags as $tag)
                <a href="{{ url('/kategori?label=' . urlencode($tag->nama_label)) }}" 
                   class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/50 text-slate-700 dark:text-slate-300 hover:text-primary-700 dark:hover:text-primary-400 text-sm rounded-full transition-colors border border-transparent hover:border-primary-200 dark:hover:border-primary-700">
                    #{{ $tag->nama_label }} 
                    <span class="text-xs text-slate-400 ml-1">({{ $tag->knowledge_count }})</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection

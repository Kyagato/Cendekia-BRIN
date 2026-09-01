@extends('layouts.public')
@section('title', 'Kategori & Repositori')

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
                        <span class="text-white font-medium ml-1">Kategori & Repositori</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-4xl font-bold text-white mb-2">Repositori Pengetahuan</h1>
        <p class="text-lg text-white/80">Jelajahi dan temukan informasi yang Anda butuhkan.</p>
    </div>
</section>

@guest
<!-- Banner Login -->
<div class="bg-secondary-600 text-white px-4 py-3 shadow-md">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-sm font-medium">Login untuk mengakses fitur lengkap seperti menyimpan favorit dan berdiskusi di forum.</p>
        </div>
        <a href="{{ route('login') }}" class="px-5 py-2 bg-white text-secondary-700 hover:bg-slate-50 rounded-lg text-sm font-bold transition whitespace-nowrap">
            Login Sekarang
        </a>
    </div>
</div>
@endguest

<!-- Main Content -->
<section class="py-8 container mx-auto px-4">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 mb-8 sticky top-4 z-30">
        <form action="{{ route('search.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full flex flex-col md:flex-row gap-4">
                <!-- Search Input -->
                <!-- Search Input with Autocomplete -->
                <div class="relative w-full md:w-96" x-data="{
                    query: '{{ request('q') ?? request('search') }}',
                    suggestions: [],
                    showSuggestions: false,
                    selectedIndex: -1,
                    debounceTimer: null,
                    fetchSuggestions() {
                        clearTimeout(this.debounceTimer);
                        if (this.query.length < 2) {
                            this.suggestions = [];
                            this.showSuggestions = false;
                            return;
                        }
                        this.debounceTimer = setTimeout(() => {
                            fetch('/api/search/autocomplete?q=' + encodeURIComponent(this.query))
                                .then(r => r.json())
                                .then(data => {
                                    this.suggestions = data.slice(0, 8);
                                    this.showSuggestions = this.suggestions.length > 0;
                                    this.selectedIndex = -1;
                                })
                                .catch(() => { this.suggestions = []; this.showSuggestions = false; });
                        }, 250);
                    },
                    selectSuggestion(item) {
                        this.query = item.judul;
                        this.showSuggestions = false;
                        this.$refs.searchInput.form.submit();
                    },
                    handleKeydown(e) {
                        if (!this.showSuggestions) return;
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            this.selectedIndex = Math.min(this.selectedIndex + 1, this.suggestions.length - 1);
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                        } else if (e.key === 'Enter' && this.selectedIndex >= 0) {
                            e.preventDefault();
                            this.selectSuggestion(this.suggestions[this.selectedIndex]);
                        } else if (e.key === 'Escape') {
                            this.showSuggestions = false;
                        }
                    }
                }" @click.away="showSuggestions = false">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="q"
                           x-ref="searchInput"
                           x-model="query"
                           @input="fetchSuggestions()"
                           @keydown="handleKeydown($event)"
                           @focus="if (suggestions.length > 0) showSuggestions = true"
                           placeholder="Cari di repositori..."
                           autocomplete="off"
                           class="w-full pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">

                    <!-- Autocomplete Dropdown -->
                    <div x-show="showSuggestions"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-slate-200 dark:border-slate-700 z-50 overflow-hidden max-h-80 overflow-y-auto"
                         style="display: none;">
                        <template x-for="(item, index) in suggestions" :key="item.id">
                            <button type="button"
                                    @click="selectSuggestion(item)"
                                    @mouseenter="selectedIndex = index"
                                    :class="selectedIndex === index ? 'bg-primary-50 dark:bg-primary-900/30' : ''"
                                    class="w-full text-left px-4 py-2.5 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer border-b border-slate-100 dark:border-slate-700/50 last:border-b-0">
                                <!-- Icon tipe -->
                                <div class="shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-slate-800 dark:text-slate-100 truncate" x-text="item.judul"></div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                        <span x-text="item.tipe" class="bg-slate-100 dark:bg-slate-600 px-1.5 py-0.5 rounded text-xs"></span>
                                        <span x-show="item.category" x-text="item.category?.nama_kategori"></span>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
                
                <!-- Category Select -->
                <select name="kategori" class="w-full md:w-64 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    <option value="">Semua Kategori</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ request('kategori') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                
                @if(request('label'))
                    <input type="hidden" name="label" value="{{ request('label') }}">
                @endif
                
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    Filter
                </button>
            </div>
            
            <!-- Tipe Tabs -->
            <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                @php
                    $tipes = ['Semua' => '', 'Teks' => 'Teks', 'Video' => 'Video', 'Gambar' => 'Gambar', 'Audio' => 'Audio'];
                    $currentTipe = request('tipe', '');
                @endphp
                @foreach($tipes as $label => $val)
                    @php
                        $active = $currentTipe === $val;
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['tipe' => $val, 'page' => 1]) }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $active ? 'bg-primary-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left: Knowledge Grid (3/4) -->
        <div class="w-full lg:w-3/4">
            @if(isset($knowledge) && $knowledge->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($knowledge as $item)
                        @include('components.knowledge-card', ['item' => $item])
                    @endforeach
                </div>
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $knowledge->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                    <svg class="mx-auto h-16 w-16 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Tidak ada konten ditemukan</h3>
                    <p class="text-slate-500 dark:text-slate-400">Coba ubah kata kunci atau filter pencarian Anda.</p>
                    <a href="{{ url('/kategori') }}" class="mt-4 inline-block text-primary-600 dark:text-primary-400 font-medium hover:underline">Reset Filter</a>
                </div>
            @endif
        </div>

        <!-- Right: Sidebar (1/4) -->
        <div class="w-full lg:w-1/4 space-y-6">
            <!-- Tag Populer -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    Tag Populer
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags ?? [] as $tag)
                    <a href="{{ request()->fullUrlWithQuery(['label' => $tag->nama_label, 'page' => 1]) }}" 
                       class="px-2.5 py-1 text-xs font-medium rounded-md transition-colors 
                       {{ request('label') === $tag->nama_label ? 'bg-primary-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                        {{ $tag->nama_label }} 
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Kategori List -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                    Semua Kategori
                </h3>
                <ul class="space-y-2">
                    @foreach($categories ?? [] as $cat)
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['kategori' => $cat->id, 'page' => 1]) }}" 
                           class="flex justify-between items-center py-2 px-3 rounded-lg transition-colors
                           {{ request('kategori') == $cat->id ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span class="truncate pr-2">{{ $cat->nama_kategori }}</span>
                            <span class="bg-slate-100 dark:bg-slate-600 text-slate-500 dark:text-slate-300 py-0.5 px-2 rounded-full text-xs shrink-0">
                                {{ $cat->knowledge_count }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection

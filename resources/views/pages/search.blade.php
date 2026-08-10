@extends('layouts.public')
@section('title', request('q') ? 'Hasil Pencarian: ' . request('q') : 'Cari Pengetahuan')

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
                        <span class="text-white font-medium ml-1">Pencarian</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-4xl font-bold text-white mb-2">Pencarian Pengetahuan</h1>
        <p class="text-lg text-white/80">Temukan dokumen, artikel, panduan, dan diskusi di seluruh repositori Cendekia BRIN.</p>
    </div>
</section>

<!-- Search Bar (Sticky) -->
<div class="sticky top-0 z-30 bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
    <div class="container mx-auto px-4 py-4">
        <form action="{{ route('search.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berdasarkan judul, deskripsi, label, penulis, atau instansi..." class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-primary-500 focus:ring-primary-500 transition">
            </div>
            <div class="flex gap-2 flex-wrap">
                <select name="tipe" class="px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:border-primary-500 focus:ring-primary-500 transition text-sm">
                    <option value="">Semua Tipe</option>
                    @foreach(['Teks', 'Video', 'Gambar', 'Audio'] as $tipe)
                        <option value="{{ $tipe }}" {{ request('tipe') == $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
                    @endforeach
                </select>
                <select name="kategori" class="px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:border-primary-500 focus:ring-primary-500 transition text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('kategori') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
                <select name="sort" class="px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:border-primary-500 focus:ring-primary-500 transition text-sm">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terpopuler" {{ request('sort') == 'terpopuler' ? 'selected' : '' }}>Terpopuler</option>
                    <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>A — Z</option>
                    <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Z — A</option>
                </select>
                <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-sm transition">
                    Cari
                </button>
            </div>
        </form>
    </div>
</div>

<section class="py-8 container mx-auto px-4">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Results -->
        <div class="flex-grow">
            <!-- Result Summary -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    @if(request('q'))
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                            Hasil untuk "<span class="text-primary-600">{{ request('q') }}</span>"
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Ditemukan {{ $results->total() }} pengetahuan
                            @if($forumResults->count() > 0)
                                dan {{ $forumResults->count() }} topik forum
                            @endif
                        </p>
                    @else
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Seluruh Pengetahuan</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Menampilkan {{ $results->total() }} item</p>
                    @endif
                </div>
                @if(request()->hasAny(['q', 'tipe', 'kategori', 'label', 'sort']))
                    <a href="{{ route('search.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Reset Filter
                    </a>
                @endif
            </div>

            <!-- Active Filters (Chips) -->
            @if(request()->hasAny(['tipe', 'kategori', 'label']))
            <div class="flex flex-wrap gap-2 mb-6">
                @if(request('tipe'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300">
                        Tipe: {{ request('tipe') }}
                        <a href="{{ request()->fullUrlWithoutQuery('tipe') }}" class="hover:text-primary-900"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                    </span>
                @endif
                @if(request('kategori'))
                    @php $activeCategory = $categories->find(request('kategori')); @endphp
                    @if($activeCategory)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                        Kategori: {{ $activeCategory->nama_kategori }}
                        <a href="{{ request()->fullUrlWithoutQuery('kategori') }}" class="hover:text-slate-900"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                    </span>
                    @endif
                @endif
                @if(request('label'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                        Label: {{ request('label') }}
                        <a href="{{ request()->fullUrlWithoutQuery('label') }}" class="hover:text-green-900"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></a>
                    </span>
                @endif
            </div>
            @endif

            <!-- Forum Results (If Applicable) -->
            @if(request('q') && $forumResults->count() > 0)
            <div class="mb-8">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                    Hasil dari Forum Diskusi
                </h3>
                <div class="space-y-3">
                    @foreach($forumResults as $thread)
                    <a href="{{ route('forum.show', $thread->id) }}" class="block bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-600 transition shadow-sm">
                        <h4 class="font-semibold text-slate-800 dark:text-white hover:text-primary-600 transition mb-1">{{ $thread->judul }}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">{{ Str::limit(strip_tags($thread->konten), 120) }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-slate-400">
                            <span>{{ $thread->user->name ?? 'Anonymous' }}</span>
                            <span>{{ $thread->replies_count }} balasan</span>
                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                <hr class="my-6 border-slate-200 dark:border-slate-700">
            </div>
            @endif

            <!-- Knowledge Results Grid -->
            @if($results->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($results as $item)
                <a href="{{ route('knowledge.show', $item->id) }}" class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-300">
                    <!-- Card Header (Tipe Badge) -->
                    <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            {{ $item->tipe === 'Teks' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : '' }}
                            {{ $item->tipe === 'Video' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' : '' }}
                            {{ $item->tipe === 'Gambar' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : '' }}
                            {{ $item->tipe === 'Audio' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300' : '' }}
                        ">
                            @if($item->tipe === 'Teks')
                                <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            @elseif($item->tipe === 'Video')
                                <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($item->tipe === 'Gambar')
                                <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            @else
                                <svg class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" /></svg>
                            @endif
                            {{ $item->tipe }}
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            {{ $item->views_count }}
                        </span>
                    </div>
                    <!-- Card Body -->
                    <div class="px-5 pb-2">
                        <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition line-clamp-2 leading-snug mb-2">{{ $item->judul }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-3">{{ Str::limit(strip_tags($item->deskripsi), 100) }}</p>
                    </div>
                    <!-- Tags -->
                    @if($item->tags->count() > 0)
                    <div class="px-5 pb-3 flex flex-wrap gap-1.5">
                        @foreach($item->tags->take(3) as $tag)
                            <span class="text-[0.65rem] px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded">{{ $tag->nama_label }}</span>
                        @endforeach
                        @if($item->tags->count() > 3)
                            <span class="text-[0.65rem] px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 rounded">+{{ $item->tags->count() - 3 }}</span>
                        @endif
                    </div>
                    @endif
                    <!-- Card Footer -->
                    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold text-[0.6rem]">
                                {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="truncate max-w-[120px]">{{ $item->user->name ?? 'Anonim' }}</span>
                        </div>
                        <span>{{ $item->created_at ? $item->created_at->diffForHumans() : '' }}</span>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $results->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <svg class="mx-auto h-20 w-20 text-slate-300 dark:text-slate-600 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Tidak ada hasil ditemukan</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                    @if(request('q'))
                        Tidak ditemukan pengetahuan yang cocok dengan pencarian "<strong>{{ request('q') }}</strong>". Coba gunakan kata kunci yang berbeda atau perluas filter Anda.
                    @else
                        Belum ada pengetahuan yang tersedia. Coba ubah filter pencarian Anda.
                    @endif
                </p>
                <a href="{{ route('search.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Reset Pencarian
                </a>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <aside class="w-full lg:w-80 shrink-0 space-y-6">
            <!-- Kategori -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Kategori
                </h3>
                <ul class="space-y-1">
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('search.index', array_merge(request()->all(), ['kategori' => $cat->id])) }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ request('kategori') == $cat->id ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span class="truncate">{{ $cat->nama_kategori }}</span>
                            <span class="text-xs bg-slate-100 dark:bg-slate-600 text-slate-500 dark:text-slate-300 px-2 py-0.5 rounded-full">{{ $cat->knowledge_count }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Filter Tipe -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Tipe Konten
                </h3>
                <div class="space-y-1">
                    @foreach(['Teks', 'Video', 'Gambar', 'Audio'] as $tipe)
                    <a href="{{ route('search.index', array_merge(request()->all(), ['tipe' => $tipe])) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition {{ request('tipe') == $tipe ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        @if($tipe === 'Teks')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        @elseif($tipe === 'Video')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @elseif($tipe === 'Gambar')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" /></svg>
                        @endif
                        {{ $tipe }}
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Label Populer -->
            @if($popularTags->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    Label Populer
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($popularTags as $tag)
                    <a href="{{ route('search.index', ['label' => $tag->nama_label]) }}" class="text-sm px-3 py-1 rounded-full transition {{ request('label') == $tag->nama_label ? 'bg-primary-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-primary-100 hover:text-primary-700 dark:hover:bg-primary-900/50 dark:hover:text-primary-300' }}">
                        {{ $tag->nama_label }}
                        <span class="text-xs opacity-75">({{ $tag->knowledge_count }})</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </aside>
    </div>
</section>
@endsection

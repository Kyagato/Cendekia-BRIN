@extends('layouts.admin')
@section('title', 'Forum Saya')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Forum Saya</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ viewMode: '{{ ($errors->any() || request('action') === 'create') ? 'create' : 'list' }}' }">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3 text-sm shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Stats Cards (Hanya tampil saat mode list) --}}
    <div x-show="viewMode === 'list'" x-transition class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Topik Saya</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $counts['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Disetujui / Tayang</div>
            <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 mt-1">{{ $counts['approved'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-amber-600 dark:text-amber-400">Menunggu Review</div>
            <div class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">{{ $counts['pending'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-red-600 dark:text-red-400">Ditolak</div>
            <div class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">{{ $counts['rejected'] }}</div>
        </div>
    </div>

    {{-- Main Container Card (LIST VIEW) --}}
    <div x-show="viewMode === 'list'" x-transition class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Daftar Topik Diskusi Saya</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola topik diskusi forum yang Anda buat</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.forum.trash') }}" class="inline-flex items-center gap-2 bg-red-50/50 hover:bg-red-50 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-600 dark:text-red-400 px-3.5 py-2 rounded-lg text-sm font-semibold transition border border-red-300 dark:border-red-800 shadow-xs">
                    <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Tong Sampah ({{ $counts['trashed'] }})
                </a>
                <button type="button" @click="viewMode = 'create'" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Topik Forum
                </button>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('dashboard.forum.index') }}" class="relative w-full md:w-80">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari topik diskusi..."
                       class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-primary-600 focus:border-primary-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
            </form>

            <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto">
                <a href="{{ route('dashboard.forum.index', request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ !request('status') ? 'bg-primary-50 dark:bg-slate-700 text-primary-600 dark:text-primary-400 border-primary-200 dark:border-slate-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Semua ({{ $counts['total'] }})
                </a>
                <a href="{{ route('dashboard.forum.index', array_merge(request()->all(), ['status' => 'approved'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('status') === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Disetujui ({{ $counts['approved'] }})
                </a>
                <a href="{{ route('dashboard.forum.index', array_merge(request()->all(), ['status' => 'pending'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('status') === 'pending' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Menunggu ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('dashboard.forum.index', array_merge(request()->all(), ['status' => 'rejected'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('status') === 'rejected' ? 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Ditolak ({{ $counts['rejected'] }})
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Judul Topik</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kategori</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Statistik</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Dibuat</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($threads as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                        <td class="py-4 px-6 max-w-sm">
                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-100 line-clamp-1">
                                {{ $item->judul }}
                            </div>
                            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">
                                {{ Str::limit(strip_tags($item->konten), 90) }}
                            </div>
                            @if($item->knowledge)
                            <div class="mt-1 text-[11px] text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                <span>🔗 Terkait:</span>
                                <span class="font-medium truncate">{{ $item->knowledge->judul }}</span>
                            </div>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600 dark:text-slate-300">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                {{ $item->category->nama_kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            @if($item->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Disetujui
                                </span>
                            @elseif($item->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-950 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800" title="{{ $item->rejection_note }}">
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400">
                            <div>{{ $item->views_count ?? 0 }} tayangan</div>
                            <div>{{ $item->replies_count ?? 0 }} balasan</div>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400">
                            {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end" x-data="{ open: false, showDeleteModal: false }">
                                <div class="relative inline-block text-left" @mouseenter="open = true" @mouseleave="open = false">
                                    {{-- Tombol Titik Tiga --}}
                                    <button @click="open = !open" type="button" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition focus:outline-none cursor-pointer">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>

                                    {{-- Menu Dropdown Hover --}}
                                    <div x-show="open" x-cloak x-transition
                                         class="absolute right-0 z-30 mt-1 w-40 origin-top-right rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 focus:outline-none py-1.5 divide-y divide-slate-100 dark:divide-slate-700/60"
                                         style="display: none;">
                                        
                                        <div class="py-1">
                                            {{-- 1. Lihat (Buka di halaman ini, bukan tab baru) --}}
                                            <a href="{{ route('forum.show', $item->id) }}"
                                               class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Lihat</span>
                                            </a>
                                        </div>

                                        <div class="py-1">
                                            {{-- 2. Hapus ke Tong Sampah --}}
                                            <button type="button" @click="showDeleteModal = true; open = false"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Konfirmasi Hapus ke Tong Sampah --}}
                                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm text-left">
                                    <div @click.outside="showDeleteModal = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md">
                                        <div class="p-6">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center shrink-0">
                                                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </div>
                                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Pindahkan ke Tong Sampah?</h3>
                                            </div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                                                Topik diskusi "<strong class="text-slate-800 dark:text-slate-200">{{ $item->judul }}</strong>" akan dipindahkan ke tong sampah forum Anda dan dapat dipulihkan kapan saja.
                                            </p>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="showDeleteModal = false"
                                                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                                    Batal
                                                </button>
                                                <form action="{{ route('forum.destroy', $item->id) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="from_dashboard" value="1">
                                                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Belum ada topik diskusi yang Anda buat</p>
                            <button type="button" @click="viewMode = 'create'" class="inline-block mt-3 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shadow-xs cursor-pointer">
                                Mulai Diskusi Sekarang
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($threads->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $threads->links() }}
        </div>
        @endif
    </div>

    {{-- VIEW 2: FORM TAMBAH TOPIK FORUM (IN-PAGE SWITCH VIEW SECARA UTUH) --}}
    <div x-show="viewMode === 'create'" x-cloak x-transition class="space-y-6" style="display: none;">
        {{-- Top Bar Navigation Form --}}
        <div class="flex items-center justify-between">
            <button type="button" @click="viewMode = 'list'" class="inline-flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 font-semibold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Daftar Topik
            </button>
        </div>

        {{-- Card Form Utama --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Buat Topik Diskusi Baru</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Mulai diskusi, ajukan pertanyaan, atau bagikan wawasan dengan komunitas SPBE langsung dari dashboard Anda.</p>
            </div>

            <div class="p-6 sm:p-8">
                <form action="{{ route('forum.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ref" value="dashboard">

                    {{-- Hubungkan ke Materi Pengetahuan (Single Search & Selection Autocomplete) --}}
                    <div class="mb-6 relative" x-data="{
                        open: false,
                        search: @js(old('knowledge_id') ? '' : ''),
                        selectedId: @js(old('knowledge_id', '')),
                        selectedTitle: '',
                        items: [
                            @foreach($knowledges as $k)
                                { id: {{ $k->id }}, judul: @js($k->judul), category_id: {{ $k->category_id }}, category_name: @js($k->category->nama_kategori ?? 'Umum') },
                            @endforeach
                        ],
                        get filteredItems() {
                            if (!this.search || this.search === this.selectedTitle) {
                                return this.items;
                            }
                            return this.items.filter(item => 
                                item.judul.toLowerCase().includes(this.search.toLowerCase()) ||
                                item.category_name.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        select(item) {
                            this.selectedId = item.id;
                            this.selectedTitle = item.judul;
                            this.search = item.judul;
                            this.open = false;
                            
                            const catSelect = document.getElementById('dashboard_category_id');
                            if (catSelect && item.category_id) {
                                catSelect.value = item.category_id;
                            }
                            
                            const judulInput = document.getElementById('dashboard_judul');
                            if (judulInput && (!judulInput.value || judulInput.value.startsWith('Diskusi: '))) {
                                judulInput.value = 'Diskusi: ' + item.judul;
                            }
                        },
                        clear() {
                            this.selectedId = '';
                            this.selectedTitle = '';
                            this.search = '';
                            this.open = false;
                        }
                    }">
                        <label for="dashboard_knowledge_search_input" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Hubungkan ke Materi Pengetahuan <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span>
                        </label>

                        <input type="hidden" name="knowledge_id" :value="selectedId">

                        <div class="relative" @click.away="open = false">
                            <div class="relative">
                                <input type="text" id="dashboard_knowledge_search_input" 
                                       x-model="search" 
                                       @focus="open = true" 
                                       @input="open = true; if(!search) clear()"
                                       placeholder="🔍 Cari dan pilih materi pengetahuan..." 
                                       autocomplete="off"
                                       class="w-full pl-10 pr-10 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition text-sm">
                                
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>

                                <button type="button" x-show="search" @click="clear()" class="absolute right-3 top-3.5 text-slate-400 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="open && filteredItems.length > 0" 
                                 x-transition
                                 class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto py-1 text-sm">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <div @click="select(item)" 
                                         class="px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer flex items-center justify-between transition border-b border-slate-100 dark:border-slate-700/60 last:border-0">
                                        <div>
                                            <span class="font-medium text-slate-800 dark:text-slate-100 block" x-text="item.judul"></span>
                                            <span class="text-xs text-slate-400 dark:text-slate-500" x-text="item.category_name"></span>
                                        </div>
                                        <span x-show="selectedId == item.id" class="text-primary-600 dark:text-primary-400 text-xs font-semibold">✓ Terpilih</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Judul Topik --}}
                    <div class="mb-6">
                        <label for="dashboard_judul" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Judul Topik Diskusi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="dashboard_judul" name="judul" value="{{ old('judul') }}" required
                               placeholder="Contoh: Diskusi mengenai Pedoman Arsitektur SPBE"
                               class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition text-sm">
                        @error('judul') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-6">
                        <label for="dashboard_category_id" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="dashboard_category_id" name="category_id" required
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-primary-600 focus:border-primary-600 transition text-sm">
                            <option value="" disabled {{ old('category_id') ? '' : 'selected' }} class="text-slate-400 dark:text-slate-500">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Konten / Pertanyaan --}}
                    <div class="mb-8">
                        <label for="dashboard_konten" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Konten / Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="dashboard_konten" name="konten" rows="8" required
                                  placeholder="Jelaskan secara detail topik diskusi atau pertanyaan Anda di sini..."
                                  class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition resize-y text-sm">{{ old('konten') }}</textarea>
                        @error('konten') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="viewMode = 'list'"
                                class="px-6 py-3 rounded-lg font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition text-sm cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center gap-2 cursor-pointer">
                            Kirim Topik
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

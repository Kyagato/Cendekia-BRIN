@extends('layouts.admin')
@section('title', 'Tong Sampah Forum')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li>
        <a href="{{ route('dashboard.forum.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition">Forum Saya</a>
    </li>
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Tong Sampah</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'list', activeItem: null, showForceDeleteModal: false, deleteTarget: null }">


    {{-- VIEW 1: DAFTAR TOPIK DI TONG SAMPAH (LIST VIEW) --}}
    <div x-show="viewMode === 'list'" x-transition class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Tong Sampah Forum Saya
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar topik diskusi Anda yang telah dihapus. Anda dapat memulihkannya kembali atau menghapusnya secara permanen.</p>
            </div>
            <a href="{{ route('dashboard.forum.index') }}" class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Forum Saya
            </a>
        </div>

        <!-- Search -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <form method="GET" action="{{ route('dashboard.forum.trash') }}" class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari di tong sampah..."
                       class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-primary-600 focus:border-primary-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
            </form>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total: {{ $trashedThreads->total() }} topik</span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Judul Topik</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kategori</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Waktu Dihapus</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($trashedThreads as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                        <td class="py-4 px-6 max-w-sm">
                            <button type="button"
                                    @click="activeItem = {
                                        id: {{ $item->id }},
                                        judul: @js($item->judul),
                                        konten: @js($item->konten),
                                        kategori: @js($item->category->nama_kategori ?? '-'),
                                        knowledge_id: @js($item->knowledge_id),
                                        knowledge_title: @js($item->knowledge->judul ?? null),
                                        deleted_at: @js($item->deleted_at ? $item->deleted_at->translatedFormat('d M Y, H:i') : '-'),
                                        created_at: @js($item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-'),
                                        views_count: {{ $item->views_count ?? 0 }},
                                        replies_count: {{ $item->replies_count ?? 0 }},
                                        restore_url: @js(route('dashboard.forum.restore', $item->id)),
                                        force_delete_url: @js(route('dashboard.forum.forceDelete', $item->id))
                                    }; viewMode = 'preview';"
                                    class="text-left font-semibold text-sm text-slate-800 dark:text-slate-100 hover:text-primary-600 dark:hover:text-primary-400 line-clamp-1 cursor-pointer transition">
                                {{ $item->judul }}
                            </button>
                            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">
                                {{ Str::limit(strip_tags($item->konten), 90) }}
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600 dark:text-slate-300">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                {{ $item->category->nama_kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400">
                            {{ $item->deleted_at ? $item->deleted_at->translatedFormat('d M Y, H:i') : '-' }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end" x-data="{ open: false }">
                                <div class="relative inline-block text-left" @mouseenter="open = true" @mouseleave="open = false">
                                    {{-- Tombol Titik Tiga --}}
                                    <button @click="open = !open" type="button" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition focus:outline-none cursor-pointer">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>

                                    {{-- Menu Dropdown Hover --}}
                                    <div x-show="open" x-cloak x-transition
                                         class="absolute right-0 z-30 mt-1 w-44 origin-top-right rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 focus:outline-none py-1.5 divide-y divide-slate-100 dark:divide-slate-700/60"
                                         style="display: none;">
                                        
                                        <div class="py-1">
                                             {{-- 1. Lihat (In-Page Switch View ke Form Detail Tong Sampah) --}}
                                            <button type="button"
                                                    @click="activeItem = {
                                                        id: {{ $item->id }},
                                                        judul: @js($item->judul),
                                                        konten: @js($item->konten),
                                                        kategori: @js($item->category->nama_kategori ?? '-'),
                                                        knowledge_id: @js($item->knowledge_id),
                                                        knowledge_title: @js($item->knowledge->judul ?? null),
                                                        deleted_at: @js($item->deleted_at ? $item->deleted_at->translatedFormat('d M Y, H:i') : '-'),
                                                        created_at: @js($item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-'),
                                                        views_count: {{ $item->views_count ?? 0 }},
                                                        replies_count: {{ $item->replies_count ?? 0 }},
                                                        restore_url: @js(route('dashboard.forum.restore', $item->id)),
                                                        force_delete_url: @js(route('dashboard.forum.forceDelete', $item->id))
                                                    }; viewMode = 'preview'; open = false;"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Lihat</span>
                                            </button>

                                            {{-- 2. Pulihkan --}}
                                            <form action="{{ route('dashboard.forum.restore', $item->id) }}" method="POST" class="block w-full">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 dark:hover:text-emerald-400 transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                    <span>Pulihkan</span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="py-1">
                                            {{-- 3. Hapus Permanen --}}
                                            <button type="button"
                                                    @click="deleteTarget = {
                                                        id: {{ $item->id }},
                                                        judul: @js($item->judul),
                                                        force_delete_url: @js(route('dashboard.forum.forceDelete', $item->id))
                                                    }; showForceDeleteModal = true; open = false;"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                <span>Hapus Permanen</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center">
                            <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Tong sampah forum kosong</p>
                            <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">Tidak ada topik diskusi Anda yang dihapus saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trashedThreads->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $trashedThreads->links() }}
        </div>
        @endif
    </div>

    {{-- VIEW 2: FORM / PRATINJAU DETAIL TOPIK TONG SAMPAH (IN-PAGE SWITCH VIEW SECARA UTUH) --}}
    <div x-show="viewMode === 'preview'" x-cloak x-transition class="space-y-6" style="display: none;">
        {{-- Top Bar Navigation Form --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <button type="button" @click="viewMode = 'list'" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-semibold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Tong Sampah Forum
            </button>
            <div class="flex items-center gap-3">
                <!-- Form Pulihkan -->
                <form :action="activeItem?.restore_url" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Pulihkan Topik Ini
                    </button>
                </form>
                <!-- Tombol Hapus Permanen -->
                <button type="button" @click="deleteTarget = activeItem; showForceDeleteModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Hapus Permanen
                </button>
            </div>
        </div>

        {{-- Banner Peringatan Status Tong Sampah --}}
        <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl flex items-center justify-between text-sm shadow-xs">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Topik diskusi ini sedang berada di <strong>Tong Sampah</strong>. Waktu dihapus: <span x-text="activeItem?.deleted_at" class="font-semibold"></span></span>
            </div>
        </div>

        {{-- Card Pratinjau Detail Konten Topik --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 sm:p-8 space-y-6">
                <!-- Meta Badges -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-full font-medium bg-red-100 dark:bg-red-950/50 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800">
                        Di Tong Sampah
                    </span>
                    <span class="px-2.5 py-1 rounded-full font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300" x-text="activeItem?.kategori">
                    </span>
                    <span class="text-slate-400 dark:text-slate-500">
                        Dibuat: <span x-text="activeItem?.created_at"></span>
                    </span>
                    <span class="text-slate-400 dark:text-slate-500">
                        • <span x-text="activeItem?.views_count"></span> tayangan
                    </span>
                    <span class="text-slate-400 dark:text-slate-500">
                        • <span x-text="activeItem?.replies_count"></span> balasan
                    </span>
                </div>

                <!-- Knowledge Linked Box jika ada -->
                <template x-if="activeItem?.knowledge_title">
                    <div class="p-4 bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block">Membahas Pengetahuan</span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200" x-text="activeItem?.knowledge_title"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Judul Topik -->
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-100" x-text="activeItem?.judul"></h1>

                <!-- Konten Topik -->
                <div class="border-t border-b border-slate-100 dark:border-slate-700/60 py-6">
                    <div class="prose prose-slate dark:prose-invert max-w-none text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line" x-text="activeItem?.konten"></div>
                </div>

                <!-- Footer Navigation -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
                    <button type="button" @click="viewMode = 'list'" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-semibold text-sm transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Tong Sampah Forum
                    </button>
                    <div class="flex items-center gap-3">
                        <form :action="activeItem?.restore_url" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-sm cursor-pointer flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                Pulihkan Topik
                            </button>
                        </form>
                        <button type="button" @click="deleteTarget = activeItem; showForceDeleteModal = true" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS PERMANEN (UNIVERSAL UNTUK LIST & PREVIEW) --}}
    <div x-show="showForceDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm text-left">
        <div @click.outside="showForceDeleteModal = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Permanen?</h3>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                    Topik diskusi "<strong class="text-slate-800 dark:text-slate-200" x-text="deleteTarget?.judul"></strong>" beserta seluruh balasannya akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showForceDeleteModal = false"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        Batal
                    </button>
                    <form :action="deleteTarget?.force_delete_url" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm cursor-pointer">Ya, Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

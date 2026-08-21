@extends('layouts.admin')
@section('title', 'Validasi Pengetahuan')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Validasi</li>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3 text-sm shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Validasi Pengetahuan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar pengajuan artikel pengetahuan yang memerlukan peninjauan, persetujuan, atau penolakan</p>
        </div>
    </div>

    {{-- Card Main Container --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        
        {{-- Search & Filter Bar --}}
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row gap-4 justify-between items-center">
            
            {{-- Search Form with Search Input & Status Filter Dropdown --}}
            <form method="GET" action="{{ route('validasi.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3 justify-between">
                
                {{-- Search Bar --}}
                <div class="relative w-full sm:w-80 flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pencarian..."
                           class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm focus:ring-red-600 focus:border-red-600 transition">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                </div>

                {{-- Status Filter Dropdown --}}
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <div class="relative inline-block text-left w-full sm:w-auto" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span>
                                    @if(request('status') === 'Diajukan')
                                        Status: Diajukan
                                    @elseif(request('status') === 'Disetujui')
                                        Status: Disetujui
                                    @elseif(request('status') === 'Ditolak')
                                        Status: Ditolak
                                    @else
                                        Status
                                    @endif
                                </span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-30 mt-2 w-48 rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 focus:outline-none">
                            <a href="{{ route('validasi.index', array_merge(request()->except('status'))) }}"
                               class="flex items-center justify-between px-4 py-2 text-sm font-semibold {{ !request('status') ? 'text-red-600 dark:text-red-400 bg-slate-50 dark:bg-slate-700/50' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700' }} transition">
                                <span>Semua Status</span>
                                @if(!request('status'))
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </a>
                            <a href="{{ route('validasi.index', array_merge(request()->query(), ['status' => 'Diajukan'])) }}"
                               class="flex items-center justify-between px-4 py-2 text-sm font-semibold {{ request('status') === 'Diajukan' ? 'text-blue-600 dark:text-blue-400 bg-slate-50 dark:bg-slate-700/50' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700' }} transition">
                                <span>Diajukan</span>
                                @if(request('status') === 'Diajukan')
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </a>
                            <a href="{{ route('validasi.index', array_merge(request()->query(), ['status' => 'Disetujui'])) }}"
                               class="flex items-center justify-between px-4 py-2 text-sm font-semibold {{ request('status') === 'Disetujui' ? 'text-emerald-600 dark:text-emerald-400 bg-slate-50 dark:bg-slate-700/50' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700' }} transition">
                                <span>Disetujui</span>
                                @if(request('status') === 'Disetujui')
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </a>
                            <a href="{{ route('validasi.index', array_merge(request()->query(), ['status' => 'Ditolak'])) }}"
                               class="flex items-center justify-between px-4 py-2 text-sm font-semibold {{ request('status') === 'Ditolak' ? 'text-rose-600 dark:text-rose-400 bg-slate-50 dark:bg-slate-700/50' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700' }} transition">
                                <span>Ditolak</span>
                                @if(request('status') === 'Ditolak')
                                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </a>
                        </div>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                        Cari
                    </button>
                </div>

            </form>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Thumbnail</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Judul</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tipe</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kategori</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Penulis</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Terbit</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($knowledges as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        {{-- Thumbnail --}}
                        <td class="py-3 px-6">
                            @if($item->file_path)
                                <div class="w-12 h-12 rounded overflow-hidden shadow-sm">
                                    <img src="{{ asset('storage/' . $item->file_path) }}" alt="Thumbnail" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded flex items-center justify-center text-slate-400 dark:text-slate-500">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                            @endif
                        </td>

                        {{-- Judul --}}
                        <td class="py-3 px-6">
                            <a href="{{ route('validasi.show', $item->id) }}" class="text-sm font-medium text-slate-800 dark:text-slate-100 hover:text-red-600 dark:hover:text-red-400 transition">
                                {{ $item->judul }}
                            </a>
                        </td>

                        {{-- Tipe --}}
                        <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">
                            {{ $item->tipe }}
                        </td>

                        {{-- Kategori --}}
                        <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">
                            {{ $item->category->nama_kategori ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="py-3 px-6">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border
                                {{ $item->status == 'Disetujui' ? 'bg-green-100 dark:bg-green-950 text-green-800 dark:text-green-300 border-green-200 dark:border-green-800' :
                                  ($item->status == 'Ditolak' ? 'bg-red-100 dark:bg-red-950 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800' :
                                  ($item->status == 'Diajukan' ? 'bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800' : 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800')) }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        {{-- Penulis --}}
                        <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">
                            {{ $item->penulis ?? $item->user->name ?? '-' }}
                        </td>

                        {{-- Terbit / Tanggal --}}
                        <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">
                            {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                        </td>

                        {{-- Aksi Menu Titik 3 (Kebab Dropdown) --}}
                        <td class="py-3 px-6 text-right">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false" type="button"
                                        class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded transition focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                                <div x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-20 mt-1 w-32 rounded-md bg-white dark:bg-slate-800 shadow-lg border border-slate-200 dark:border-slate-700 py-1 focus:outline-none">
                                    <a href="{{ route('validasi.show', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span>Validasi</span>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400 dark:text-slate-500 text-sm">
                            Tidak ada data pengajuan pengetahuan untuk divalidasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($knowledges->hasPages())
        <div class="p-5 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
            {{ $knowledges->links() }}
        </div>
        @endif

    </div>

</div>
@endsection

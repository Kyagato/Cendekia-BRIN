@extends('layouts.admin')
@section('title', 'Pengetahuan')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <!-- Header -->
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Daftar Pengetahuan</h1>
            <p class="text-sm text-slate-500 mt-1">Manajemen artikel pengetahuan</p>
        </div>
        <a href="{{ route('knowledge.create') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah
        </a>
    </div>

    <!-- Filters -->
    <div class="p-6 border-b border-slate-200 flex flex-col lg:flex-row justify-between items-center gap-4">
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Search -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" placeholder="Pencarian..." class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-red-600 focus:border-red-600">
            </div>

            <!-- Status -->
            <button class="inline-flex items-center gap-2 px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-600 bg-white hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Status
            </button>

            <!-- Type -->
            <button class="inline-flex items-center gap-2 px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-600 bg-white hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                Type
            </button>
        </div>

        <!-- Tampil Kolom -->
        <button class="inline-flex items-center gap-2 px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 font-medium bg-white hover:bg-slate-50 transition w-full lg:w-auto justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
            Tampil Kolom
        </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-slate-200">
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Thumbnail</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Judul</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Tipe</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Kategori</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Status</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Penulis</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Terbit</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($knowledges as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6">
                        @if($item->file_path)
                            <div class="w-12 h-12 rounded overflow-hidden shadow-sm">
                                <img src="{{ asset('storage/' . $item->file_path) }}" alt="Thumbnail" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center text-slate-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 px-6">
                        <div class="text-sm font-medium text-slate-800">{{ $item->judul }}</div>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->tipe }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->category->nama_kategori ?? '-' }}</td>
                    <td class="py-3 px-6">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                            {{ $item->status == 'Disetujui' ? 'bg-green-100 text-green-800' : 
                              ($item->status == 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                    <td class="py-3 px-6 text-right">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @mouseenter="open = true" @click="open = !open" type="button" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z" /></svg>
                            </button>
                            <div x-show="open" @mouseleave="open = false" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 z-50 mt-1 w-36 origin-top-right bg-white rounded-lg shadow-lg border border-slate-200 py-1" style="display: none;">
                                <a href="{{ route('knowledge.show', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Lihat
                                </a>
                                <a href="{{ route('knowledge.edit', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center">
                        <p class="text-slate-500 text-sm">Data tidak ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="p-4 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-600">
        <div>
            Menampilkan {{ $knowledges->firstItem() ?? 0 }} dari {{ $knowledges->lastItem() ?? 0 }} baris data.
        </div>
        
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-2">
                <span>Baris per halaman</span>
                <select class="border-slate-300 rounded px-2 py-1 text-sm focus:ring-red-600 focus:border-red-600">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>
            
            <div>
                Hal. {{ $knowledges->currentPage() }} dari {{ $knowledges->lastPage() }}
            </div>
            
            <div class="flex items-center gap-1">
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ $knowledges->onFirstPage() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                </button>
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ $knowledges->onFirstPage() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ !$knowledges->hasMorePages() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ !$knowledges->hasMorePages() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
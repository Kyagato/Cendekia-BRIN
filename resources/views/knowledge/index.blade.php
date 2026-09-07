@extends('layouts.admin')
@section('title', 'Pengetahuan')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <!-- Header -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Daftar Pengetahuan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manajemen artikel pengetahuan</p>
        </div>
        <a href="{{ route('knowledge.create') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah
        </a>
    </div>

    <!-- Filters -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row justify-between items-center gap-4">
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Search Form -->
            <form method="GET" action="{{ route('knowledge.index') }}" class="relative w-full sm:w-64">
                @if(request('tipe')) <input type="hidden" name="tipe" value="{{ request('tipe') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Pencarian..."
                       class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-red-600 focus:border-red-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
            </form>

            <!-- Filter Tipe -->
            <div x-data="{ open: false }" class="relative inline-block text-left">
                <button @click="open = !open" @click.away="open = false" type="button"
                        class="inline-flex items-center gap-2 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Tipe {{ request('tipe') ? ': ' . request('tipe') : '' }}
                </button>
                <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-40 origin-top-left rounded-md bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black ring-opacity-5 border border-slate-200 dark:border-slate-700 focus:outline-none" style="display: none;">
                    <div class="py-1">
                        <a href="{{ route('knowledge.index', array_merge(request()->except('tipe', 'page_knowledges'))) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == '' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Semua Tipe</a>
                        <a href="{{ route('knowledge.index', array_merge(request()->all(), ['tipe' => 'Teks'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Teks' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Teks</a>
                        <a href="{{ route('knowledge.index', array_merge(request()->all(), ['tipe' => 'Video'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Video' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Video</a>
                        <a href="{{ route('knowledge.index', array_merge(request()->all(), ['tipe' => 'Gambar'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Gambar' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Gambar</a>
                        <a href="{{ route('knowledge.index', array_merge(request()->all(), ['tipe' => 'Audio'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Audio' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Audio</a>
                    </div>
                </div>
            </div>

            <!-- Filter Status -->
            <div x-data="{ open: false }" class="relative inline-block text-left">
                <button @click="open = !open" @click.away="open = false" type="button"
                        class="inline-flex items-center gap-2 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Status {{ request('status') ? ': ' . (request('status') == 'diterima' ? 'Diterima' : ucfirst(request('status'))) : '' }}
                </button>
                <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-40 origin-top-left rounded-md bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black ring-opacity-5 border border-slate-200 dark:border-slate-700 focus:outline-none" style="display: none;">
                    <div class="py-1">
                        <a href="{{ route('knowledge.index', array_merge(request()->except('status', 'page_knowledges'))) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('status') == '' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Semua Status</a>
                        <a href="{{ route('knowledge.index', array_merge(request()->all(), ['status' => 'diajukan'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('status') == 'diajukan' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Diajukan</a>
                        <a href="{{ route('knowledge.index', array_merge(request()->all(), ['status' => 'diterima'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('status') == 'diterima' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Diterima</a>
                        <a href="{{ route('knowledge.index', array_merge(request()->all(), ['status' => 'ditolak'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('status') == 'ditolak' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Ditolak</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
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
                    <td class="py-3 px-6">
                        <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $item->judul }}</div>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->tipe }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->category->nama_kategori ?? '-' }}</td>
                    <td class="py-3 px-6">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border
                            {{ $item->status == 'Disetujui' ? 'bg-green-100 dark:bg-green-950 text-green-800 dark:text-green-300 border-green-200 dark:border-green-800' :
                              ($item->status == 'Ditolak' ? 'bg-red-100 dark:bg-red-950 text-red-800 dark:text-red-300 border-red-200 dark:border-red-800' : 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800') }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex items-center justify-end">
                            <div x-data="{ open: false, showDeleteModal: false }" class="relative inline-block text-left" @mouseenter="open = true" @mouseleave="open = false">
                                <button @click="open = !open" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition focus:outline-none">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                </button>
                                <div x-show="open" x-cloak x-transition class="absolute right-0 z-20 mt-1 w-32 origin-top-right rounded-md bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black ring-opacity-5 border border-slate-200 dark:border-slate-700 focus:outline-none py-1" style="display: none;">
                                    {{-- Lihat --}}
                                    <a href="{{ route('admin.knowledge.show', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat</span>
                                    </a>
                                    {{-- Edit --}}
                                    @can('edit-knowledge', $item)
                                    <a href="{{ route('knowledge.edit', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Edit</span>
                                    </a>
                                    @endcan
                                    {{-- Validasi/Ubah --}}
                                    @if(in_array(auth()->user()->role, ['Analisis Pengetahuan', 'Analis Pengetahuan', 'Super Admin', 'Admin Pusat', 'Admin IPPD']))
                                    <a href="{{ route('validasi.show', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                        <span>Ubah</span>
                                    </a>
                                    @endif
                                    {{-- Hapus --}}
                                    @can('delete-knowledge', $item)
                                    <button type="button" @click="showDeleteModal = true; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        <span>Hapus</span>
                                    </button>
                                    @endcan

                                </div>

                                {{-- Modal Konfirmasi Hapus --}}
                                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                                    <div @click.outside="showDeleteModal = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md">
                                        <div class="p-6">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center shrink-0">
                                                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                </div>
                                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Konfirmasi Hapus</h3>
                                            </div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                                                Apakah Anda yakin ingin menghapus pengetahuan "<strong class="text-slate-700 dark:text-slate-200">{{ $item->judul }}</strong>"? Tindakan ini tidak dapat dibatalkan.
                                            </p>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="showDeleteModal = false"
                                                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                                    Batal
                                                </button>
                                                <form action="{{ route('knowledge.destroy', $item->id) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center">
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Data tidak ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($knowledges->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
        {{ $knowledges->links() }}
    </div>
    @endif
</div>

@if(in_array(auth()->user()->role, ['Anggota', 'Kreator Pengetahuan', 'Moderator', 'Super Admin', 'Admin Pusat', 'Admin IPPD']))

<!-- Table Draft Pengetahuan -->
<div class="mt-8 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Draft Pengetahuan (Batal Diajukan)</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar artikel yang disimpan sebagai draft atau dibatalkan pengajuannya</p>
    </div>

    <!-- Table -->
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
                @forelse($drafts as $item)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
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
                    <td class="py-3 px-6">
                        <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $item->judul }}</div>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->tipe }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->category->nama_kategori ?? '-' }}</td>
                    <td class="py-3 px-6">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-300">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600 dark:text-slate-300">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                    <td class="py-3 px-6 text-right">
                        <div class="flex items-center justify-end">
                            <div x-data="{ open: false, showDeleteModal: false }" class="relative inline-block text-left">
                                <button @click="open = !open" @click.outside="open = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition focus:outline-none">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                </button>
                                <div x-show="open" x-cloak x-transition class="absolute right-0 z-20 mt-1 w-32 origin-top-right rounded-md bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black ring-opacity-5 border border-slate-200 dark:border-slate-700 py-1">
                                    <a href="{{ route('admin.knowledge.show', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat</span>
                                    </a>
                                    @can('edit-knowledge', $item)
                                    <a href="{{ route('knowledge.edit', $item->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Edit</span>
                                    </a>
                                    @endcan
                                    @can('delete-knowledge', $item)
                                    <button type="button" @click="showDeleteModal = true; open = false" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        <span>Hapus</span>
                                    </button>
                                    @endcan
                                </div>


                                {{-- Modal Konfirmasi Hapus --}}
                                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                                    <div @click.outside="showDeleteModal = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md">
                                        <div class="p-6">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center shrink-0">
                                                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                </div>
                                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Konfirmasi Hapus</h3>
                                            </div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                                                Apakah Anda yakin ingin menghapus draft "<strong class="text-slate-700 dark:text-slate-200">{{ $item->judul }}</strong>"? Tindakan ini tidak dapat dibatalkan.
                                            </p>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="showDeleteModal = false"
                                                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                                    Batal
                                                </button>
                                                <form action="{{ route('knowledge.destroy', $item->id) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center">
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Tidak ada draft saat ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($drafts->hasPages())
    <div class="p-4 border-t border-slate-200 dark:border-slate-700">
        {{ $drafts->links() }}
    </div>
    @endif
</div>
@endif
@endsection

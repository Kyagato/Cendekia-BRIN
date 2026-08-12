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
        <a href="{{ route('knowledge.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah
        </a>
    </div>

    <div class="px-6 py-4 border-b border-slate-200">
        <h2 class="text-lg font-bold text-slate-800">Pengetahuan menunggu validasi</h2>
        <p class="text-sm text-slate-500">Daftar yang masih menunggu persetujuan atau penolakan.</p>
    </div>

    <!-- Filters -->
    <div class="p-6 border-b border-slate-200 flex flex-col lg:flex-row justify-between items-center gap-4">
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Search -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" placeholder="Pencarian..." class="w-full pl-9 pr-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
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
                    @can('validate-knowledge')
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500 text-right">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pendingKnowledges as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6">
                        <div class="w-12 h-12 bg-slate-100 rounded flex items-center justify-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    </td>
                    <td class="py-3 px-6">
                        <div class="text-sm font-medium text-slate-800">{{ $item->judul }}</div>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->tipe }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->category->nama_kategori ?? '-' }}</td>
                    <td class="py-3 px-6">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                    @can('validate-knowledge')
                    <td class="py-3 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('validasi.approve', $item) }}" onsubmit="return confirm('Setujui pengetahuan ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2.5 py-1.5 rounded text-xs font-medium bg-green-600 text-white hover:bg-green-700">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('validasi.reject', $item) }}" onsubmit="return confirm('Tolak pengetahuan ini?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="catatan_validasi" value="Penolakan otomatis dari analisis karena belum memenuhi kriteria publikasi.">
                                <button type="submit" class="px-2.5 py-1.5 rounded text-xs font-medium bg-red-600 text-white hover:bg-red-700">Tolak</button>
                            </form>
                        </div>
                    </td>
                    @endcan
                </tr>
                @empty
                <tr>
                    <td colspan="@can('validate-knowledge') 8 @else 7 @endcan" class="py-16 text-center">
                        <p class="text-slate-500 text-sm">Belum ada pengetahuan yang menunggu validasi.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="p-4 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-600">
        <div>
            Menampilkan {{ $pendingKnowledges->firstItem() ?? 0 }} dari {{ $pendingKnowledges->lastItem() ?? 0 }} baris data.
        </div>

        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-2">
                <span>Baris per halaman</span>
                <select class="border-slate-300 rounded px-2 py-1 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>

            <div>
                Hal. {{ $pendingKnowledges->currentPage() }} dari {{ $pendingKnowledges->lastPage() }}
            </div>

            <div class="flex items-center gap-1">
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ $pendingKnowledges->onFirstPage() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                </button>
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ $pendingKnowledges->onFirstPage() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ !$pendingKnowledges->hasMorePages() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <button class="p-1 rounded hover:bg-slate-100 disabled:opacity-50" {{ !$pendingKnowledges->hasMorePages() ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h2 class="text-lg font-bold text-slate-800">Riwayat validasi</h2>
        <p class="text-sm text-slate-500">Pengetahuan yang sudah disetujui atau ditolak akan muncul di bawah ini.</p>
    </div>

    <div class="overflow-x-auto">
        @if($processedKnowledges->isNotEmpty())
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Judul</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Kategori</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Status</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Divalidasi oleh</th>
                    <th class="py-3 px-6 text-xs font-semibold text-slate-500">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($processedKnowledges as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6 text-sm font-medium text-slate-800">{{ $item->judul }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->category->nama_kategori ?? '-' }}</td>
                    <td class="py-3 px-6">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->status == 'Disetujui' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->validator->name ?? '-' }}</td>
                    <td class="py-3 px-6 text-sm text-slate-600">{{ $item->validated_at ? $item->validated_at->format('d M Y H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-10 text-center text-sm text-slate-500">
            Belum ada riwayat validasi.
        </div>
        @endif
    </div>
</div>
@endsection
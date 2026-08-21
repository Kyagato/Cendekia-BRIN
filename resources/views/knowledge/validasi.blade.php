@extends('layouts.admin')
@section('title', 'Validasi Pengetahuan')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li>
        <a href="{{ route('validasi.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-500 transition">Validasi</a>
    </li>
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Tinjau &amp; Edit Validasi</li>
@endsection

@section('content')
<div x-data="{ showTolakModal: false }" class="max-w-6xl mx-auto space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3 text-sm shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Validasi &amp; Edit Pengetahuan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ubah data artikel dan tentukan keputusan persetujuan atau penolakan</p>
        </div>
        <a href="{{ route('validasi.index') }}" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </div>

    {{-- Main Edit Form --}}
    <form action="{{ route('validasi.update', $knowledge->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-800 shadow-md rounded-xl p-8 border border-slate-200 dark:border-slate-700">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- LEFT COLUMN: Editable Form Fields --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Judul --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Judul Artikel <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul', $knowledge->judul) }}" required
                               class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-semibold text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 transition">
                    </div>

                    {{-- Format / Tipe --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">Format Konten <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                            @foreach(['Teks', 'Gambar', 'Video', 'Audio'] as $fmt)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tipe" value="{{ $fmt }}" {{ old('tipe', $knowledge->tipe) === $fmt ? 'checked' : '' }} class="text-red-600 focus:ring-red-600 w-4 h-4">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $fmt }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- URL Teks / Media --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">URL {{ $knowledge->tipe }}</label>
                        <input type="text" name="url_teks" value="{{ old('url_teks', $knowledge->url_teks) }}" placeholder="https://..."
                               class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 transition">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pastikan URL dapat diakses dengan baik</p>
                    </div>

                    {{-- Penulis & Kolaborator --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Penulis</label>
                            <input type="text" name="penulis" value="{{ old('penulis', $knowledge->penulis) }}" placeholder="Nama penulis..."
                                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 font-medium focus:ring-2 focus:ring-red-600 focus:border-red-600 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Kolaborator</label>
                            <input type="text" name="kolaborator" value="{{ old('kolaborator', $knowledge->kolaborator) }}" placeholder="Nama kolaborator..."
                                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 font-medium focus:ring-2 focus:ring-red-600 focus:border-red-600 transition">
                        </div>
                    </div>

                    {{-- Ringkasan / Deskripsi --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Ringkasan</label>
                        <textarea name="deskripsi" rows="3" placeholder="Tuliskan ringkasan singkat..."
                                  class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm leading-relaxed focus:ring-2 focus:ring-red-600 focus:border-red-600 transition resize-y">{{ old('deskripsi', $knowledge->deskripsi) }}</textarea>
                    </div>

                    {{-- Detail --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Detail Konten</label>
                        <textarea name="detail" rows="6" placeholder="Tuliskan detail konten secara lengkap..."
                                  class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm leading-relaxed focus:ring-2 focus:ring-red-600 focus:border-red-600 transition resize-y">{{ old('detail', $knowledge->detail) }}</textarea>
                    </div>

                    {{-- Upload Thumbnail / File --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Thumbnail / File Lampiran</label>
                        @if($knowledge->file_path)
                            <div class="mb-3 p-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center gap-3">
                                <img src="{{ asset('storage/' . $knowledge->file_path) }}" alt="Thumbnail" class="w-14 h-14 object-cover rounded border border-slate-200 dark:border-slate-700">
                                <span class="text-xs text-slate-600 dark:text-slate-400">File terpasang saat ini</span>
                            </div>
                        @endif
                        <input type="file" name="file" class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-slate-700 file:text-slate-700 dark:file:text-slate-200 hover:file:bg-slate-200 dark:hover:file:bg-slate-600 transition">
                    </div>

                </div>

                {{-- RIGHT COLUMN: Sidebar Controls --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Tombol Simpan Perubahan Data --}}
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900 p-5 space-y-3">
                        <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Simpan Perubahan Data
                        </button>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 text-center">Simpan pengeditan data di atas sebelum atau sesudah memberikan persetujuan.</p>
                    </div>

                    {{-- Persetujuan Validasi --}}
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900">
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Keputusan Validasi</h3>
                        </div>
                        <div class="px-5 py-4 flex flex-col gap-2">
                            <div class="flex gap-2">
                                <button type="button" onclick="document.getElementById('form-approve').submit();"
                                        class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Setujui &amp; Terbit
                                </button>
                                <button type="button" @click="showTolakModal = true"
                                        class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Status Saat Ini --}}
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900">
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Status Saat Ini</h3>
                        </div>
                        <div class="px-5 py-4 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $knowledge->status === 'Disetujui' ? 'bg-green-500' : ($knowledge->status === 'Ditolak' ? 'bg-red-500' : ($knowledge->status === 'Diajukan' ? 'bg-blue-500' : 'bg-yellow-400')) }}"></span>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                {{ $knowledge->status === 'Diajukan' ? 'Diajukan (Menunggu Validasi)' : $knowledge->status }}
                            </span>
                        </div>
                    </div>

                    {{-- Kategori Select --}}
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900">
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Kategori</h3>
                        </div>
                        <div class="px-5 py-4">
                            <select name="category_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm focus:ring-red-600 focus:border-red-600 transition">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $knowledge->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Label / Tags Input --}}
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900">
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Label / Tag</h3>
                        </div>
                        <div class="px-5 py-4">
                            <input type="text" name="tags" value="{{ old('tags', $knowledge->tags ? $knowledge->tags->pluck('nama_label')->implode(', ') : '') }}" placeholder="Pisahkan dengan koma (contoh: iptek, BRIN)"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-xs focus:ring-red-600 focus:border-red-600 transition">
                        </div>
                    </div>

                    {{-- Tanggal Terbit Input --}}
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900">
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tanggal Terbit</h3>
                        </div>
                        <div class="px-5 py-4">
                            <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit', $knowledge->tanggal_terbit ? \Carbon\Carbon::parse($knowledge->tanggal_terbit)->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm focus:ring-red-600 focus:border-red-600 transition">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    {{-- Form Approve hidden --}}
    <form id="form-approve" action="{{ route('validasi.approve', $knowledge->id) }}" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>

    {{-- Modal Tolak Pengajuan --}}
    <div x-show="showTolakModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         style="display: none;">

        <div x-show="showTolakModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-slate-200 dark:border-slate-700">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Tolak Pengajuan</h2>
                <button @click="showTolakModal = false" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('validasi.reject', $knowledge->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-slate-600 dark:text-slate-300">Apakah Anda yakin ingin menolak pengajuan ini?</p>
                    <textarea name="alasan_tolak" rows="4" placeholder="Tulis alasan penolakan..."
                              class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-sm text-slate-800 dark:text-slate-100 resize-none"></textarea>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" @click="showTolakModal = false"
                            class="px-5 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

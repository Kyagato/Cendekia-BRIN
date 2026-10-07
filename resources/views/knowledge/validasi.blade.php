@extends('layouts.admin')
@section('title', 'Validasi Pengetahuan')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li>
        <a href="{{ route('validasi.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition">Validasi</a>
    </li>
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Tinjau &amp; Edit Validasi</li>
@endsection

@section('content')
<div x-data="{ showTolakModal: false }" class="max-w-6xl mx-auto space-y-6">


    {{-- Top Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Validasi &amp; Edit Pengetahuan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ubah data artikel dan tentukan keputusan persetujuan atau penolakan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('validasi.index') }}" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Banner Catatan Penolakan / Revisi Sebelumnya --}}
    @if(!empty($knowledge->catatan_penolakan))
    <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-900/60 rounded-lg text-amber-700 dark:text-amber-400 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">Catatan Penolakan / Revisi Sebelumnya:</h3>
                <p class="text-sm text-amber-800 dark:text-amber-300 mt-1 leading-relaxed whitespace-pre-line">{{ $knowledge->catatan_penolakan }}</p>
            </div>
        </div>
    </div>
    @endif

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
                               class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-semibold text-sm focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition">
                    </div>

                    {{-- Format / Tipe --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">Format Konten <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                            @foreach(['Teks', 'Gambar', 'Video', 'Audio'] as $fmt)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tipe" value="{{ $fmt }}" {{ old('tipe', $knowledge->tipe) === $fmt ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-600 w-4 h-4">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $fmt }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- URL Teks / Media --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200">URL {{ $knowledge->tipe }}</label>
                            @if($knowledge->url_teks)
                            <a href="{{ $knowledge->url_teks }}" target="_blank" class="text-xs text-primary-600 dark:text-primary-400 hover:underline inline-flex items-center gap-1 font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                Buka Tautan Media
                            </a>
                            @endif
                        </div>
                        <input type="text" name="url_teks" value="{{ old('url_teks', $knowledge->url_teks) }}" placeholder="https://..."
                               class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pastikan URL dapat diakses dengan baik oleh publik.</p>

                        {{-- Embed Player jika URL YouTube --}}
                        @if($knowledge->url_teks && (str_contains($knowledge->url_teks, 'youtube.com') || str_contains($knowledge->url_teks, 'youtu.be')))
                            @php
                                $ytId = null;
                                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $knowledge->url_teks, $match)) {
                                    $ytId = $match[1];
                                }
                            @endphp
                            @if($ytId)
                            <div class="mt-3 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 aspect-video bg-black shadow-xs">
                                <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                            @endif
                        @endif
                    </div>

                    {{-- Penulis & Kolaborator --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Penulis</label>
                            <input type="text" name="penulis" value="{{ old('penulis', $knowledge->penulis) }}" placeholder="Nama penulis..."
                                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 font-medium focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Kolaborator</label>
                            <input type="text" name="kolaborator" value="{{ old('kolaborator', $knowledge->kolaborator) }}" placeholder="Nama kolaborator..."
                                   class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 font-medium focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition">
                        </div>
                    </div>

                    {{-- Ringkasan / Deskripsi --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Ringkasan</label>
                        <textarea name="deskripsi" rows="3" placeholder="Tuliskan ringkasan singkat..."
                                  class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm leading-relaxed focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition resize-y">{{ old('deskripsi', $knowledge->deskripsi) }}</textarea>
                    </div>

                    {{-- Detail --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Detail Konten</label>
                        <textarea name="detail" rows="6" placeholder="Tuliskan detail konten secara lengkap..."
                                  class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm leading-relaxed focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition resize-y">{{ old('detail', $knowledge->detail) }}</textarea>
                    </div>

                    {{-- Upload Thumbnail / File Lampiran --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Berkas Lampiran &amp; Thumbnail</label>
                        @if($knowledge->file_path)
                            @php
                                $filePathExt = strtolower(pathinfo($knowledge->file_path, PATHINFO_EXTENSION));
                                $isImgFile = in_array($filePathExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isPdfFile = ($filePathExt === 'pdf');
                                $isAudioFile = in_array($filePathExt, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac']);
                                $isVideoFile = in_array($filePathExt, ['mp4', 'mkv', 'webm', 'mov', 'avi']);
                            @endphp
                            <div class="mb-4 p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl space-y-3">
                                <div class="flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if($isImgFile)
                                            <a href="{{ asset('storage/' . $knowledge->file_path) }}" target="_blank" class="block shrink-0">
                                                <img src="{{ asset('storage/' . $knowledge->file_path) }}" alt="Thumbnail" class="w-14 h-14 object-cover rounded-lg border border-slate-200 dark:border-slate-700 hover:opacity-90 transition shadow-xs">
                                            </a>
                                        @elseif($isPdfFile)
                                            <div class="w-14 h-14 bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400 rounded-lg flex flex-col items-center justify-center shrink-0 border border-red-200 dark:border-red-800">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                <span class="text-[9px] font-bold uppercase tracking-wider">PDF</span>
                                            </div>
                                        @elseif($isAudioFile)
                                            <div class="w-14 h-14 bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-lg flex flex-col items-center justify-center shrink-0 border border-amber-200 dark:border-amber-800">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                                                <span class="text-[9px] font-bold uppercase tracking-wider">AUDIO</span>
                                            </div>
                                        @elseif($isVideoFile)
                                            <div class="w-14 h-14 bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 rounded-lg flex flex-col items-center justify-center shrink-0 border border-purple-200 dark:border-purple-800">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <span class="text-[9px] font-bold uppercase tracking-wider">VIDEO</span>
                                            </div>
                                        @else
                                            <div class="w-14 h-14 bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 rounded-lg flex flex-col items-center justify-center shrink-0 border border-blue-200 dark:border-blue-800">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="text-[9px] font-bold uppercase tracking-wider">{{ $filePathExt ?: 'FILE' }}</span>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate max-w-xs" title="{{ basename($knowledge->file_path) }}">
                                                {{ basename($knowledge->file_path) }}
                                            </div>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400">Berkas terpasang saat ini</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ asset('storage/' . $knowledge->file_path) }}" target="_blank" class="px-3.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center gap-1.5 shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                            Buka / Pratinjau
                                        </a>
                                        <a href="{{ asset('storage/' . $knowledge->file_path) }}" download class="px-3.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1.5 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                            Unduh
                                        </a>
                                    </div>
                                </div>

                                {{-- Inline Player untuk Audio / Video --}}
                                @if($isAudioFile)
                                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                                        <audio controls class="w-full">
                                            <source src="{{ asset('storage/' . $knowledge->file_path) }}">
                                        </audio>
                                    </div>
                                @elseif($isVideoFile)
                                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                                        <video controls class="w-full max-h-64 rounded-lg bg-black">
                                            <source src="{{ asset('storage/' . $knowledge->file_path) }}">
                                        </video>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Ganti berkas lampiran (Opsional, jika ingin mengunggah berkas revisi):</label>
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

                    {{-- Tag Input --}}
                    @php
                        $validasiTags = old('tags')
                            ? array_map('trim', explode(',', old('tags')))
                            : ($knowledge->tags ? $knowledge->tags->pluck('nama_label')->toArray() : []);
                    @endphp
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-900">
                        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tag</h3>
                        </div>
                        <div class="px-5 py-4" x-data="{
                            tagInput: '',
                            tags: {{ json_encode($validasiTags) }},
                            addTag() {
                                let val = this.tagInput.trim().toLowerCase();
                                if (val && !this.tags.includes(val)) {
                                    this.tags.push(val);
                                }
                                this.tagInput = '';
                            },
                            removeTag(index) {
                                this.tags.splice(index, 1);
                            },
                            get hiddenValue() {
                                return this.tags.join(', ');
                            }
                        }">
                            <input type="hidden" name="tags" :value="hiddenValue">
                            <div class="flex flex-wrap items-center gap-2 min-h-[40px] px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus-within:ring-2 focus-within:ring-red-600 focus-within:border-red-600 transition">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="inline-flex items-center gap-1.5 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-xs font-semibold pl-3 pr-1.5 py-1.5 rounded-full">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="removeTag(index)" class="w-5 h-5 inline-flex items-center justify-center rounded-full bg-red-200 dark:bg-red-800 hover:bg-red-400 dark:hover:bg-red-600 text-red-700 dark:text-red-200 hover:text-white transition cursor-pointer shrink-0" title="Hapus tag">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                                <input type="text" x-model="tagInput"
                                       @keydown.enter.prevent="addTag()"
                                       @keydown.comma.prevent="addTag()"
                                       @keydown.backspace="if (tagInput === '' && tags.length > 0) removeTag(tags.length - 1)"
                                       placeholder="Ketik tag lalu tekan Enter"
                                       class="flex-1 min-w-[100px] border-0 bg-transparent p-0 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0 focus:outline-none">
                            </div>
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
                    <p class="text-sm text-slate-600 dark:text-slate-300">Tuliskan alasan penolakan atau instruksi perbaikan yang jelas untuk pengunggah:</p>
                    <textarea name="alasan_tolak" rows="4" placeholder="Tulis alasan penolakan dan instruksi revisi..." required maxlength="1000"
                              class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-sm text-slate-800 dark:text-slate-100 resize-none">{{ old('alasan_tolak', $knowledge->catatan_penolakan) }}</textarea>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3">
                    <button type="button" @click="showTolakModal = false"
                            class="px-5 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

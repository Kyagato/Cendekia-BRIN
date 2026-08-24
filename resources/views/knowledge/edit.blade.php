@extends('layouts.admin')
@section('title', 'Edit Pengetahuan')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li>
        <a href="{{ route('knowledge.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-500 transition">Pengetahuan</a>
    </li>
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Edit Pengetahuan</li>
@endsection

@section('content')
<form x-data="{ formatType: '{{ old('tipe', $knowledge->tipe ?? 'Teks') }}' }" action="{{ route('knowledge.update', $knowledge->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Top Action Bar --}}
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Edit Pengetahuan</h1>
                @if(isset($knowledge->status))
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Status saat ini: 
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                        {{ $knowledge->status == 'Disetujui' ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300' : 
                          ($knowledge->status == 'Ditolak' ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' : 
                          ($knowledge->status == 'Diajukan' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300')) }}">
                        {{ $knowledge->status }}
                    </span>
                </p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('knowledge.index') }}" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </a>

                {{-- Tombol Batal Ajukan (hanya muncul jika status = Diajukan) --}}
                @if(isset($knowledge->status) && $knowledge->status == 'Diajukan')
                <button type="button" onclick="document.getElementById('form-batal-ajukan').submit()" class="px-5 py-2.5 border-2 border-red-600 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                    Batal Ajukan
                </button>
                @endif

                {{-- Tombol Simpan --}}
                <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Simpan & Ajukan
                </button>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="bg-white dark:bg-slate-800 shadow-md rounded-xl p-8 border border-transparent dark:border-slate-700">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- ===== LEFT COLUMN (7/12) ===== --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Judul --}}
                    <div>
                        <label for="judul" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Judul</label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', $knowledge->judul) }}" placeholder="Masukkan judul pengetahuan" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" required>
                        @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Format (Alpine.js dynamic) --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">Format</label>
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="tipe" value="Gambar" x-model="formatType" class="text-red-600 focus:ring-red-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 w-4 h-4" required>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-slate-100 transition">Gambar</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="tipe" value="Video" x-model="formatType" class="text-red-600 focus:ring-red-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 w-4 h-4" required>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-slate-100 transition">Video</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="tipe" value="Audio" x-model="formatType" class="text-red-600 focus:ring-red-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 w-4 h-4" required>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-slate-100 transition">Audio</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="tipe" value="Teks" x-model="formatType" class="text-red-600 focus:ring-red-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 w-4 h-4" required>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-slate-100 transition">Text</span>
                            </label>
                        </div>
                        @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dynamic URL field --}}
                    <div>
                        <label for="url_teks" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">
                            Url <span x-text="formatType"></span>
                        </label>
                        <input type="url" id="url_teks" name="url_teks" value="{{ old('url_teks', $knowledge->url_teks) }}" :placeholder="'Masukkan url ' + formatType" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 focus:ring-red-600 focus:border-red-600 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
                    </div>

                    {{-- Penulis & Kolaborator --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="penulis" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Penulis</label>
                            <input type="text" id="penulis" name="penulis" value="{{ old('penulis', $knowledge->penulis) }}" placeholder="Masukkan nama penulis" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 focus:ring-red-600 focus:border-red-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" required>
                            @error('penulis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="kolaborator" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Kolaborator</label>
                            <input type="text" id="kolaborator" name="kolaborator" value="{{ old('kolaborator', $knowledge->kolaborator) }}" placeholder="Masukkan nama kolaborator" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 focus:ring-red-600 focus:border-red-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
                            @error('kolaborator') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Ringkasan --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Ringkasan</label>
                        {{-- Toolbar --}}
                        <div class="border border-slate-300 dark:border-slate-600 border-b-0 rounded-t-lg bg-slate-50 dark:bg-slate-800 p-2 flex gap-2 flex-wrap text-slate-600 dark:text-slate-400">
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded font-bold transition">B</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded italic transition">I</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded underline transition">U</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded line-through transition">S</button>
                            <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 my-auto mx-1"></div>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg></button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg></button>
                        </div>
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full px-4 py-3 rounded-b-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" placeholder="Tuliskan ringkasan pengetahuan di sini...">{{ old('deskripsi', $knowledge->deskripsi) }}</textarea>
                        @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Detail --}}
                    <div>
                        <label for="detail" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Detail</label>
                        {{-- Toolbar --}}
                        <div class="border border-slate-300 dark:border-slate-600 border-b-0 rounded-t-lg bg-slate-50 dark:bg-slate-800 p-2 flex gap-2 flex-wrap text-slate-600 dark:text-slate-400">
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded font-bold transition">B</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded italic transition">I</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded underline transition">U</button>
                            <button type="button" class="p-1 hover:bg-slate-200 dark:hover:bg-slate-700 rounded line-through transition">S</button>
                        </div>
                        <textarea id="detail" name="detail" rows="6" class="w-full px-4 py-3 rounded-b-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" placeholder="Tuliskan detail pengetahuan di sini...">{{ old('detail', $knowledge->detail) }}</textarea>
                    </div>

                </div>

                {{-- ===== RIGHT COLUMN (5/12) ===== --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Baris 1: Kategori & Tanggal Terbit --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Kategori --}}
                        <div>
                            <label for="category_id" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Kategori</label>
                            <div class="relative">
                                <select id="category_id" name="category_id" class="w-full px-4 py-3 appearance-none rounded-lg border border-slate-300 dark:border-slate-600 focus:ring-red-600 focus:border-red-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach ($categories as $kategori)
                                        <option value="{{ $kategori->id }}" {{ old('category_id', $knowledge->category_id) == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">Klik menu <a href="{{ route('category.index') }}" class="text-red-600 dark:text-red-400 hover:underline">Kategori</a> untuk melihat detil kategori.</p>
                            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Terbit --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Tanggal Terbit</label>
                            <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit', $knowledge->tanggal_terbit ? \Carbon\Carbon::parse($knowledge->tanggal_terbit)->format('Y-m-d') : '') }}" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 focus:ring-red-600 focus:border-red-600 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300">
                        </div>
                    </div>

                    {{-- Baris 2: Tagline & Status Akses --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Tagline --}}
                        <div>
                            <label for="tags" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Tagline</label>
                            <input type="text" id="tags" name="tags" value="{{ old('tags', $knowledge->tags ? $knowledge->tags->pluck('nama_label')->implode(', ') : '') }}" placeholder="Masukkan Tagline (pisahkan dengan koma)" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 focus:ring-red-600 focus:border-red-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
                        </div>

                        {{-- Status Akses --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Status Akses</label>
                            <div class="relative">
                                <select name="status_akses" class="w-full px-4 py-3 appearance-none rounded-lg border border-slate-300 dark:border-slate-600 focus:ring-red-600 focus:border-red-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100">
                                    <option value="" disabled>Pilih status</option>
                                    <option value="public" {{ old('status_akses', $knowledge->status_akses) == 'public' ? 'selected' : '' }}>Publik</option>
                                    <option value="private" {{ old('status_akses', $knowledge->status_akses) == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Unggulan --}}
                    <div class="flex items-center gap-3 py-3 border-b border-slate-100 dark:border-slate-700">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="unggulan" value="1" class="w-5 h-5 text-red-600 bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-600 rounded focus:ring-red-600 focus:ring-2" {{ old('unggulan', $knowledge->unggulan) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-bold text-slate-800 dark:text-slate-200">Jadikan Pengetahuan Unggulan</span>
                        </label>
                    </div>

                    {{-- Thumbnail Upload --}}
                    <div x-data="{ fileName: '' }">
                        <label class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Thumbnail / File Lampiran</label>
                        
                        @if($knowledge->file_path)
                            <div class="mb-4">
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">File saat ini:</p>
                                <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate">{{ basename($knowledge->file_path) }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-lg bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer" onclick="document.getElementById('file_upload').click()">
                            <div class="space-y-1 text-center">
                                <svg x-show="!fileName" class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <svg x-cloak x-show="fileName" class="mx-auto h-12 w-12 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center mt-2">
                                    <label for="file_upload" class="relative cursor-pointer bg-transparent rounded-md font-medium text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                        <span x-show="!fileName">Ganti file thumbnail</span>
                                        <span x-cloak x-show="fileName" x-text="fileName" class="text-slate-800 dark:text-slate-200 font-semibold truncate max-w-[200px] inline-block"></span>
                                        <input id="file_upload" name="file_upload" type="file" class="sr-only" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                    </label>
                                </div>
                                <p x-show="!fileName" class="text-xs text-slate-500 dark:text-slate-400 mt-1">PNG, JPG, GIF up to 50MB</p>
                                <p x-cloak x-show="fileName" class="text-xs text-green-600 dark:text-green-400 font-medium mt-1">File baru siap disimpan</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

{{-- Form tersembunyi untuk Batal Ajukan --}}
@if(isset($knowledge->status) && $knowledge->status == 'Diajukan')
<form id="form-batal-ajukan" action="{{ route('knowledge.update', $knowledge->id) }}" method="POST" class="hidden">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" value="Draft">
    <input type="hidden" name="judul" value="{{ $knowledge->judul }}">
    <input type="hidden" name="tipe" value="{{ $knowledge->tipe }}">
    <input type="hidden" name="penulis" value="{{ $knowledge->penulis }}">
    <input type="hidden" name="category_id" value="{{ $knowledge->category_id }}">
</form>
@endif
@endsection

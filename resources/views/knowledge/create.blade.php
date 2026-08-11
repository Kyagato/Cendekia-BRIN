@extends('layouts.admin')
@section('title', 'Buat Pengetahuan')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li>
        <a href="{{ route('knowledge.index') }}" class="hover:text-primary-600 transition">Pengetahuan</a>
    </li>
    <li>
        <svg class="w-4 h-4 text-slate-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 font-semibold">Tambah Pengetahuan</li>
@endsection

@section('content')
<form action="{{ route('knowledge.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-slate-800">Tambah Pengetahuan</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('knowledge.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                Simpan
            </button>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Judul -->
            <div>
                <label for="judul" class="block text-sm font-bold text-slate-800 mb-2">Judul</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500" required>
                @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Format -->
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-3">Format</label>
                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe" value="Gambar" class="text-blue-600 focus:ring-blue-500 w-4 h-4" {{ old('tipe') == 'Gambar' ? 'checked' : '' }} required>
                        <span class="text-sm font-medium text-slate-700">Gambar</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe" value="Video" class="text-blue-600 focus:ring-blue-500 w-4 h-4" {{ old('tipe') == 'Video' ? 'checked' : '' }} required>
                        <span class="text-sm font-medium text-slate-700">Video</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe" value="Audio" class="text-blue-600 focus:ring-blue-500 w-4 h-4" {{ old('tipe') == 'Audio' ? 'checked' : '' }} required>
                        <span class="text-sm font-medium text-slate-700">Audio</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe" value="Teks" class="text-blue-600 focus:ring-blue-500 w-4 h-4" {{ old('tipe') == 'Teks' ? 'checked' : '' }} required>
                        <span class="text-sm font-medium text-slate-700">Text</span>
                    </label>
                </div>
                @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Url Teks -->
            <div>
                <label for="url_teks" class="block text-sm font-bold text-slate-800 mb-2">Url Teks</label>
                <input type="url" id="url_teks" name="url_teks" placeholder="Masukkan url Teks" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500 bg-slate-50">
            </div>

            <!-- Penulis & Kolaborator -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="penulis" class="block text-sm font-bold text-slate-800 mb-2">Penulis</label>
                    <input type="text" id="penulis" name="penulis" value="{{ old('penulis', Auth::user()->name) }}" placeholder="Masukkan nama penulis" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                    @error('penulis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="kolaborator" class="block text-sm font-bold text-slate-800 mb-2">Kolaborator</label>
                    <input type="text" id="kolaborator" name="kolaborator" value="{{ old('kolaborator') }}" placeholder="Masukkan nama kolaborator" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('kolaborator') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Ringkasan -->
            <div>
                <label for="deskripsi" class="block text-sm font-bold text-slate-800 mb-2">Ringkasan</label>
                <!-- Toolbar Dummy -->
                <div class="border border-slate-300 border-b-0 rounded-t-lg bg-slate-50 p-2 flex gap-2 flex-wrap text-slate-600">
                    <button type="button" class="p-1 hover:bg-slate-200 rounded font-bold">B</button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded italic">I</button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded underline">U</button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded line-through">S</button>
                    <div class="w-px h-5 bg-slate-300 my-auto mx-1"></div>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg></button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg></button>
                </div>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full px-4 py-3 rounded-b-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Untuk itu, kreator harus mengisi metadata.">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Detail -->
            <div>
                <label for="detail" class="block text-sm font-bold text-slate-800 mb-2">Detail</label>
                <!-- Toolbar Dummy -->
                <div class="border border-slate-300 border-b-0 rounded-t-lg bg-slate-50 p-2 flex gap-2 flex-wrap text-slate-600">
                    <button type="button" class="p-1 hover:bg-slate-200 rounded font-bold">B</button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded italic">I</button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded underline">U</button>
                    <button type="button" class="p-1 hover:bg-slate-200 rounded line-through">S</button>
                </div>
                <textarea id="detail" name="detail" rows="6" class="w-full px-4 py-3 rounded-b-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Tuliskan detail pengetahuan di sini...">{{ old('detail') }}</textarea>
            </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            
            <!-- Kategori -->
            <div>
                <label for="category_id" class="block text-sm font-bold text-slate-800 mb-2">Kategori</label>
                <div class="relative">
                    <select id="category_id" name="category_id" class="w-full px-4 py-3 appearance-none rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500 bg-white" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach ($categories as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-1.5">Klik menu <a href="#" class="text-blue-500 hover:underline">Kategori</a> untuk melihat detil kategori.</p>
                @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Label -->
            <div>
                <label for="tags" class="block text-sm font-bold text-slate-800 mb-2">Label</label>
                <div class="relative">
                    <input type="text" id="tags" name="tags" placeholder="Pilih Tag (pisahkan dengan koma)" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tanggal Terbit -->
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">Tanggal Terbit</label>
                <div class="relative">
                    <input type="date" name="tanggal_terbit" class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-700">
                </div>
            </div>

            <!-- Status Akses -->
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">Status Akses</label>
                <div class="relative">
                    <select name="status_akses" class="w-full px-4 py-3 appearance-none rounded-lg border border-slate-300 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-500">
                        <option value="" disabled selected>Pilih status</option>
                        <option value="public">Publik</option>
                        <option value="private">Private</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Unggulan -->
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm font-bold text-slate-800">Unggulan</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="unggulan" value="1" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <!-- Thumbnail Upload -->
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">Thumbnail / File Lampiran</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg bg-slate-50 hover:bg-slate-100 transition cursor-pointer" onclick="document.getElementById('file_upload').click()">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-slate-600 justify-center">
                            <label for="file_upload" class="relative cursor-pointer bg-transparent rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Unggah file thumbnail</span>
                                <input id="file_upload" name="file_upload" type="file" class="sr-only">
                            </label>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG, GIF up to 10MB</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection
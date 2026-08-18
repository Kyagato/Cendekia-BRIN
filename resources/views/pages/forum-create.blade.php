@extends('layouts.public')
@section('title', 'Buat Topik Baru')

@section('content')
<section class="pt-32 pb-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="mb-8">
            <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition font-medium">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Forum
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100 dark:border-slate-700">
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Buat Topik Diskusi Baru</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Mulai diskusi, ajukan pertanyaan, atau bagikan wawasan dengan komunitas SPBE.</p>
            </div>

            <div class="p-6 sm:p-8">
                <form action="{{ route('forum.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="judul" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Judul Topik <span class="text-red-500">*</span></label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Bagaimana cara mengimplementasikan arsitektur SPBE?" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-primary-500 focus:ring-primary-500 transition">
                        @error('judul') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="category_id" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" required class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-primary-500 focus:ring-primary-500 transition">
                            <option value="" disabled selected class="text-slate-400 dark:text-slate-500">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-8">
                        <label for="konten" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Konten / Pertanyaan <span class="text-red-500">*</span></label>
                        <textarea id="konten" name="konten" rows="8" required placeholder="Jelaskan secara detail topik diskusi atau pertanyaan Anda di sini..." class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-primary-500 focus:ring-primary-500 transition resize-y">{{ old('konten') }}</textarea>
                        @error('konten') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('forum.index') }}" class="px-6 py-3 rounded-lg font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition">
                            Kirim Topik
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

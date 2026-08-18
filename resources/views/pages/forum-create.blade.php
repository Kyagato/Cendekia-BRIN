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

                    {{-- Pilih Materi Pengetahuan (Single Search & Selection Autocomplete) --}}
                    <div class="mb-6 relative" x-data="{
                        open: false,
                        search: @js(old('knowledge_id') ? '' : ($linkedKnowledge->judul ?? '')),
                        selectedId: @js(old('knowledge_id', $linkedKnowledge->id ?? '')),
                        selectedTitle: @js(old('knowledge_id') ? '' : ($linkedKnowledge->judul ?? '')),
                        items: [
                            @foreach($knowledges as $k)
                                { id: {{ $k->id }}, judul: @js($k->judul), category_id: {{ $k->category_id }}, category_name: @js($k->category->nama_kategori ?? 'Umum') },
                            @endforeach
                        ],
                        get filteredItems() {
                            if (!this.search || this.search === this.selectedTitle) {
                                return this.items;
                            }
                            return this.items.filter(item => 
                                item.judul.toLowerCase().includes(this.search.toLowerCase()) ||
                                item.category_name.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        select(item) {
                            this.selectedId = item.id;
                            this.selectedTitle = item.judul;
                            this.search = item.judul;
                            this.open = false;
                            
                            const catSelect = document.getElementById('category_id');
                            if (catSelect && item.category_id) {
                                catSelect.value = item.category_id;
                            }
                            
                            const judulInput = document.getElementById('judul');
                            if (judulInput && (!judulInput.value || judulInput.value.startsWith('Diskusi: '))) {
                                judulInput.value = 'Diskusi: ' + item.judul;
                            }
                        },
                        clear() {
                            this.selectedId = '';
                            this.selectedTitle = '';
                            this.search = '';
                            this.open = false;
                        }
                    }">
                        <label for="knowledge_search_input" class="block text-sm font-semibold text-slate-800 mb-2">
                            Hubungkan ke Materi Pengetahuan <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                        </label>

                        {{-- Hidden input for form submission --}}
                        <input type="hidden" name="knowledge_id" :value="selectedId">

                        {{-- Single Search Input Field --}}
                        <div class="relative" @click.away="open = false">
                            <div class="relative">
                                <input type="text" id="knowledge_search_input" 
                                       x-model="search" 
                                       @focus="open = true" 
                                       @input="open = true; if(!search) clear()"
                                       placeholder="🔍 Cari dan pilih materi pengetahuan..." 
                                       autocomplete="off"
                                       class="w-full pl-10 pr-10 py-3 rounded-lg border border-slate-300 focus:border-red-500 focus:ring-red-500 transition text-slate-800 text-sm">
                                
                                {{-- Search Icon --}}
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>

                                {{-- Clear Button --}}
                                <button type="button" x-show="search" @click="clear()" class="absolute right-3 top-3.5 text-slate-400 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Floating Results Dropdown --}}
                            <div x-show="open && filteredItems.length > 0" 
                                 x-transition
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto py-1 text-sm">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <div @click="select(item)" 
                                         class="px-4 py-2.5 hover:bg-red-50 cursor-pointer flex items-center justify-between transition border-b border-slate-50 last:border-0">
                                        <div>
                                            <span class="font-medium text-slate-800 block" x-text="item.judul"></span>
                                            <span class="text-xs text-slate-400" x-text="item.category_name"></span>
                                        </div>
                                        <span x-show="selectedId == item.id" class="text-red-600 text-xs font-semibold">✓ Terpilih</span>
                                    </div>
                                </template>
                            </div>

                            <div x-show="open && search && filteredItems.length === 0" 
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg p-4 text-center text-xs text-slate-400">
                                Tidak ada materi pengetahuan yang cocok dengan "<span x-text="search"></span>"
                            </div>
                        </div>
                        @error('knowledge_id') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="judul" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Judul Topik <span class="text-red-500">*</span></label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', isset($linkedKnowledge) ? 'Diskusi: ' . $linkedKnowledge->judul : '') }}" required placeholder="Contoh: Bagaimana cara mengimplementasikan arsitektur SPBE?" class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-primary-500 focus:ring-primary-500 transition">
                        @error('judul') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="category_id" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" required class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-primary-500 focus:ring-primary-500 transition">
                            <option value="" disabled selected class="text-slate-400 dark:text-slate-500">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $linkedKnowledge->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
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

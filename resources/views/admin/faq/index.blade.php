@extends('layouts.admin')
@section('title', 'Edit FAQs')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-100 font-bold">Edit FAQs</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    showCreateSectionModal: false,
    newSectionOnlyName: '',

    showCreateModal: false,
    createKategori: '',
    
    showEditModal: false,
    editId: null,
    editKategori: '',
    editPertanyaan: '',
    editJawaban: '',

    showEditSectionModal: false,
    oldSectionName: '',
    newSectionName: '',

    openCreateSectionModal() {
        this.newSectionOnlyName = '';
        this.showCreateSectionModal = true;
    },

    openCreateModal(sectionName = '') {
        this.createKategori = sectionName;
        this.showCreateModal = true;
    },

    openEditModal(faq) {
        this.editId = faq.id;
        this.editKategori = faq.kategori_faq;
        this.editPertanyaan = faq.pertanyaan;
        this.editJawaban = faq.jawaban;
        this.showEditModal = true;
    },

    openEditSectionModal(sectionName) {
        this.oldSectionName = sectionName;
        this.newSectionName = sectionName;
        this.showEditSectionModal = true;
    }
}">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit FAQs</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                Kelola daftar bagian dan pertanyaan umum (FAQs) untuk tampilan publik MojoPedia.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Tombol Tambah Bagian Baru -->
            <button @click="openCreateSectionModal()" 
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2.5 rounded-lg shadow-sm transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                Tambah Bagian Baru
            </button>
        </div>
    </div>

    {{-- Error Validation Alert --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-100 p-4 rounded-xl">
            <p class="font-bold text-sm">Terdapat kesalahan pengisian:</p>
            <ul class="list-disc list-inside text-xs mt-1 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FAQ Content List (Grouped by Nama Bagian) --}}
    @if(isset($faqsGrouped) && $faqsGrouped->count() > 0)
        <div class="space-y-6">
            @foreach($faqsGrouped as $bagian => $items)
                @php
                    $validQuestions = $items->whereNotNull('pertanyaan');
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    {{-- Section Header --}}
                    <div class="px-6 py-4 bg-slate-100 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600 flex flex-wrap justify-between items-center gap-3">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span class="text-slate-700 dark:text-slate-200">Bagian:</span>
                                <span class="text-red-600 dark:text-red-400 font-extrabold text-lg">{{ $bagian }}</span>
                            </h2>
                            <span class="text-xs bg-slate-200 dark:bg-slate-600 text-slate-800 dark:text-slate-100 px-2.5 py-1 rounded-full font-bold">
                                {{ $validQuestions->count() }} Pertanyaan
                            </span>
                        </div>

                        {{-- Action Buttons Per Bagian --}}
                        <div class="flex items-center gap-2">
                            <!-- Edit Nama Bagian -->
                            <button @click="openEditSectionModal('{{ addslashes($bagian) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-500 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 text-xs font-bold rounded-lg transition shadow-sm"
                                    title="Edit nama bagian ini">
                                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Edit Bagian
                            </button>

                            <!-- Tambah Judul Baru di Bagian Ini -->
                            <button @click="openCreateModal('{{ addslashes($bagian) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition shadow-sm"
                                    title="Tambah pertanyaan baru langsung ke Bagian {{ $bagian }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                Tambah Judul Baru
                            </button>

                            <!-- Hapus Bagian -->
                            <form action="{{ route('admin.faq.destroySection') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bagian {{ addslashes($bagian) }} dan seluruh pertanyaan di dalamnya?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="kategori_faq" value="{{ $bagian }}">
                                <button type="submit" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-lg transition shadow-sm"
                                        title="Hapus bagian ini beserta seluruh pertanyaan di dalamnya">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- FAQ Items List --}}
                    @if($validQuestions->count() > 0)
                        <div class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($validQuestions as $faq)
                                <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-700 transition flex flex-col md:flex-row md:items-start justify-between gap-4">
                                    <div class="space-y-2 flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-bold text-slate-900 dark:text-white text-base leading-snug">
                                                {{ $faq->pertanyaan }}
                                            </h3>
                                        </div>
                                        <div class="text-sm text-slate-800 dark:text-slate-100 leading-relaxed bg-slate-50 dark:bg-slate-900 p-3.5 rounded-lg border border-slate-200 dark:border-slate-700 whitespace-pre-line">
                                            {{ $faq->jawaban }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0 self-end md:self-start pt-2 md:pt-0">
                                        <button @click="openEditModal({{ json_encode($faq) }})" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-white text-xs font-bold rounded-lg transition border border-slate-300 dark:border-slate-500 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.faq.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center bg-slate-50/50 dark:bg-slate-800/50">
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Belum ada pertanyaan di bagian ini. Klik tombol <span class="font-bold text-red-600 dark:text-red-400">+ Tambah Judul Baru</span> di atas untuk menambahkan pertanyaan pertama.
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
            <svg class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum ada Bagian atau Data FAQ</h3>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 mb-4">Klik tombol di bawah ini untuk membuat Bagian baru.</p>
            <button @click="openCreateSectionModal()" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition shadow-sm">
                + Tambah Bagian Baru
            </button>
        </div>
    @endif

    {{-- Modal 1: Tambah Bagian Baru (HANYA ISIAN NAMA BAGIAN) --}}
    <div x-show="showCreateSectionModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900 bg-opacity-75 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showCreateSectionModal = false" class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Bagian Baru</h3>
                <button @click="showCreateSectionModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.faq.storeSection') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label for="new_section_name" class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">
                        Nama Bagian
                    </label>
                    <input type="text" 
                           id="new_section_name" 
                           name="kategori_faq" 
                           x-model="newSectionOnlyName"
                           placeholder="Contoh: Pembayaran, Akun, Konten, Teknis" 
                           required 
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-red-600 focus:border-red-600 text-sm font-medium">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                        Bagian baru akan dibuat. Setelah dibuat, Anda dapat menambahkan pertanyaan/judul ke dalam bagian ini.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="showCreateSectionModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-sm font-bold rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                        Simpan Bagian
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal 2: Tambah Judul Baru ke Dalam Bagian --}}
    <div x-show="showCreateModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900 bg-opacity-75 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showCreateModal = false" class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                    Tambah Judul Baru ke Bagian: <span class="text-red-600 dark:text-red-400" x-text="createKategori"></span>
                </h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.faq.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                {{-- Nama Bagian Terkunci --}}
                <div class="p-3.5 bg-red-50 dark:bg-slate-900 border border-red-200 dark:border-slate-700 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Bagian Target:</span>
                        <span class="text-sm font-extrabold text-red-600 dark:text-red-400" x-text="createKategori"></span>
                    </div>
                    <input type="hidden" name="kategori_faq" x-model="createKategori">
                </div>

                {{-- 1. Judul Baru (Pertanyaan) --}}
                <div>
                    <label for="create_pertanyaan" class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">
                        Judul Baru (Pertanyaan)
                    </label>
                    <input type="text" 
                           id="create_pertanyaan" 
                           name="pertanyaan" 
                           placeholder="Tuliskan pertanyaan / judul FAQ baru..." 
                           required 
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-red-600 focus:border-red-600 text-sm font-medium">
                </div>

                {{-- 2. Isian (Jawaban) --}}
                <div>
                    <label for="create_jawaban" class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">
                        Isian (Jawaban)
                    </label>
                    <textarea id="create_jawaban" 
                              name="jawaban" 
                              rows="4" 
                              placeholder="Tuliskan jawaban / isian detail FAQ..." 
                              required 
                              class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-red-600 focus:border-red-600 text-sm font-medium"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-sm font-bold rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                        Simpan Judul & Isian
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal 3: Edit Pertanyaan FAQ --}}
    <div x-show="showEditModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900 bg-opacity-75 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showEditModal = false" class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Pertanyaan FAQ</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/admin/faq') }}/' + editId" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                {{-- 1. Nama Bagian --}}
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">
                        Nama Bagian
                    </label>
                    <input type="text" 
                           name="kategori_faq" 
                           x-model="editKategori"
                           list="edit_sections_list" 
                           required 
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-600 focus:border-red-600 text-sm font-medium">
                    <datalist id="edit_sections_list">
                        @foreach($sections as $sec)
                            <option value="{{ $sec }}"></option>
                        @endforeach
                    </datalist>
                </div>

                {{-- 2. Judul Baru (Pertanyaan) --}}
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">
                        Judul (Pertanyaan)
                    </label>
                    <input type="text" 
                           name="pertanyaan" 
                           x-model="editPertanyaan"
                           required 
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-600 focus:border-red-600 text-sm font-medium">
                </div>

                {{-- 3. Isian (Jawaban) --}}
                <div>
                    <label class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">
                        Isian (Jawaban)
                    </label>
                    <textarea name="jawaban" 
                              x-model="editJawaban"
                              rows="4" 
                              required 
                              class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-600 focus:border-red-600 text-sm font-medium"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-sm font-bold rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                        Update FAQ
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal 4: Edit Nama Bagian --}}
    <div x-show="showEditSectionModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900 bg-opacity-75 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showEditSectionModal = false" class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Nama Bagian</h3>
                <button @click="showEditSectionModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.faq.updateSection') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="old_kategori_faq" x-model="oldSectionName">

                <div>
                    <label for="new_kategori_faq" class="block text-sm font-bold text-slate-900 dark:text-slate-100 mb-1">
                        Nama Bagian Baru
                    </label>
                    <input type="text" 
                           id="new_kategori_faq" 
                           name="new_kategori_faq" 
                           x-model="newSectionName"
                           required 
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-600 focus:border-red-600 text-sm font-medium">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                        Mengubah nama bagian ini akan otomatis mengubah nama bagian pada seluruh pertanyaan di dalamnya.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="showEditSectionModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-sm font-bold rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                        Simpan Nama Bagian
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')
@section('title', 'Edit Direktori Kategori')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-100 font-bold">Edit Direktori</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    search: '{{ request('q', '') }}',
    showCreateModal: false,
    newNamaKategori: '',

    showEditModal: false,
    editId: null,
    editNamaKategori: '',
    editActionUrl: '',

    showDeleteModal: false,
    deleteId: null,
    deleteNamaKategori: '',
    deleteCount: 0,
    deleteActionUrl: '',

    openCreateModal() {
        this.newNamaKategori = '';
        this.showCreateModal = true;
        this.$nextTick(() => {
            if (this.$refs.createInput) this.$refs.createInput.focus();
        });
    },

    openEditModal(category) {
        this.editId = category.id;
        this.editNamaKategori = category.nama_kategori;
        this.editActionUrl = '{{ url('admin/direktori-kategori') }}/' + category.id;
        this.showEditModal = true;
        this.$nextTick(() => {
            if (this.$refs.editInput) this.$refs.editInput.focus();
        });
    },

    openDeleteModal(category) {
        this.deleteId = category.id;
        this.deleteNamaKategori = category.nama_kategori;
        this.deleteCount = category.knowledge_count || 0;
        this.deleteActionUrl = '{{ url('admin/direktori-kategori') }}/' + category.id;
        this.showDeleteModal = true;
    }
}">

    {{-- Top Header Banner --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 mb-1.5">
                <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                    DIREKTORI KATEGORI
                </h1>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                Kelola master taksonomi jenis direktori pengetahuan untuk katalog repositori MojoPedia.
            </p>
        </div>

        {{-- Action Button: Tambah Direktori Baru --}}
        <div class="flex items-center gap-3">
            <button @click="openCreateModal()" 
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition hover:shadow cursor-pointer shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Direktori Baru</span>
            </button>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-start gap-3 shadow-sm" role="alert">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-start gap-3 shadow-sm" role="alert">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 font-medium">{{ session('error') }}</div>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200 text-sm flex items-start gap-3 shadow-sm" role="alert">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 font-medium">{{ session('info') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm shadow-sm" role="alert">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Validasi Gagal:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter & Actions Bar (Dibuat identik seperti search bar di halaman Pengetahuan) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        {{-- Search Form: w-full sm:w-64 --}}
        <form method="GET" action="{{ route('admin.categories.index') }}" class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" 
                   name="q" 
                   value="{{ request('q') }}" 
                   placeholder="Pencarian..."
                   class="w-full pl-9 {{ request('q') ? 'pr-8' : 'pr-3' }} py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-primary-600 focus:border-primary-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition shadow-2xs">
            @if(request('q'))
                <a href="{{ route('admin.categories.index') }}" 
                   class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" 
                   title="Hapus pencarian">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </form>

        {{-- Stats Badges --}}
        <div class="flex items-center gap-2.5 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Total Direktori: <strong class="text-slate-900 dark:text-white">{{ $categories->count() }}</strong>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Total Pengetahuan: <strong class="text-slate-900 dark:text-white">{{ $categories->sum('knowledge_count') }}</strong>
            </span>
        </div>
    </div>

    {{-- Grid of Categories Cards (Preserving Exact Style from Screenshot) --}}
    @if($categories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($categories as $category)
                <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    {{-- Card Top: Icon & Category Info --}}
                    <div>
                        <div class="flex items-start gap-3.5 mb-4">
                            {{-- Book Icon Container --}}
                            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/50 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>

                            {{-- Title & Subtitle --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $category->nama_kategori }}">
                                    {{ $category->nama_kategori }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Koleksi dokumen dan aset terdaftar
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Card Bottom: Counter Badge & Actions --}}
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                        {{-- Counter Pill Badge --}}
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                            {{ $category->knowledge_count }} Pengetahuan
                        </span>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-1">
                            {{-- Tombol Edit --}}
                            <button type="button" 
                                    @click="openEditModal({{ json_encode($category) }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition" 
                                    title="Edit Nama Direktori">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            {{-- Tombol Hapus --}}
                            <button type="button" 
                                    @click="openDeleteModal({{ json_encode($category) }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-slate-700 transition" 
                                    title="Hapus Direktori">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>

                            {{-- Link Lihat di Katalog Publik (Chevron Icon seperti di screenshot) --}}
                            <a href="{{ route('category.index', ['kategori' => $category->id]) }}" 
                               target="_blank"
                               class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition" 
                               title="Buka Koleksi Pengetahuan">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Belum Ada Direktori Kategori</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-5">
                @if(request('q'))
                    Tidak ditemukan direktori kategori dengan kata kunci "{{ request('q') }}".
                @else
                    Belum ada kategori yang terdaftar dalam sistem repositori pengetahuan.
                @endif
            </p>
            <button @click="openCreateModal()" 
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Direktori Baru
            </button>
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- MODAL TAMBAH DIREKTORI BARU                            --}}
    {{-- ======================================================== --}}
    <div x-show="showCreateModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showCreateModal = false" 
             class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Tambah Direktori Baru</h3>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label for="create_nama_kategori" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1.5">
                            Nama Direktori Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="create_nama_kategori" 
                               name="nama_kategori" 
                               x-ref="createInput"
                               x-model="newNamaKategori" 
                               required 
                               placeholder="Contoh: Infrastruktur & Keamanan" 
                               class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 placeholder-slate-400 transition">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                            Gunakan nama yang representatif agar taksonomi repositori tetap rapi.
                        </p>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="showCreateModal = false" 
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl shadow-sm transition cursor-pointer">
                        Simpan Direktori
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL EDIT DIREKTORI                                    --}}
    {{-- ======================================================== --}}
    <div x-show="showEditModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showEditModal = false" 
             class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Edit Direktori Kategori</h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="editActionUrl" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label for="edit_nama_kategori" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1.5">
                            Nama Direktori Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="edit_nama_kategori" 
                               name="nama_kategori" 
                               x-ref="editInput"
                               x-model="editNamaKategori" 
                               required 
                               class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 placeholder-slate-400 transition">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                            Perubahan nama kategori akan langsung memperbarui seluruh dokumen pengetahuan terkait.
                        </p>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="showEditModal = false" 
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl shadow-sm transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL HAPUS DIREKTORI                                   --}}
    {{-- ======================================================== --}}
    <div x-show="showDeleteModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showDeleteModal = false" 
             class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="p-6">
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Hapus Direktori Kategori?</h3>
                
                <template x-if="deleteCount > 0">
                    <div class="space-y-3">
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            Direktori <strong class="text-slate-900 dark:text-white" x-text="'\'' + deleteNamaKategori + '\''"></strong> saat ini masih memuat <span class="font-bold text-rose-600 dark:text-rose-400" x-text="deleteCount + ' dokumen pengetahuan'"></span>.
                        </p>
                        <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs leading-relaxed">
                            <strong>Perhatian:</strong> Sistem tidak mengizinkan penghapusan direktori yang masih memiliki relasi data agar tidak merusak katalog dokumen. Silakan ubah atau pindahkan dokumen terkait terlebih dahulu.
                        </div>
                    </div>
                </template>

                <template x-if="deleteCount === 0">
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        Apakah Anda yakin ingin menghapus direktori <strong class="text-slate-900 dark:text-white" x-text="'\'' + deleteNamaKategori + '\''"></strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </template>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-2.5">
                <button type="button" 
                        @click="showDeleteModal = false" 
                        class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                
                <template x-if="deleteCount === 0">
                    <form :action="deleteActionUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-xl shadow-sm transition cursor-pointer">
                            Hapus Direktori
                        </button>
                    </form>
                </template>

                <template x-if="deleteCount > 0">
                    <button type="button" 
                            disabled 
                            class="px-5 py-2 text-sm font-semibold text-slate-400 bg-slate-200 dark:bg-slate-700 rounded-xl cursor-not-allowed opacity-60">
                        Tidak Dapat Dihapus
                    </button>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

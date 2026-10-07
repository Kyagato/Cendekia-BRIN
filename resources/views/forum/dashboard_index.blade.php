@extends('layouts.admin')
@section('title', 'Forum Saya')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Forum Saya</li>
@endsection

@section('content')
<div class="space-y-6" x-data="{
    viewMode: '{{ ($errors->any() || request('action') === 'create' || !empty($linkedKnowledge)) ? 'create' : 'list' }}',
    activeItem: null,
    editItem: null,
    showDeleteModal: false,
    deleteTarget: null,
    openEdit(item) {
        this.editItem = JSON.parse(JSON.stringify(item));
        this.viewMode = 'edit';
    }
}">

    {{-- Stats Cards (Hanya tampil saat mode list) --}}
    <div x-show="viewMode === 'list'" x-transition class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Topik Saya</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $counts['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Disetujui / Tayang</div>
            <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 mt-1">{{ $counts['approved'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-amber-600 dark:text-amber-400">Menunggu Review</div>
            <div class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">{{ $counts['pending'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
            <div class="text-xs font-semibold text-red-600 dark:text-red-400">Ditolak</div>
            <div class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">{{ $counts['rejected'] }}</div>
        </div>
    </div>

    {{-- VIEW 1: DAFTAR TOPIK DISKUSI SAYA (LIST VIEW) --}}
    <div x-show="viewMode === 'list'" x-transition class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Daftar Topik Diskusi Saya</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola topik diskusi forum yang Anda buat</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.forum.trash') }}" class="group inline-flex items-center gap-2 bg-red-50/50 hover:bg-red-50 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-600 dark:text-red-400 px-3.5 py-2 rounded-lg text-sm font-semibold transition border border-red-300 dark:border-red-800 shadow-xs">
                    <svg class="w-4 h-4 text-red-500 dark:text-red-400 overflow-visible transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path class="trash-lid" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16 M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" style="transform-origin: 4px 7px;" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6" />
                    </svg>
                    <span>Tong Sampah ({{ $counts['trashed'] }})</span>
                </a>
                <button type="button" @click="viewMode = 'create'" class="group inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 transition-transform group-hover-spin-brief" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Topik Forum</span>
                </button>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('dashboard.forum.index') }}" class="relative w-full md:w-80">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari topik diskusi..."
                       class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-primary-600 focus:border-primary-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
            </form>

            <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto">
                <a href="{{ route('dashboard.forum.index', request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ !request('status') ? 'bg-primary-50 dark:bg-slate-700 text-primary-600 dark:text-primary-400 border-primary-200 dark:border-slate-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Semua ({{ $counts['total'] }})
                </a>
                <a href="{{ route('dashboard.forum.index', array_merge(request()->all(), ['status' => 'approved'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('status') === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Disetujui ({{ $counts['approved'] }})
                </a>
                <a href="{{ route('dashboard.forum.index', array_merge(request()->all(), ['status' => 'pending'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('status') === 'pending' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Menunggu ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('dashboard.forum.index', array_merge(request()->all(), ['status' => 'rejected'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ request('status') === 'rejected' ? 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    Ditolak ({{ $counts['rejected'] }})
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Judul Topik</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kategori</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Statistik</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Dibuat</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($threads as $item)
                    @php
                        $threadData = [
                            'id' => $item->id,
                            'judul' => $item->judul,
                            'konten' => $item->konten,
                            'category_id' => $item->category_id,
                            'kategori' => $item->category->nama_kategori ?? '-',
                            'status' => $item->status,
                            'rejection_note' => $item->rejection_note,
                            'is_pinned' => (bool)$item->is_pinned,
                            'is_locked' => (bool)$item->is_locked,
                            'knowledge_id' => $item->knowledge_id,
                            'knowledge_title' => $item->knowledge->judul ?? null,
                            'created_at' => $item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-',
                            'views_count' => $item->views_count ?? 0,
                            'replies_count' => $item->replies_count ?? 0,
                            'author' => [
                                'name' => $item->user->name ?? 'Pengguna',
                                'foto_profil' => $item->user->foto_profil ?? null,
                                'instansi' => $item->user->instansi ?? ($item->user->role ?? 'Anggota'),
                            ],
                            'replies' => $item->replies->map(fn($r) => [
                                'id' => $r->id,
                                'konten' => $r->konten,
                                'created_at' => $r->created_at ? $r->created_at->translatedFormat('d M Y, H:i') : '-',
                                'user' => [
                                    'name' => $r->user->name ?? 'Anonim',
                                    'foto_profil' => $r->user->foto_profil ?? null,
                                    'instansi' => $r->user->instansi ?? '',
                                ]
                            ])->values()->all(),
                            'public_url' => route('forum.show', $item->id),
                            'delete_url' => route('forum.destroy', $item->id),
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                        <td class="py-4 px-6 max-w-sm">
                            <button type="button"
                                    @click="activeItem = @js($threadData); viewMode = 'preview';"
                                    class="text-left font-semibold text-sm text-slate-800 dark:text-slate-100 hover:text-primary-600 dark:hover:text-primary-400 line-clamp-1 cursor-pointer transition">
                                {{ $item->judul }}
                            </button>
                            <div class="text-xs text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">
                                {{ Str::limit(strip_tags($item->konten), 90) }}
                            </div>
                            @if($item->knowledge)
                            <div class="mt-1 text-[11px] text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                <span>🔗 Terkait:</span>
                                <span class="font-medium truncate">{{ $item->knowledge->judul }}</span>
                            </div>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600 dark:text-slate-300">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                {{ $item->category->nama_kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            @if($item->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Disetujui
                                </span>
                            @elseif($item->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-950 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800" title="{{ $item->rejection_note }}">
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400">
                            <div>{{ $item->views_count ?? 0 }} tayangan</div>
                            <div>{{ $item->replies_count ?? 0 }} balasan</div>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400">
                            {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end" x-data="{ open: false }">
                                <div class="relative inline-block text-left" @mouseenter="open = true" @mouseleave="open = false">
                                    {{-- Tombol Titik Tiga --}}
                                    <button @click="open = !open" type="button" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition focus:outline-none cursor-pointer">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>

                                    {{-- Menu Dropdown Hover --}}
                                    <div x-show="open" x-cloak x-transition
                                         class="absolute right-0 z-30 mt-1 w-40 origin-top-right rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 focus:outline-none py-1.5 divide-y divide-slate-100 dark:divide-slate-700/60"
                                         style="display: none;">
                                        
                                        <div class="py-1">
                                             {{-- 1. Lihat (In-Page Switch View ke Form Detail Forum di Dashboard) --}}
                                            <button type="button"
                                                    @click="activeItem = @js($threadData); viewMode = 'preview'; open = false;"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Lihat</span>
                                            </button>
                                        </div>

                                        <div class="py-1">
                                            {{-- 2. Edit Topik --}}
                                            <button type="button"
                                                    @click="openEdit(@js($threadData)); open = false;"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </button>
                                        </div>

                                        <div class="py-1">
                                            {{-- 2. Hapus ke Tong Sampah --}}
                                            <button type="button"
                                                    @click="deleteTarget = {
                                                        id: {{ $item->id }},
                                                        judul: @js($item->judul),
                                                        delete_url: @js(route('forum.destroy', $item->id))
                                                    }; showDeleteModal = true; open = false"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Belum ada topik diskusi yang Anda buat</p>
                            <button type="button" @click="viewMode = 'create'" class="inline-block mt-3 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shadow-xs cursor-pointer">
                                Mulai Diskusi Sekarang
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($threads->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
            {{ $threads->links() }}
        </div>
        @endif
    </div>

    {{-- VIEW 2: FORM TAMBAH TOPIK FORUM (IN-PAGE SWITCH VIEW SECARA UTUH) --}}
    <div x-show="viewMode === 'create'" x-cloak x-transition class="space-y-6" style="display: none;">
        {{-- Top Bar Navigation Form --}}
        <div class="flex items-center justify-between">
            <button type="button" @click="viewMode = 'list'" class="inline-flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 font-semibold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Daftar Topik
            </button>
        </div>

        {{-- Card Form Utama --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Buat Topik Diskusi Baru</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Mulai diskusi, ajukan pertanyaan, atau bagikan wawasan dengan komunitas SPBE langsung dari dashboard Anda.</p>
            </div>

            <div class="p-6 sm:p-8">
                <form action="{{ route('forum.store') }}" method="POST"
                      x-data="{
                          judul: '{{ addslashes(old('judul', !empty($linkedKnowledge) ? "Diskusi: {$linkedKnowledge->judul}" : '')) }}',
                          categoryId: '{{ old('category_id', !empty($linkedKnowledge) ? $linkedKnowledge->category_id : '') }}',
                          searchKnowledge: '',
                          selectedKnowledge: {{ old('knowledge_id') ? json_encode($knowledges->where('id', old('knowledge_id'))->map(fn($k) => ['id' => $k->id, 'judul' => $k->judul, 'category_id' => $k->category_id, 'kategori' => $k->category->nama_kategori ?? null])->first()) : (!empty($linkedKnowledge) ? json_encode(['id' => $linkedKnowledge->id, 'judul' => $linkedKnowledge->judul, 'category_id' => $linkedKnowledge->category_id, 'kategori' => $linkedKnowledge->category->nama_kategori ?? null]) : 'null') }},
                          isDropdownOpen: false,
                          knowledges: {{ json_encode($knowledges->map(fn($k) => ['id' => $k->id, 'judul' => $k->judul, 'category_id' => $k->category_id, 'kategori' => $k->category->nama_kategori ?? null])) }},
                          get filteredKnowledges() {
                              if (!this.searchKnowledge) return this.knowledges.slice(0, 10);
                              return this.knowledges.filter(k => k.judul.toLowerCase().includes(this.searchKnowledge.toLowerCase())).slice(0, 10);
                          },
                          select(item) {
                              this.selectedKnowledge = item;
                              this.searchKnowledge = '';
                              this.isDropdownOpen = false;

                              // Otomatis isi kategori sesuai materi yang dipilih (sama seperti di beranda utama)
                              if (item.category_id) {
                                  this.categoryId = item.category_id;
                              }

                              // Otomatis isi judul topik jika masih kosong atau berawalan 'Diskusi: '
                              if (!this.judul || this.judul.startsWith('Diskusi: ')) {
                                  this.judul = 'Diskusi: ' + item.judul;
                              }
                          },
                          clear() {
                              this.selectedKnowledge = null;
                              this.searchKnowledge = '';
                          }
                      }">
                    @csrf
                    <input type="hidden" name="ref" value="dashboard">

                    {{-- Hubungkan ke Materi Pengetahuan (Single Search & Selection Autocomplete) --}}
                    <div class="mb-6 relative" @click.outside="isDropdownOpen = false">

                        <input type="hidden" name="knowledge_id" :value="selectedKnowledge ? selectedKnowledge.id : ''">

                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Hubungkan ke Materi Pengetahuan <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span>
                        </label>

                        <!-- Input Pencarian -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text"
                                   x-model="searchKnowledge"
                                   @focus="isDropdownOpen = true"
                                   placeholder="🔍 Cari dan pilih materi pengetahuan..."
                                   autocomplete="off"
                                   class="w-full pl-10 pr-10 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition text-sm">

                            <button type="button"
                                    x-show="searchKnowledge"
                                    @click="searchKnowledge = ''"
                                    class="absolute right-3 top-3.5 text-slate-400 hover:text-red-500 dark:hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Dropdown Hasil Pencarian -->
                        <div x-show="isDropdownOpen"
                             x-cloak
                             class="absolute z-30 mt-1.5 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
                            <template x-for="item in filteredKnowledges" :key="item.id">
                                <div @click="select(item)"
                                     class="p-3 hover:bg-slate-50 dark:hover:bg-slate-700/60 cursor-pointer transition flex items-center justify-between text-sm">
                                    <span class="font-medium text-slate-800 dark:text-slate-200 line-clamp-1" x-text="item.judul"></span>
                                    <span x-show="item.kategori" class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0 ml-2" x-text="item.kategori"></span>
                                </div>
                            </template>
                            <div x-show="filteredKnowledges.length === 0" class="p-3 text-center text-xs text-slate-400">
                                Materi tidak ditemukan
                            </div>
                        </div>

                        <!-- Badge Terpilih -->
                        <div x-show="selectedKnowledge" x-cloak class="mt-2.5 flex items-center gap-2 p-2.5 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                            <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            <span class="font-semibold truncate flex-1" x-text="selectedKnowledge ? selectedKnowledge.judul : ''"></span>
                            <button type="button" @click="clear()" class="text-red-500 hover:text-red-700 text-xs font-semibold ml-2 shrink-0">Hapus</button>
                        </div>
                    </div>

                    {{-- Judul Topik --}}
                    <div class="mb-6">
                        <label for="dashboard_judul" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Judul Topik Diskusi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="dashboard_judul" name="judul" required
                               x-model="judul"
                               value="{{ old('judul') }}"
                               placeholder="Contoh: Bagaimana implementasi arsitektur SPBE pada instansi daerah?"
                               class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition text-sm">
                        @error('judul') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-6">
                        <label for="dashboard_category_id" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="dashboard_category_id" name="category_id" required
                                x-model="categoryId"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-primary-600 focus:border-primary-600 transition text-sm">
                            <option value="" disabled class="text-slate-400 dark:text-slate-500">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Konten / Pertanyaan --}}
                    <div class="mb-8">
                        <label for="dashboard_konten" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Konten / Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="dashboard_konten" name="konten" rows="8" required
                                  placeholder="Jelaskan secara detail topik diskusi atau pertanyaan Anda di sini..."
                                  class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition resize-y text-sm">{{ old('konten') }}</textarea>
                        @error('konten') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="viewMode = 'list'"
                                class="px-6 py-3 rounded-lg font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition text-sm cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center gap-2 cursor-pointer">
                            Kirim Topik
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- VIEW 3: FORM / PRATINJAU DETAIL TOPIK DISKUSI SAYA (PERSIS SEPERTI FORUM BERANDA UTAMA) --}}
    <div x-show="viewMode === 'preview'" x-cloak x-transition class="space-y-6" style="display: none;">
        {{-- Top Bar Navigation Form --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <button type="button" @click="viewMode = 'list'" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-semibold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Forum Saya
            </button>
            <div class="flex items-center gap-3">
                <template x-if="activeItem?.status === 'approved'">
                    <a :href="activeItem?.public_url" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        Buka di Forum Publik
                    </a>
                </template>
                <button type="button" @click="openEdit(activeItem)"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 transition shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Topik
                </button>
                <button type="button" @click="deleteTarget = activeItem; showDeleteModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Hapus ke Tong Sampah
                </button>
            </div>
        </div>

        {{-- Status Alert Box --}}
        <template x-if="activeItem?.status === 'rejected'">
            <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl flex items-start justify-between gap-3 text-sm shadow-xs">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="font-bold block">Topik Ditolak</strong>
                        <span x-text="activeItem?.rejection_note || 'Topik ini ditolak oleh moderator. Silakan perbaiki isi topik sebelum mengajukan kembali.'"></span>
                    </div>
                </div>
                <button type="button" @click="openEdit(activeItem)" class="shrink-0 px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Perbaiki & Ajukan Kembali</span>
                </button>
            </div>
        </template>
        <template x-if="activeItem?.status === 'pending'">
            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl flex items-center gap-3 text-sm shadow-xs">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <span>Topik ini sedang dalam tahap <strong>Menunggu Peninjauan</strong> oleh tim moderator sebelum ditayangkan secara publik.</span>
                </div>
            </div>
        </template>
        <template x-if="activeItem?.status === 'approved'">
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3 text-sm shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <span>Topik diskusi ini telah <strong>Disetujui</strong> dan aktif ditayangkan di forum publik.</span>
                </div>
            </div>
        </template>

        {{-- Card Pratinjau Detail Konten Topik (Identik dengan Forum Publik Beranda Utama) --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden"
             :class="activeItem?.is_pinned ? 'border-primary-600 dark:border-primary-500' : ''">
            <div class="p-6 sm:p-8">
                <!-- Meta Badges -->
                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-4">
                    <span x-show="activeItem?.is_pinned" class="bg-blue-50 text-primary-600 dark:bg-blue-950 dark:text-blue-300 text-xs px-2.5 py-0.5 rounded-full font-semibold border border-blue-200/60">
                        📌 Pinned
                    </span>
                    <span x-show="activeItem?.is_locked" class="bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-xs px-2.5 py-0.5 rounded-full font-semibold">
                        🔒 Dikunci
                    </span>
                    <span :class="{
                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800': activeItem?.status === 'approved',
                        'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border-amber-200 dark:border-amber-800': activeItem?.status === 'pending',
                        'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 border-red-200 dark:border-red-800': activeItem?.status === 'rejected'
                    }" class="px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                    x-text="activeItem?.status === 'approved' ? 'Disetujui' : (activeItem?.status === 'rejected' ? 'Ditolak' : 'Menunggu')">
                    </span>
                    <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-2.5 py-0.5 rounded-full text-xs font-medium border border-slate-200 dark:border-slate-700"
                          x-text="activeItem?.kategori">
                    </span>
                    <span class="text-xs" x-text="activeItem?.created_at"></span>
                    <span class="flex items-center gap-1 text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <span x-text="activeItem?.views_count || 0"></span> tayangan
                    </span>
                </div>

                <!-- Linked Knowledge Banner (Membahas Pengetahuan) -->
                <template x-if="activeItem?.knowledge_title">
                    <div class="mb-6 p-4 bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/60 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/50 text-primary-600 dark:text-blue-400 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-primary-600 dark:text-blue-400 uppercase tracking-wider block">Membahas Pengetahuan</span>
                                <a :href="'/knowledge/' + activeItem?.knowledge_id" target="_blank" class="text-sm font-semibold text-slate-900 dark:text-slate-100 hover:text-primary-600 transition" x-text="activeItem?.knowledge_title">
                                </a>
                            </div>
                        </div>
                        <a :href="'/knowledge/' + activeItem?.knowledge_id" target="_blank" class="shrink-0 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                            Lihat Materi
                        </a>
                    </div>
                </template>

                <!-- Thread Title & Content -->
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-6" x-text="activeItem?.judul"></h1>
                <div class="prose prose-slate dark:prose-invert max-w-none text-sm text-slate-700 dark:text-slate-300 leading-relaxed mb-8 whitespace-pre-line" x-text="activeItem?.konten"></div>

                <!-- Author Info -->
                <div class="flex items-center gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <template x-if="activeItem?.author?.foto_profil">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0">
                            <img :src="'/storage/' + activeItem.author.foto_profil" class="w-full h-full object-cover" :alt="activeItem.author.name" />
                        </div>
                    </template>
                    <template x-if="!activeItem?.author?.foto_profil">
                        <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm shrink-0"
                             x-text="activeItem?.author?.name ? activeItem.author.name.charAt(0).toUpperCase() : 'U'">
                        </div>
                    </template>
                    <div>
                        <span class="font-semibold text-slate-900 dark:text-slate-100 text-sm block" x-text="activeItem?.author?.name"></span>
                        <p class="text-xs text-slate-400 dark:text-slate-500" x-text="activeItem?.author?.instansi || ''"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Balasan / Replies Section (Persis Seperti Forum Publik Beranda Utama) --}}
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4"
                x-text="(activeItem?.replies?.length || 0) + ' Balasan'">
            </h2>

            <div class="space-y-4">
                <template x-for="reply in (activeItem?.replies || [])" :key="reply.id">
                    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
                        <div class="flex items-start gap-3">
                            <template x-if="reply.user?.foto_profil">
                                <div class="w-9 h-9 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0">
                                    <img :src="'/storage/' + reply.user.foto_profil" class="w-full h-full object-cover" :alt="reply.user.name" />
                                </div>
                            </template>
                            <template x-if="!reply.user?.foto_profil">
                                <div class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-xs shrink-0"
                                     x-text="reply.user?.name ? reply.user.name.charAt(0).toUpperCase() : 'U'">
                                </div>
                            </template>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-sm text-slate-900 dark:text-slate-100" x-text="reply.user?.name"></span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500" x-text="reply.created_at"></span>
                                </div>
                                <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line" x-text="reply.konten"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!activeItem?.replies || activeItem.replies.length === 0">
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-8 text-center text-sm text-slate-500 dark:text-slate-400">
                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Belum ada balasan untuk topik diskusi ini.
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- VIEW 4: FORM EDIT TOPIK DISKUSI (IN-PAGE SWITCH VIEW) --}}
    <div x-show="viewMode === 'edit'" x-cloak x-transition class="space-y-6" style="display: none;">
        {{-- Top Bar Navigation Form --}}
        <div class="flex items-center justify-between">
            <button type="button" @click="viewMode = 'list'" class="inline-flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 font-semibold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Daftar Topik
            </button>
        </div>

        {{-- Card Form Edit --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                            <span x-text="editItem?.status === 'rejected' ? 'Perbaiki & Ajukan Kembali Topik' : 'Edit Topik Diskusi'"></span>
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Perbarui rincian topik diskusi Anda.
                        </p>
                    </div>
                    <template x-if="editItem?.status === 'rejected'">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-950 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800">
                            Status: Ditolak
                        </span>
                    </template>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                {{-- Banner Khusus Jika Status Ditolak --}}
                <template x-if="editItem?.status === 'rejected'">
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-xl text-sm flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="flex-1">
                            <strong class="font-bold text-red-900 dark:text-red-200 block">Topik Diskusi Ini Sebelumnya Ditolak</strong>
                            <p class="text-red-700 dark:text-red-300 mt-1">
                                <span class="font-semibold">Alasan Penolakan:</span>
                                <span class="italic" x-text="editItem?.rejection_note || 'Tidak ada catatan spesifik dari moderator.'"></span>
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2">
                                Silakan sesuaikan judul, kategori, atau isi konten di bawah ini. Klik tombol <strong>Ajukan Kembali</strong> untuk mengirim ulang ke status menunggu persetujuan moderator.
                            </p>
                        </div>
                    </div>
                </template>

                <form :action="'/forum/' + (editItem ? editItem.id : '')" method="POST" x-data="{ resubmitFlag: 0 }">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="ref" value="dashboard">
                    <input type="hidden" name="resubmit" :value="resubmitFlag">

                    {{-- Hubungkan ke Materi Pengetahuan --}}
                    <div class="mb-6 relative"
                         x-data="{
                             searchKnowledgeEdit: '',
                             isDropdownOpenEdit: false,
                             knowledges: {{ json_encode($knowledges->map(fn($k) => ['id' => $k->id, 'judul' => $k->judul, 'category_id' => $k->category_id, 'kategori' => $k->category->nama_kategori ?? null])) }},
                             get filteredKnowledgesEdit() {
                                 if (!this.searchKnowledgeEdit) return this.knowledges.slice(0, 10);
                                 return this.knowledges.filter(k => k.judul.toLowerCase().includes(this.searchKnowledgeEdit.toLowerCase())).slice(0, 10);
                             },
                             select(item) {
                                 if (editItem) {
                                     editItem.knowledge_id = item.id;
                                     editItem.knowledge_title = item.judul;
                                     if (item.category_id) {
                                         editItem.category_id = item.category_id;
                                     }
                                     if (!editItem.judul || editItem.judul.startsWith('Diskusi: ')) {
                                         editItem.judul = 'Diskusi: ' + item.judul;
                                     }
                                 }
                                 this.searchKnowledgeEdit = '';
                                 this.isDropdownOpenEdit = false;
                             },
                             clear() {
                                 if (editItem) {
                                     editItem.knowledge_id = null;
                                     editItem.knowledge_title = null;
                                 }
                                 this.searchKnowledgeEdit = '';
                             }
                         }"
                         @click.outside="isDropdownOpenEdit = false">

                        <input type="hidden" name="knowledge_id" :value="editItem?.knowledge_id || ''">

                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Hubungkan ke Materi Pengetahuan <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span>
                        </label>

                        <!-- Input Pencarian -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text"
                                   x-model="searchKnowledgeEdit"
                                   @focus="isDropdownOpenEdit = true"
                                   placeholder="🔍 Cari dan ganti materi pengetahuan..."
                                   autocomplete="off"
                                   class="w-full pl-10 pr-10 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition text-sm">

                            <button type="button"
                                    x-show="searchKnowledgeEdit"
                                    @click="searchKnowledgeEdit = ''"
                                    class="absolute right-3 top-3.5 text-slate-400 hover:text-red-500 dark:hover:text-red-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Dropdown Hasil Pencarian -->
                        <div x-show="isDropdownOpenEdit"
                             x-cloak
                             class="absolute z-30 mt-1.5 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
                            <template x-for="item in filteredKnowledgesEdit" :key="item.id">
                                <div @click="select(item)"
                                     class="p-3 hover:bg-slate-50 dark:hover:bg-slate-700/60 cursor-pointer transition flex items-center justify-between text-sm">
                                    <span class="font-medium text-slate-800 dark:text-slate-200 line-clamp-1" x-text="item.judul"></span>
                                    <span x-show="item.kategori" class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0 ml-2" x-text="item.kategori"></span>
                                </div>
                            </template>
                            <div x-show="filteredKnowledgesEdit.length === 0" class="p-3 text-center text-xs text-slate-400">
                                Materi tidak ditemukan
                            </div>
                        </div>

                        <!-- Badge Terpilih -->
                        <div x-show="editItem?.knowledge_title" x-cloak class="mt-2.5 flex items-center gap-2 p-2.5 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                            <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            <span class="font-semibold truncate flex-1" x-text="editItem?.knowledge_title || ''"></span>
                            <button type="button" @click="clear()" class="text-red-500 hover:text-red-700 text-xs font-semibold ml-2 shrink-0">Hapus</button>
                        </div>
                    </div>

                    {{-- Judul Topik --}}
                    <div class="mb-6">
                        <label for="edit_judul" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Judul Topik Diskusi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit_judul" name="judul" required
                               x-model="editItem.judul"
                               placeholder="Judul topik diskusi..."
                               class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition text-sm">
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-6">
                        <label for="edit_category_id" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_category_id" name="category_id" required
                                x-model="editItem.category_id"
                                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-primary-600 focus:border-primary-600 transition text-sm">
                            <option value="" disabled class="text-slate-400 dark:text-slate-500">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Konten / Pertanyaan --}}
                    <div class="mb-8">
                        <label for="edit_konten" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                            Konten / Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="edit_konten" name="konten" rows="8" required
                                  x-model="editItem.konten"
                                  placeholder="Jelaskan secara detail topik diskusi Anda di sini..."
                                  class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-primary-600 focus:border-primary-600 transition resize-y text-sm"></textarea>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                        <button type="button" @click="viewMode = 'list'"
                                class="px-6 py-3 rounded-lg font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition text-sm cursor-pointer">
                            Batal
                        </button>

                        {{-- Khusus Status Ditolak: Tombol Ajukan Kembali (Outline Kuning) dan Simpan Perubahan --}}
                        <template x-if="editItem?.status === 'rejected'">
                            <div class="flex items-center gap-3">
                                {{-- Tombol Ajukan Kembali (Outline Kuning) --}}
                                <button type="submit"
                                        @click="resubmitFlag = 1"
                                        class="px-5 py-3 border-2 border-amber-500 hover:border-amber-600 bg-white hover:bg-amber-50 dark:bg-slate-800 dark:hover:bg-amber-950/30 text-amber-600 hover:text-amber-700 dark:text-amber-400 font-semibold rounded-lg transition text-sm flex items-center gap-2 cursor-pointer shadow-xs">
                                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Ajukan Kembali</span>
                                </button>

                                {{-- Tombol Simpan Perubahan --}}
                                <button type="submit"
                                        @click="resubmitFlag = 0"
                                        class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </template>

                        {{-- Status Bukan Ditolak: Hanya Simpan Perubahan --}}
                        <template x-if="editItem?.status !== 'rejected'">
                            <button type="submit"
                                    @click="resubmitFlag = 0"
                                    class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Perubahan</span>
                            </button>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS KE TONG SAMPAH (UNIVERSAL LIST & PREVIEW) --}}
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm text-left">
        <div @click.outside="showDeleteModal = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Pindahkan ke Tong Sampah?</h3>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                    Topik diskusi "<strong class="text-slate-800 dark:text-slate-200" x-text="deleteTarget?.judul"></strong>" akan dipindahkan ke tong sampah forum Anda dan dapat dipulihkan kapan saja.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        Batal
                    </button>
                    <form :action="deleteTarget?.delete_url" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <input type="hidden" name="from_dashboard" value="1">
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm cursor-pointer">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

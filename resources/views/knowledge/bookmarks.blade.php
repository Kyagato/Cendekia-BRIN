@extends('layouts.admin')
@section('title', 'Bookmark')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <!-- Header -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-500 flex items-center justify-center">
                    <svg class="w-4 h-4 fill-amber-500" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Bookmark</h1>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar artikel dan aset pengetahuan yang Anda bookmark secara pribadi.</p>
        </div>
        <div class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
            Total: {{ $knowledges->total() }} Bookmark
        </div>
    </div>

    <!-- Filters -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row justify-between items-center gap-4">
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Search Form -->
            <form method="GET" action="{{ route('bookmarks.index') }}" class="relative w-full sm:w-64">
                @if(request('tipe')) <input type="hidden" name="tipe" value="{{ request('tipe') }}"> @endif
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari dalam tersimpan..."
                       class="w-full pl-9 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-primary-600 focus:border-primary-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500">
            </form>

            <!-- Filter Tipe -->
            <div x-data="{ open: false }" class="relative inline-block text-left">
                <button @click="open = !open" @click.away="open = false" type="button"
                        class="inline-flex items-center justify-between gap-2 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <span>Tipe {{ request('tipe') ? ': ' . request('tipe') : '' }}</span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-40 origin-top-left rounded-md bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black ring-opacity-5 border border-slate-200 dark:border-slate-700 focus:outline-none" style="display: none;">
                    <div class="py-1">
                        <a href="{{ route('bookmarks.index', array_merge(request()->except('tipe'))) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == '' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Semua Tipe</a>
                        <a href="{{ route('bookmarks.index', array_merge(request()->all(), ['tipe' => 'Teks'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Teks' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Teks</a>
                        <a href="{{ route('bookmarks.index', array_merge(request()->all(), ['tipe' => 'Video'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Video' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Video</a>
                        <a href="{{ route('bookmarks.index', array_merge(request()->all(), ['tipe' => 'Gambar'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Gambar' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Gambar</a>
                        <a href="{{ route('bookmarks.index', array_merge(request()->all(), ['tipe' => 'Audio'])) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 {{ request('tipe') == 'Audio' ? 'bg-slate-50 dark:bg-slate-700 font-bold' : '' }}">Audio</a>
                    </div>
                </div>
            </div>

            @if(request('q') || request('tipe'))
                <a href="{{ route('bookmarks.index') }}" class="text-xs text-rose-500 hover:text-rose-600 font-medium">Reset Filter</a>
            @endif
        </div>
    </div>

    <!-- Table List -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Judul & Penulis</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4">Dilihat</th>
                    <th class="px-6 py-4">Waktu Tersimpan</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($knowledges as $item)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                    <td class="px-6 py-4">
                        <div class="max-w-md">
                            <a href="{{ route('knowledge.show', $item->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition line-clamp-1 block">
                                {{ $item->judul }}
                            </a>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Oleh: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $item->penulis ?? ($item->user->name ?? 'Anonim') }}</span>
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                            {{ $item->category->nama_kategori ?? 'Umum' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold
                            @if($item->tipe === 'Video') bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300
                            @elseif($item->tipe === 'Gambar') bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300
                            @elseif($item->tipe === 'Audio') bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300
                            @else bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 @endif">
                            {{ $item->tipe }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        {{ $item->views_count ?? 0 }} kali
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ \Carbon\Carbon::parse($item->pivot->created_at ?? $item->created_at)->translatedFormat('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('knowledge.show', $item->id) }}" class="p-1.5 rounded-lg text-primary-600 hover:bg-primary-50 dark:hover:bg-slate-700 transition" title="Buka Dokumen">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                            <button 
                                type="button" 
                                onclick="removeBookmark({{ $item->id }}, this)" 
                                class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition" 
                                title="Hapus dari Bookmark"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-slate-500 dark:text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-slate-700 text-amber-500 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </div>
                        <p class="font-bold text-base text-slate-800 dark:text-slate-200">Belum ada artikel tersimpan</p>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Klik ikon bookmark/simpan pada artikel yang Anda temukan di beranda atau halaman detail untuk menyimpannya ke menu ini.</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-lg bg-primary-600 text-white text-xs font-semibold hover:bg-primary-700 transition">
                            Jelajahi Pengetahuan
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($knowledges->hasPages())
    <div class="p-6 border-t border-slate-200 dark:border-slate-700">
        {{ $knowledges->links() }}
    </div>
    @endif
</div>

<script>
function removeBookmark(id, buttonEl) {
    if (!confirm('Hapus artikel ini dari daftar tersimpan Anda?')) return;

    fetch(`/knowledge/${id}/bookmark`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const row = buttonEl.closest('tr');
            if (row) {
                row.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => row.remove(), 300);
            }
        }
    })
    .catch(err => console.error(err));
}
</script>
@endsection

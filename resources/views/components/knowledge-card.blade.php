@props(['item'])
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden card-hover flex flex-col h-full">
    <div class="p-5 flex-grow flex flex-col justify-between">
        <div>
            <!-- Header Tipe & Waktu -->
            <div class="flex justify-between items-center mb-3">
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                    @if($item->tipe == 'Teks') bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300
                    @elseif($item->tipe == 'Video') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                    @elseif($item->tipe == 'Gambar') bg-secondary-100 text-secondary-700 dark:bg-secondary-900 dark:text-secondary-300
                    @else bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300
                    @endif">
                    {{ $item->tipe }}
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->diffForHumans() : '' }}
                </span>
            </div>

            <!-- Konten Utama: Judul & Deskripsi di kiri, Small Thumbnail Box di kanan mentok -->
            <div class="flex items-start justify-between gap-4 mt-2 mb-2">
                <div class="flex-grow min-w-0">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2 line-clamp-2 leading-snug">
                        <a href="{{ url('/knowledge/'.$item->id . (request()->is('kategori*') ? '?from=kategori' : '')) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">{{ $item->judul }}</a>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-2">
                        {{ Str::limit(strip_tags($item->deskripsi), 65) }}
                    </p>
                </div>

                <!-- Box Thumbnail Kecil (Sejajar dengan Judul, Kanan Mentok) -->
                <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 shadow-sm flex items-center justify-center">
                    @if($item->file_path)
                        <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-750">
                            @if($item->tipe == 'Video')
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @elseif($item->tipe == 'Gambar')
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            @elseif($item->tipe == 'Audio')
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                            @else
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Kategori & Views -->
    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-750/50 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center text-xs text-slate-500 dark:text-slate-400">
        <div class="flex items-center gap-1 truncate max-w-[60%]">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            <span class="truncate">{{ $item->category->nama_kategori ?? 'Umum' }}</span>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                {{ $item->views_count }}
            </span>
        </div>
    </div>
</div>

@props(['item'])
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden card-hover flex flex-col h-full">
    <div class="p-5 flex-grow">
        <div class="flex justify-between items-start mb-3">
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
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2 line-clamp-2">
            <a href="{{ url('/knowledge/'.$item->id) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">{{ $item->judul }}</a>
        </h3>
        <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-3 mb-4">
            {{ Str::limit(strip_tags($item->deskripsi), 60) }}
        </p>
    </div>
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

@extends('layouts.admin')
@section('title', $knowledge->judul)

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
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Detail</li>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Top Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 break-words [overflow-wrap:anywhere]">{{ $knowledge->judul }}</h1>
            <div class="flex items-center gap-3 mt-2 text-sm text-slate-500 dark:text-slate-400">
                <span>{{ $knowledge->user->name ?? 'Anonim' }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-400 dark:bg-slate-600"></span>
                <span>{{ $knowledge->created_at?->format('d M Y') ?? '-' }}</span>
                <span class="w-1 h-1 rounded-full bg-slate-400 dark:bg-slate-600"></span>
                <span>{{ $knowledge->views_count ?? 0 }} dilihat</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('knowledge.index') }}" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
            <a href="{{ route('knowledge.edit', $knowledge->id) }}" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-slate-800 shadow-md rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">

        {{-- Meta Info Bar --}}
        <div class="px-8 py-5 bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center gap-4">
            {{-- Status Badge --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                {{ $knowledge->status == 'Disetujui' ? 'bg-green-100 dark:bg-green-950 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800' : 
                  ($knowledge->status == 'Ditolak' ? 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800' : 
                  ($knowledge->status == 'Diajukan' ? 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800')) }}">
                {{ $knowledge->status }}
            </span>

            {{-- Tipe Badge --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                @switch($knowledge->tipe)
                    @case('Gambar')
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        @break
                    @case('Video')
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        @break
                    @case('Audio')
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" /></svg>
                        @break
                    @default
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                @endswitch
                {{ $knowledge->tipe }}
            </span>

            {{-- Kategori (Tanpa icon, sejajar dengan badge lainnya) --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 whitespace-nowrap">
                {{ $knowledge->category->nama_kategori ?? '-' }}
            </span>

            {{-- Tags --}}
            @if($knowledge->tags && $knowledge->tags->count())
                @foreach($knowledge->tags as $tag)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">#{{ $tag->nama_label }}</span>
                @endforeach
            @endif
        </div>

        {{-- Content Body --}}
        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- Left: Main Content --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Ringkasan --}}
                    @if($knowledge->deskripsi)
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-2 uppercase tracking-wider">Ringkasan</h2>
                        <div class="text-slate-700 dark:text-slate-300 leading-relaxed text-sm bg-slate-50 dark:bg-slate-900 rounded-lg p-4 border border-slate-200 dark:border-slate-700 break-words [overflow-wrap:anywhere] prose dark:prose-invert max-w-none rich-editor-content">
                            {!! $knowledge->deskripsi !!}
                        </div>
                    </div>
                    @endif

                    {{-- Detail --}}
                    @if($knowledge->detail)
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-2 uppercase tracking-wider">Detail</h2>
                        <div class="prose prose-sm max-w-none text-slate-700 dark:text-slate-300 leading-relaxed break-words [overflow-wrap:anywhere] prose dark:prose-invert max-w-none rich-editor-content">
                            {!! $knowledge->detail !!}
                        </div>
                    </div>
                    @endif

                    {{-- URL --}}
                    @if($knowledge->url_teks)
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-2 uppercase tracking-wider">URL {{ $knowledge->tipe }}</h2>
                        <a href="{{ $knowledge->url_teks }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:underline break-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            {{ $knowledge->url_teks }}
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Right: Sidebar Info --}}
                <div class="lg:col-span-4 space-y-5">
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-700 space-y-4">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Informasi</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Penulis</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $knowledge->penulis ?? $knowledge->user->name ?? '-' }}</span>
                            </div>
                            @if($knowledge->kolaborator)
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Kolaborator</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $knowledge->kolaborator }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Tanggal Dibuat</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $knowledge->created_at?->format('d M Y') ?? '-' }}</span>
                            </div>
                            @if($knowledge->tanggal_terbit)
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Tanggal Terbit</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $knowledge->tanggal_terbit }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Status Akses</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium capitalize">{{ $knowledge->status_akses ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Dilihat</span>
                                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ $knowledge->views_count ?? 0 }}×</span>
                            </div>
                        </div>
                    </div>

                    {{-- Forum Diskusi Terkait --}}
                    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-700 space-y-4 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            Diskusi Forum
                        </h3>
                        
                        @if($knowledge->threads && $knowledge->threads->count() > 0)
                            <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                                @foreach($knowledge->threads as $thread)
                                <a href="{{ route('forum.show', $thread->id) }}" class="block p-3 rounded-lg border border-slate-100 dark:border-slate-800 hover:border-red-200 dark:hover:border-red-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition text-sm">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200 line-clamp-1 hover:text-red-600 dark:hover:text-red-400 transition">{{ $thread->judul }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex justify-between">
                                        <span>Oleh: {{ $thread->user->name ?? 'Anonim' }}</span>
                                        <span>{{ $thread->created_at?->diffForHumans() }}</span>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 dark:text-slate-500">Belum ada diskusi forum terkait pengetahuan ini.</p>
                        @endif
                        
                        <a href="{{ route('forum.create', ['knowledge_id' => $knowledge->id]) }}" class="w-full text-center block px-4 py-2.5 bg-red-50 dark:bg-slate-800 border border-red-200 dark:border-slate-700 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-slate-700 rounded-lg text-sm font-semibold transition">
                            Mulai Diskusi Baru
                        </a>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="bg-red-50 dark:bg-slate-900 rounded-xl p-5 border border-red-200 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-red-800 dark:text-red-400 mb-3">Konfirmasi hapus pengetahuan?</h3>
                        <form action="{{ route('knowledge.destroy', $knowledge->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengetahuan ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-lg text-sm transition shadow-md flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Hapus Pengetahuan
                            </button>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection

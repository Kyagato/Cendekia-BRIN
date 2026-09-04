@extends('layouts.public')
@section('title', $knowledge->judul)

@section('content')
<div class="bg-white dark:bg-slate-900 min-h-screen pb-16 transition-colors duration-200" style="padding-top: 85px;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumbs Dynamic (Di atas Judul) --}}
        @php
            $referer = request()->headers->get('referer') ?? url()->previous();
            $fromCategory = request()->query('from') === 'kategori' || ($referer && str_contains(strtolower($referer), 'kategori'));
        @endphp
        <nav class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-4 flex-wrap" aria-label="Breadcrumb">
            @if($fromCategory)
                <a href="{{ url('/kategori') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition font-medium">Kategori</a>
            @else
                <a href="{{ url('/') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition font-medium">Beranda</a>
            @endif
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" width="14" height="14" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            <span class="text-slate-800 dark:text-slate-200 font-semibold line-clamp-1">{{ Str::limit($knowledge->judul, 80) }}</span>
        </nav>

        <hr class="border-slate-200 dark:border-slate-700 mb-6">

        {{-- Judul Besar --}}
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-4 break-words [overflow-wrap:anywhere]">
            {{ $knowledge->judul }}
        </h1>

        {{-- Meta: Dibuat oleh, Tags, Diperbarui --}}
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500 dark:text-slate-400 mb-10">
            <div class="flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4 text-slate-400 shrink-0" width="16" height="16" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                <span>Dibuat oleh: {{ $knowledge->penulis ?? ($knowledge->user->name ?? 'Anonim') }}{{ $knowledge->kolaborator ? ', ' . $knowledge->kolaborator : '' }}</span>
            </div>

            @if($knowledge->tags && $knowledge->tags->count() > 0)
            <div class="flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4 text-slate-400 shrink-0" width="16" height="16" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                <span>
                    @foreach($knowledge->tags as $tag)
                        {{ $tag->nama_label }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </span>
            </div>
            @endif

            <div class="flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4 text-slate-400 shrink-0" width="16" height="16" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>Diperbarui: {{ $knowledge->updated_at ? $knowledge->updated_at->format('d-m-Y') : ($knowledge->created_at ? $knowledge->created_at->format('d-m-Y') : '-') }}</span>
            </div>
        </div>

        {{-- Container: Konten Utama (Kiri 74%) + Sidebar Sticky (Kanan 24%) --}}
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start; width: 100%; justify-content: space-between;">

            {{-- Kolom Kiri: Konten --}}
            <div style="flex: 1 1 70%; min-width: 300px; max-width: 74%; shrink: 1;" class="space-y-10">

                <style>
                    .rich-editor-content ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
                    .rich-editor-content ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
                    .rich-editor-content ol[style*="lower-alpha"] { list-style-type: lower-alpha !important; }
                    .rich-editor-content li { display: list-item !important; }
                </style>

                {{-- Ringkasan --}}
                @if($knowledge->deskripsi)
                <section id="ringkasan" class="scroll-mt-28">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Ringkasan</h2>
                    <div class="text-slate-700 dark:text-slate-300 leading-relaxed text-base break-words [overflow-wrap:anywhere] prose dark:prose-invert max-w-none rich-editor-content">
                        {!! $knowledge->deskripsi !!}
                    </div>
                </section>
                @endif

                {{-- Detil --}}
                @if($knowledge->detail)
                <section id="detail" class="scroll-mt-28">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Detil</h2>
                    <div class="text-slate-700 dark:text-slate-300 leading-relaxed text-base break-words [overflow-wrap:anywhere] prose dark:prose-invert max-w-none rich-editor-content">
                        {!! $knowledge->detail !!}
                    </div>
                </section>
                @endif

                {{-- Media Preview --}}
                @if($knowledge->file_path)
                <section>
                    @if($knowledge->tipe == 'Gambar')
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                            <img src="{{ asset('storage/' . $knowledge->file_path) }}" alt="{{ $knowledge->judul }}" class="w-full max-h-[550px] object-contain mx-auto">
                        </div>
                    @elseif($knowledge->tipe == 'Video')
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black">
                            <video controls class="w-full max-h-[500px]">
                                <source src="{{ asset('storage/' . $knowledge->file_path) }}">
                            </video>
                        </div>
                    @elseif($knowledge->tipe == 'Audio')
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                            <audio controls class="w-full">
                                <source src="{{ asset('storage/' . $knowledge->file_path) }}">
                            </audio>
                        </div>
                    @endif
                </section>
                @endif

                {{-- URL Teks --}}
                @if($knowledge->url_teks)
                <section>
                    <a href="{{ $knowledge->url_teks }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:underline font-medium break-all">
                        <svg class="w-4 h-4 shrink-0" width="16" height="16" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        {{ $knowledge->url_teks }}
                    </a>
                </section>
                @endif

                {{-- Meta Data --}}
                <section id="metadata" class="scroll-mt-28">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Meta Data</h2>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <dl class="divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Nomor ID</dt>
                                <dd class="text-slate-600 dark:text-slate-300 break-all">{{ $knowledge->id }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Judul</dt>
                                <dd class="text-slate-600 dark:text-slate-300">{{ $knowledge->judul }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Penulis</dt>
                                <dd class="text-slate-600 dark:text-slate-300">{{ $knowledge->penulis ?? ($knowledge->user->name ?? '-') }}</dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Kategori</dt>
                                <dd class="text-slate-600 dark:text-slate-300">{{ $knowledge->category->nama_kategori ?? '-' }}</dd>
                            </div>
                            @if($knowledge->deskripsi)
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Deskripsi</dt>
                                <dd class="text-slate-600 dark:text-slate-300 break-words">{!! $knowledge->deskripsi !!}</dd>
                            </div>
                            @endif
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Waktu</dt>
                                <dd class="text-slate-600 dark:text-slate-300">
                                    {{ $knowledge->tanggal_terbit ? \Carbon\Carbon::parse($knowledge->tanggal_terbit)->translatedFormat('l, j F Y') : $knowledge->created_at->translatedFormat('l, j F Y') }}
                                </dd>
                            </div>
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Format</dt>
                                <dd class="text-slate-600 dark:text-slate-300">{{ $knowledge->tipe }}</dd>
                            </div>
                            @if($knowledge->tags && $knowledge->tags->count() > 0)
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Label</dt>
                                <dd class="flex flex-wrap gap-2">
                                    @foreach($knowledge->tags as $tag)
                                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs rounded-md">{{ $tag->nama_label }}</span>
                                    @endforeach
                                </dd>
                            </div>
                            @endif
                            @if($knowledge->kolaborator)
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Kontributor</dt>
                                <dd class="text-slate-600 dark:text-slate-300">{{ $knowledge->kolaborator }}</dd>
                            </div>
                            @endif
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Status Publikasi</dt>
                                <dd class="text-slate-600 dark:text-slate-300">{{ $knowledge->status }}</dd>
                            </div>
                            @if($knowledge->url_teks)
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">URL</dt>
                                <dd>
                                    <a href="{{ $knowledge->url_teks }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $knowledge->url_teks }}</a>
                                </dd>
                            </div>
                            @endif
                            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                                <dt class="font-semibold text-slate-900 dark:text-slate-100">Dilihat</dt>
                                <dd class="text-slate-600 dark:text-slate-300">{{ $knowledge->views_count ?? 0 }} kali</dd>
                            </div>
                        </dl>
                    </div>
                </section>

            </div>

            {{-- Kolom Kanan: Sidebar Sticky (Seperempat 24% & Melayang Tetap di Kanan) --}}
            <div style="flex: 0 0 24%; min-width: 200px; max-width: 24%; position: sticky; top: 100px;">
                <div class="space-y-6">

                    {{-- Estimasi waktu baca --}}
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <svg class="w-4 h-4 shrink-0" width="16" height="16" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ $readingTime }} menit dibaca</span>
                    </div>

                    {{-- Di halaman ini --}}
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Di halaman ini</h3>
                        <nav class="space-y-2 text-sm">
                            @if($knowledge->deskripsi)
                            <a href="#ringkasan" class="block text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition font-medium">Ringkasan</a>
                            @endif
                            @if($knowledge->detail)
                            <a href="#detail" class="block text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">Detail</a>
                            @endif
                            <a href="#metadata" class="block text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">Meta Data</a>
                        </nav>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>
@endsection

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
                <span>Dibuat oleh:
                    @if($knowledge->user)
                        <a href="{{ route('users.show', $knowledge->user->id) }}"
                           class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition">
                            {{ $knowledge->penulis ?? $knowledge->user->name }}
                        </a>
                    @else
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $knowledge->penulis ?? 'Anonim' }}</span>
                    @endif
                    {{ $knowledge->kolaborator ? ', ' . $knowledge->kolaborator : '' }}
                </span>
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

            {{-- Tombol Aksi: Bookmark --}}
            <div class="ml-auto shrink-0 flex items-center gap-2">
                {{-- Tombol Bookmark / Simpan --}}
                <button
                    id="btn-bookmark"
                    type="button"
                    onclick="toggleBookmark({{ $knowledge->id }})"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 border {{ $isBookmarked ? 'bg-amber-50 text-amber-700 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-700/60 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:border-slate-300 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700' }}"
                >
                    <svg 
                        id="bookmark-icon"
                        class="w-4 h-4 transition-transform duration-200 {{ $isBookmarked ? 'text-amber-500 fill-amber-500 scale-110' : 'text-slate-400 fill-none' }}" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor" 
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <span id="bookmark-text">{{ $isBookmarked ? 'Tersimpan' : 'Simpan Artikel' }}</span>
                    <span id="bookmark-count" class="ml-1 px-1.5 py-0.2 rounded-full text-[11px] {{ $isBookmarked ? 'bg-amber-200/80 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                        {{ $bookmarksCount }}
                    </span>
                </button>
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

                {{-- Media Preview dari URL (Video/Gambar/Audio) - Di atas Ringkasan --}}
                @if($knowledge->url_teks)
                <section class="mb-2">
                    @php
                        $mediaUrl = $knowledge->url_teks;
                        $youtubeId = null;
                        // Deteksi YouTube URL dan extract video ID
                        if (preg_match('/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $mediaUrl, $ytMatch)) {
                            $youtubeId = $ytMatch[1];
                        }
                    @endphp

                    @if($youtubeId)
                        {{-- YouTube Embed --}}
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black">
                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        </div>
                    @elseif($knowledge->tipe == 'Video')
                        {{-- Video biasa (non-YouTube) --}}
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black">
                            <video controls class="w-full max-h-[500px]">
                                <source src="{{ $mediaUrl }}">
                                Browser Anda tidak mendukung pemutar video.
                            </video>
                        </div>
                    @elseif($knowledge->tipe == 'Gambar')
                        {{-- Gambar dari URL --}}
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                            <img src="{{ $mediaUrl }}" alt="{{ $knowledge->judul }}" class="w-full max-h-[550px] object-contain mx-auto">
                        </div>
                    @elseif($knowledge->tipe == 'Audio')
                        {{-- Audio dari URL atau uploaded file path --}}
                        @php
                            $audioSrc = str_starts_with($mediaUrl, 'http://') || str_starts_with($mediaUrl, 'https://') ? $mediaUrl : asset('storage/' . $mediaUrl);
                        @endphp
                        <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                            <audio controls class="w-full">
                                <source src="{{ $audioSrc }}">
                                Browser Anda tidak mendukung pemutar audio.
                            </audio>
                        </div>
                    @endif
                </section>
                @endif

                {{-- Media Preview dari file_path (uploaded file) --}}
                {{-- Catatan: Thumbnail gambar untuk tipe Video, Audio, atau Teks hanya muncul di kartu Beranda & Kategori, tidak ditampilkan di dalam detail pengetahuan --}}
                @if($knowledge->file_path && $knowledge->file_path !== $knowledge->url_teks)
                @php
                    $filePathExt = strtolower(pathinfo($knowledge->file_path, PATHINFO_EXTENSION));
                    $isImageFile = in_array($filePathExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                @endphp
                @if($knowledge->tipe == 'Gambar' || ($knowledge->tipe == 'Video' && !$isImageFile) || ($knowledge->tipe == 'Audio' && !$isImageFile))
                <section class="mb-2">
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
                @endif

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
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Detail</h2>
                    <div class="text-slate-700 dark:text-slate-300 leading-relaxed text-base break-words [overflow-wrap:anywhere] prose dark:prose-invert max-w-none rich-editor-content">
                        {!! $knowledge->detail !!}
                    </div>
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
                                <dd class="text-slate-600 dark:text-slate-300">
                                    @if($knowledge->user)
                                        <a href="{{ route('users.show', $knowledge->user->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                                            {{ $knowledge->penulis ?? $knowledge->user->name }}
                                        </a>
                                    @else
                                        {{ $knowledge->penulis ?? '-' }}
                                    @endif
                                </dd>
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

                {{-- Rating / Like & Interaksi Bar --}}
                <div class="mt-8 p-6 bg-gradient-to-r from-slate-50 via-blue-50/20 to-slate-50 dark:from-slate-800/90 dark:via-slate-800 dark:to-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Beri Rating & Tanggapan</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 font-semibold">Feedback</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Apakah materi pengetahuan ini bermanfaat untuk Anda? Berikan apresiasi atau tinggalkan diskusi.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0 flex-wrap" x-data="{ shareOpen: false }">
                        {{-- Tombol Like --}}
                        <button
                            id="btn-like"
                            type="button"
                            onclick="toggleLike({{ $knowledge->id }})"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border cursor-pointer {{ $isLiked ? 'bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-700/60 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700' }}"
                        >
                            <svg 
                                id="like-icon"
                                class="w-5 h-5 transition-transform duration-200 {{ $isLiked ? 'text-rose-500 fill-rose-500 scale-110' : 'text-slate-400 fill-none' }}" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor" 
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span id="like-text">{{ $isLiked ? 'Disukai' : 'Suka' }}</span>
                            <span id="like-count" class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold {{ $isLiked ? 'bg-rose-200/80 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                {{ $likesCount }}
                            </span>
                        </button>

                        {{-- Tombol Lompat ke Komentar --}}
                        <a href="#komentar" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Komentar</span>
                            <span id="top-comments-count" class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                {{ $commentsCount }}
                            </span>
                        </a>

                        {{-- Dropdown Tombol Bagikan & Salin Link --}}
                        <div class="relative">
                            <button
                                type="button"
                                @click="shareOpen = !shareOpen"
                                class="animated-border-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700 shadow-xs cursor-pointer select-none"
                                :class="{ 'ring-2 ring-blue-500/20 border-blue-400 dark:border-blue-500': shareOpen }"
                                title="Bagikan atau salin link artikel ini"
                            >
                                <svg class="animated-border-svg" aria-hidden="true">
                                    <rect x="1" y="1" width="calc(100% - 2px)" height="calc(100% - 2px)" rx="12" ry="12" pathLength="100" class="animated-border-rect" />
                                </svg>
                                <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span>Bagikan</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': shareOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Popover Menu -->
                            <div
                                x-show="shareOpen"
                                @click.outside="shareOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-2 z-50"
                                style="display: none;"
                            >
                                {{-- Salin Tautan Cepat --}}
                                <button
                                    type="button"
                                    id="btn-copy-link"
                                    onclick="copyKnowledgeLink()"
                                    class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition text-left cursor-pointer group"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                            <svg id="copy-link-icon" class="w-4 h-4 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </span>
                                        <div>
                                            <div id="copy-link-text" class="text-xs font-bold leading-tight">Salin Link</div>
                                            <div class="text-[11px] text-slate-400 dark:text-slate-500 font-normal">Salin tautan ke clipboard</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700/60">URL</span>
                                </button>

                                <div class="h-px bg-slate-100 dark:bg-slate-700/60 my-1.5"></div>

                                <div class="px-2.5 py-1 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                    Bagikan Ke Media
                                </div>

                                <!-- WhatsApp -->
                                <a
                                    href="https://api.whatsapp.com/send?text={{ rawurlencode($knowledge->judul . ' - ' . url()->current()) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="shareOpen = false"
                                    class="flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-lg transition"
                                >
                                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/60 dark:text-emerald-300 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                    </span>
                                    <span>WhatsApp</span>
                                </a>

                                <!-- Telegram -->
                                <a
                                    href="https://t.me/share/url?url={{ rawurlencode(url()->current()) }}&text={{ rawurlencode($knowledge->judul) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="shareOpen = false"
                                    class="flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-sky-50 dark:hover:bg-sky-950/40 hover:text-sky-700 dark:hover:text-sky-300 rounded-lg transition"
                                >
                                    <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 dark:bg-sky-900/60 dark:text-sky-300 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.536-.196 1.006.128.832.922z"/>
                                        </svg>
                                    </span>
                                    <span>Telegram</span>
                                </a>

                                <!-- Facebook -->
                                <a
                                    href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="shareOpen = false"
                                    class="flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-950/40 hover:text-blue-700 dark:hover:text-blue-300 rounded-lg transition"
                                >
                                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/60 dark:text-blue-300 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </span>
                                    <span>Facebook</span>
                                </a>

                                <!-- X (Twitter) -->
                                <a
                                    href="https://twitter.com/intent/tweet?text={{ rawurlencode($knowledge->judul) }}&url={{ rawurlencode(url()->current()) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="shareOpen = false"
                                    class="flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white rounded-lg transition"
                                >
                                    <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    </span>
                                    <span>X (Twitter)</span>
                                </a>

                                <!-- Native Web Share (Aplikasi Lainnya) -->
                                <button
                                    type="button"
                                    onclick="triggerNativeShare('{{ addslashes($knowledge->judul) }}', '{{ url()->current() }}')"
                                    @click="shareOpen = false"
                                    class="w-full flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition text-left cursor-pointer border-t border-slate-100 dark:border-slate-700/60 mt-1 pt-2"
                                >
                                    <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-950/50 dark:text-purple-300 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </span>
                                    <span>Aplikasi Lainnya...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Komentar --}}
                <section id="komentar" class="scroll-mt-28 space-y-6 pt-4" x-data="commentSystem()">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                            <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Diskusi & Komentar
                            <span id="section-comments-count" class="text-xs px-2.5 py-1 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold" x-text="commentsCount">
                                {{ $commentsCount }}
                            </span>
                        </h2>
                    </div>

                    {{-- Flash Session Success (Otomatis hilang setelah 5 detik) --}}
                    @if(session('success'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 transition p-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @endif

                    {{-- Toast Notifikasi AJAX (Muncul 5 detik lalu otomatis menghilang) --}}
                    <div x-show="toast.show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between shadow-sm"
                         style="display: none;">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium" x-text="toast.message"></span>
                        </div>
                        <button type="button" @click="toast.show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 transition p-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Daftar Komentar --}}
                    <div id="comments-list" class="space-y-4">
                        @foreach($comments as $comment)
                            @include('partials.knowledge-comment-item', ['comment' => $comment])
                        @endforeach
                    </div>

                    <div id="empty-comments" class="p-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 {{ $comments->count() > 0 ? 'hidden' : '' }}">
                        <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Belum ada komentar</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Jadilah yang pertama memberikan tanggapan atau pertanyaan pada pengetahuan ini!</p>
                    </div>

                    {{-- Kotak Tulis Komentar Sticky / Floating (Mengikuti saat digeser ke atas & bawah) --}}
                    @auth
                    <div class="sticky bottom-4 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-300 dark:border-slate-700 p-4 transition-all duration-300">
                        <div class="flex items-center justify-between" :class="{ 'mb-3': !isCollapsed }">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary-600 animate-pulse"></span>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                    <span x-text="replyToId ? ('Membalas Komentar: ' + replyToName) : 'Tuliskan Komentar Anda'"></span>
                                    <button 
                                        x-show="replyToId !== null" 
                                        @click="cancelReply()" 
                                        type="button"
                                        class="ml-2 text-xs text-rose-500 hover:text-rose-600 font-semibold transition cursor-pointer"
                                    >
                                        ✕ Batal
                                    </button>
                                </h3>
                            </div>

                            <!-- Minimize / Expand Toggle Button -->
                            <button 
                                type="button" 
                                @click="isCollapsed = !isCollapsed" 
                                class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white flex items-center gap-1 px-2.5 py-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 transition select-none cursor-pointer font-medium"
                            >
                                <span x-text="isCollapsed ? 'Buka Form' : 'Sembunyikan'"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="isCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <form x-show="!isCollapsed" 
                              @submit.prevent="submitComment($event)" 
                              action="{{ route('knowledge.comment', $knowledge->id) }}" 
                              method="POST">
                            @csrf
                            <input type="hidden" name="parent_id" :value="replyToId">
                            <textarea 
                                id="comment-textarea"
                                name="konten" 
                                x-model="commentText"
                                rows="3" 
                                required 
                                placeholder="Tuliskan komentar atau pertanyaan Anda di sini..." 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition resize-none shadow-xs"
                            ></textarea>
                            <div class="flex items-center justify-between mt-2.5">
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 hidden sm:inline">
                                    Komentar Anda akan dapat dibaca oleh pembaca lainnya.
                                </span>
                                <button 
                                    type="submit" 
                                    :disabled="isSubmitting"
                                    class="ml-auto px-5 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm cursor-pointer"
                                >
                                    <template x-if="!isSubmitting">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                            </svg>
                                            <span>Kirim Komentar</span>
                                        </div>
                                    </template>
                                    <template x-if="isSubmitting">
                                        <div class="flex items-center gap-2">
                                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            <span>Mengirim...</span>
                                        </div>
                                    </template>
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="text-center py-6 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Silakan masuk ke akun Anda untuk memberikan rating dan komentar.</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            Masuk / Login
                        </a>
                    </div>
                    @endauth
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

                    {{-- Box Penulis --}}
                    @if($knowledge->user)
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm space-y-3">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Penulis</span>
                        <div class="flex items-center gap-3">
                            @if($knowledge->user->foto_profil)
                                <img src="{{ asset('storage/' . $knowledge->user->foto_profil) }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-base flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($knowledge->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('users.show', $knowledge->user->id) }}" class="font-bold text-sm text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition truncate block">
                                    {{ $knowledge->user->name }}
                                </a>
                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ $knowledge->user->role ?? 'Anggota' }}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('users.show', $knowledge->user->id) }}" class="block text-center py-2 px-3 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-xl transition">
                            Lihat Profil Penulis &rarr;
                        </a>
                    </div>
                    @endif

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
                            <a href="#komentar" class="block text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">Komentar & Rating</a>
                        </nav>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<style>
    .animated-border-btn {
        position: relative;
        overflow: visible;
    }

    .animated-border-svg {
        position: absolute;
        inset: -1px;
        width: calc(100% + 2px);
        height: calc(100% + 2px);
        pointer-events: none;
        overflow: visible;
        border-radius: 0.75rem;
    }

    .animated-border-rect {
        x: 1px;
        y: 1px;
        width: calc(100% - 2px);
        height: calc(100% - 2px);
        rx: 12px;
        ry: 12px;
        stroke: #2563eb;
        stroke-width: 2px;
        stroke-linecap: round;
        fill: none;
        stroke-dasharray: 0 100;
        stroke-dashoffset: 15;
        opacity: 0;
        filter: drop-shadow(0 0 3px rgba(37, 99, 235, 0.5));
        transition: opacity 0.35s ease;
    }

    .dark .animated-border-rect {
        stroke: #60a5fa;
        filter: drop-shadow(0 0 4px rgba(96, 165, 250, 0.7));
    }

    .animated-border-btn:hover .animated-border-rect {
        opacity: 1;
        animation: border-draw-loop 1.8s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
    }

    @keyframes border-draw-loop {
        0% {
            stroke-dasharray: 0 100;
            stroke-dashoffset: 15;
        }
        25% {
            stroke-dasharray: 35 65;
        }
        100% {
            stroke-dasharray: 35 65;
            stroke-dashoffset: -85;
        }
    }
</style>

<script>
function toggleBookmark(knowledgeId) {
    const btn = document.getElementById('btn-bookmark');
    const icon = document.getElementById('bookmark-icon');
    const text = document.getElementById('bookmark-text');
    const count = document.getElementById('bookmark-count');
    
    // Disable temporarily
    btn.disabled = true;

    fetch(`/knowledge/${knowledgeId}/bookmark`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        return res.json();
    })
    .then(data => {
        btn.disabled = false;
        if (!data || data.status !== 'success') return;

        if (data.bookmarked) {
            // State: Tersimpan
            btn.className = "inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 border bg-amber-50 text-amber-700 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-700/60 shadow-sm";
            icon.className = "w-4 h-4 transition-transform duration-200 text-amber-500 fill-amber-500 scale-110";
            text.textContent = 'Tersimpan';
            count.className = "ml-1 px-1.5 py-0.2 rounded-full text-[11px] bg-amber-200/80 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200";
        } else {
            // State: Belum disimpan
            btn.className = "inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 border bg-slate-50 text-slate-700 border-slate-200 hover:border-slate-300 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700";
            icon.className = "w-4 h-4 transition-transform duration-200 text-slate-400 fill-none";
            text.textContent = 'Simpan Artikel';
            count.className = "ml-1 px-1.5 py-0.2 rounded-full text-[11px] bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300";
        }

        count.textContent = data.total_bookmarks;
    })
    .catch(err => {
        btn.disabled = false;
        console.error('Error toggling bookmark:', err);
    });
}

function toggleLike(knowledgeId) {
    const btn = document.getElementById('btn-like');
    const icon = document.getElementById('like-icon');
    const text = document.getElementById('like-text');
    const count = document.getElementById('like-count');
    
    btn.disabled = true;

    fetch(`/knowledge/${knowledgeId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        return res.json();
    })
    .then(data => {
        btn.disabled = false;
        if (!data || data.status !== 'success') return;

        if (data.liked) {
            btn.className = "inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border cursor-pointer bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-700/60 shadow-sm";
            icon.className = "w-5 h-5 transition-transform duration-200 text-rose-500 fill-rose-500 scale-125";
            text.textContent = 'Disukai';
            count.className = "ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-rose-200/80 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200";
            setTimeout(() => { icon.className = "w-5 h-5 transition-transform duration-200 text-rose-500 fill-rose-500 scale-110"; }, 250);
        } else {
            btn.className = "inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border cursor-pointer bg-white text-slate-700 border-slate-200 hover:border-slate-300 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700";
            icon.className = "w-5 h-5 transition-transform duration-200 text-slate-400 fill-none";
            text.textContent = 'Suka';
            count.className = "ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300";
        }

        count.textContent = data.total_likes;
    })
    .catch(err => {
        btn.disabled = false;
        console.error('Error toggling like:', err);
    });
}

function commentSystem() {
    return {
        isCollapsed: false,
        replyToId: null,
        replyToName: '',
        isSubmitting: false,
        commentText: '',
        commentsCount: {{ $commentsCount }},
        toast: {
            show: false,
            message: '',
            timer: null
        },
        showToast(msg) {
            this.toast.message = msg;
            this.toast.show = true;
            if (this.toast.timer) clearTimeout(this.toast.timer);
            this.toast.timer = setTimeout(() => {
                this.toast.show = false;
            }, 5000);
        },
        setReply(id, name) {
            this.replyToId = id;
            this.replyToName = name;
            this.isCollapsed = false;
            this.$nextTick(() => {
                const el = document.getElementById('comment-textarea');
                if (el) el.focus();
            });
        },
        cancelReply() {
            this.replyToId = null;
            this.replyToName = '';
        },
        async submitComment(e) {
            if (!this.commentText || !this.commentText.trim()) return;
            this.isSubmitting = true;

            const form = e.target;
            const formData = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok && data.status === 'success') {
                    // Sembunyikan empty state jika sebelumnya belum ada komentar
                    const emptyState = document.getElementById('empty-comments');
                    if (emptyState) emptyState.classList.add('hidden');

                    if (this.replyToId) {
                        const repliesContainer = document.getElementById('replies-container-' + this.replyToId);
                        if (repliesContainer) {
                            repliesContainer.classList.remove('hidden');
                            repliesContainer.insertAdjacentHTML('beforeend', data.html);
                        }
                    } else {
                        const list = document.getElementById('comments-list');
                        if (list) {
                            list.insertAdjacentHTML('beforeend', data.html);
                        }
                    }

                    // Reset form & state
                    this.commentText = '';
                    this.cancelReply();
                    this.commentsCount++;

                    // Update count di tombol atas
                    const badge = document.getElementById('top-comments-count');
                    if (badge) badge.innerText = this.commentsCount;

                    // Tampilkan notifikasi hijau selama 5 detik lalu menghilang
                    this.showToast(data.message || 'Komentar Anda berhasil dikirim.');

                    // Scroll smooth ke komentar baru agar terlihat tanpa terpental ke atas
                    this.$nextTick(() => {
                        const newEl = document.getElementById('comment-' + data.comment.id);
                        if (newEl) {
                            newEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    });
                } else {
                    alert(data.message || 'Terjadi kesalahan saat mengirim komentar.');
                }
            } catch (err) {
                console.error(err);
                form.submit();
            } finally {
                this.isSubmitting = false;
            }
        },
        async deleteComment(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) return;

            try {
                const res = await fetch(`/knowledge/comment/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    const el = document.getElementById('comment-' + id);
                    if (el) {
                        el.style.transition = 'all 0.3s ease';
                        el.style.opacity = '0';
                        el.style.transform = 'scale(0.95)';
                        setTimeout(() => el.remove(), 300);
                    }
                    this.commentsCount = Math.max(0, this.commentsCount - 1);
                    const badge = document.getElementById('top-comments-count');
                    if (badge) badge.innerText = this.commentsCount;

                    if (this.commentsCount === 0) {
                        const emptyState = document.getElementById('empty-comments');
                        if (emptyState) emptyState.classList.remove('hidden');
                    }

                    this.showToast(data.message || 'Komentar berhasil dihapus.');
                } else {
                    alert(data.message || 'Gagal menghapus komentar.');
                }
            } catch (err) {
                console.error(err);
            }
        }
    };
}

function copyKnowledgeLink() {
    const url = window.location.href;
    const btn = document.getElementById('btn-copy-link');
    const icon = document.getElementById('copy-link-icon');
    const text = document.getElementById('copy-link-text');

    navigator.clipboard.writeText(url).then(() => {
        if (text) text.textContent = 'Tersalin!';
        if (btn) btn.classList.add('border-emerald-400', 'bg-emerald-50', 'text-emerald-700', 'dark:bg-emerald-950/40', 'dark:text-emerald-300');
        if (icon) {
            icon.classList.remove('text-slate-500', 'dark:text-slate-400');
            icon.classList.add('text-emerald-600', 'dark:text-emerald-400');
        }

        showCopyToast('Tautan artikel berhasil disalin ke clipboard!');

        setTimeout(() => {
            if (text) text.textContent = 'Salin Link';
            if (btn) btn.classList.remove('border-emerald-400', 'bg-emerald-50', 'text-emerald-700', 'dark:bg-emerald-950/40', 'dark:text-emerald-300');
            if (icon) {
                icon.classList.add('text-slate-500', 'dark:text-slate-400');
                icon.classList.remove('text-emerald-600', 'dark:text-emerald-400');
            }
        }, 2500);
    }).catch(err => {
        console.error('Failed to copy link:', err);
    });
}

function showCopyToast(message) {
    let toast = document.getElementById('copy-toast');
    if (!toast) return;
    const span = toast.querySelector('span');
    if (span) span.textContent = message;

    toast.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
    toast.classList.add('translate-y-0', 'opacity-100');

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
    }, 3000);
}

function triggerNativeShare(title, url) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(err => console.log('Share canceled or error:', err));
    } else {
        copyKnowledgeLink();
    }
}
</script>

{{-- Floating Toast Feedback Salin Link --}}
<div id="copy-toast" class="fixed bottom-6 right-6 z-50 transform translate-y-10 opacity-0 pointer-events-none transition-all duration-300 flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-xs font-semibold shadow-2xl">
    <svg class="w-4 h-4 text-emerald-400 dark:text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    <span>Tautan artikel berhasil disalin ke clipboard!</span>
</div>
@endsection

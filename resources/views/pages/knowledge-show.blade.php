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

            {{-- Tombol Bookmark / Simpan --}}
            <div class="ml-auto shrink-0 flex items-center gap-2">
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

                    <div class="flex items-center gap-3 shrink-0">
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
                            <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                {{ $commentsCount }}
                            </span>
                        </a>
                    </div>
                </div>

                {{-- Section Komentar --}}
                <section id="komentar" class="scroll-mt-28 space-y-6 pt-4" x-data="{ isCollapsed: false, replyToId: null, replyToName: '', setReply(id, name) { this.replyToId = id; this.replyToName = name; this.isCollapsed = false; this.$nextTick(() => { const el = document.getElementById('comment-textarea'); if(el) el.focus(); }); }, cancelReply() { this.replyToId = null; this.replyToName = ''; } }">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                            <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Diskusi & Komentar
                            <span class="text-xs px-2.5 py-1 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold">
                                {{ $commentsCount }}
                            </span>
                        </h2>
                    </div>

                    {{-- Flash Session Success --}}
                    @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    {{-- Daftar Komentar --}}
                    @if($comments->count() > 0)
                        <div class="space-y-4">
                            @foreach($comments as $comment)
                                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-sm text-slate-700 dark:text-slate-200">
                                                @if($comment->user && $comment->user->foto_profil)
                                                    <img src="{{ asset('storage/' . $comment->user->foto_profil) }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($comment->user?->name ?? 'A', 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-sm text-slate-900 dark:text-white">
                                                        {{ $comment->user?->name ?? 'Anonim' }}
                                                    </span>
                                                    @if($comment->user && in_array($comment->user->role, ['Super Admin', 'Admin Pusat', 'Admin']))
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300 font-semibold">Admin</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-slate-400 dark:text-slate-500">
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Aksi Hapus Komentar jika pemilik atau admin --}}
                                        @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->isAdmin()))
                                            <form action="{{ route('knowledge.comment.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-slate-400 hover:text-rose-600 transition p-1" title="Hapus komentar">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed pl-1 sm:pl-3">
                                        {{ $comment->konten }}
                                    </div>

                                    {{-- Tombol Balas --}}
                                    @auth
                                    <div class="pl-1 sm:pl-3 pt-1">
                                        <button type="button" 
                                                @click="setReply({{ $comment->id }}, '{{ addslashes($comment->user?->name ?? 'Anonim') }}')"
                                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                            </svg>
                                            Balas
                                        </button>
                                    </div>
                                    @endauth

                                    {{-- Balasan Bersarang (Replies) --}}
                                    @if($comment->replies && $comment->replies->count() > 0)
                                        <div class="ml-4 sm:ml-8 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 space-y-3">
                                            @foreach($comment->replies as $reply)
                                                <div class="bg-slate-50 dark:bg-slate-750/50 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700 space-y-2">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <div class="flex items-center gap-2.5">
                                                            <div class="w-8 h-8 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-200">
                                                                @if($reply->user && $reply->user->foto_profil)
                                                                    <img src="{{ asset('storage/' . $reply->user->foto_profil) }}" alt="{{ $reply->user->name }}" class="w-full h-full object-cover">
                                                                @else
                                                                    {{ strtoupper(substr($reply->user?->name ?? 'A', 0, 1)) }}
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <span class="font-bold text-xs text-slate-900 dark:text-white">
                                                                    {{ $reply->user?->name ?? 'Anonim' }}
                                                                </span>
                                                                <span class="text-[11px] text-slate-400 dark:text-slate-500 block">
                                                                    {{ $reply->created_at->diffForHumans() }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->isAdmin()))
                                                            <form action="{{ route('knowledge.comment.destroy', $reply->id) }}" method="POST" onsubmit="return confirm('Hapus balasan ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-xs text-slate-400 hover:text-rose-600 transition p-1" title="Hapus balasan">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed pl-1 sm:pl-2">
                                                        {{ $reply->konten }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Belum ada komentar</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Jadilah yang pertama memberikan tanggapan atau pertanyaan pada pengetahuan ini!</p>
                        </div>
                    @endif

                    {{-- Kotak Tulis Komentar Sticky / Floating (Mengikuti saat digeser ke atas & bawah persis seperti forum) --}}
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

                        <form x-show="!isCollapsed" action="{{ route('knowledge.comment', $knowledge->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="parent_id" :value="replyToId">
                            <textarea 
                                id="comment-textarea"
                                name="konten" 
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
                                    class="ml-auto px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kirim Komentar
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
</script>
@endsection

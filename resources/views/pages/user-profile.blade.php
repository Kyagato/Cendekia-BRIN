@extends('layouts.public')
@section('title', 'Profil ' . $user->name)

@section('content')
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen pb-16 transition-colors duration-200" style="padding-top: 85px;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-6 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition font-medium">Beranda</a>
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            <span class="text-slate-500 dark:text-slate-400">Profil Pengguna</span>
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            <span class="text-slate-800 dark:text-slate-200 font-semibold line-clamp-1">{{ $user->name }}</span>
        </nav>

        {{-- Main Profile Card Header --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-8">
            
            {{-- Banner Cover --}}
            <div class="h-36 sm:h-48 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 relative overflow-hidden">
                <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                
                {{-- Badges on Banner --}}
                <div class="absolute top-4 right-4 flex items-center gap-2">
                    <span class="bg-black/35 backdrop-blur-md px-3 py-1 rounded-full text-white text-xs font-medium flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Profil Publik
                    </span>
                </div>
            </div>

            {{-- User Info Section --}}
            <div class="px-6 sm:px-8 pb-8 pt-0 relative">
                <div class="flex flex-col sm:flex-row items-center sm:items-end justify-between gap-5 -mt-16 sm:-mt-20 mb-6">
                    
                    {{-- Avatar & Identitas --}}
                    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-5 text-center sm:text-left">
                        <div class="relative shrink-0">
                            @if($user->foto_profil)
                                <img src="{{ asset('storage/' . $user->foto_profil) }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl object-cover border-4 border-white dark:border-slate-900 shadow-xl bg-slate-100 dark:bg-slate-800">
                            @else
                                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl border-4 border-white dark:border-slate-900 shadow-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-extrabold text-4xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full" title="Akun Aktif"></span>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                                    {{ $user->name }}
                                </h1>
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    {{ $user->role ?? 'Anggota' }}
                                </span>
                            </div>

                            @if($user->pekerjaan || $user->instansi)
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                                {{ $user->pekerjaan ?? 'Anggota' }} &bull; {{ $user->instansi ?? 'Badan Riset dan Inovasi Nasional (BRIN)' }}
                            </p>
                            @endif

                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-slate-500 dark:text-slate-400 pt-1">
                                @if($user->alamat)
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $user->alamat }}</span>
                                </span>
                                @endif

                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Bergabung {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Bagikan Profil --}}
                    <div x-data="{
                        copied: false,
                        async share() {
                            if (navigator.share) {
                                try {
                                    await navigator.share({
                                        title: 'Profil {{ addslashes($user->name) }} - Cendekia BRIN',
                                        url: window.location.href
                                    });
                                    return;
                                } catch (err) {}
                            }
                            try {
                                await navigator.clipboard.writeText(window.location.href);
                                this.copied = true;
                                setTimeout(() => this.copied = false, 3000);
                            } catch (e) {
                                prompt('Salin tautan profil ini:', window.location.href);
                            }
                        }
                    }" class="shrink-0 w-full sm:w-auto flex justify-center sm:justify-end">
                        <button type="button"
                                @click="share()"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-sm"
                                :class="copied ? 'bg-emerald-600 text-white' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white'">
                            <template x-if="!copied">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                    <span>Bagikan Profil</span>
                                </span>
                            </template>
                            <template x-if="copied">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Tautan Berhasil Disalin!</span>
                                </span>
                            </template>
                        </button>
                    </div>

                </div>

                {{-- Statistik Ringkas Pengguna --}}
                <div class="grid grid-cols-3 gap-3 sm:gap-6 p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800">
                    <div class="text-center">
                        <div class="text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ $totalKnowledge }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Pengetahuan Terbit</div>
                    </div>
                    <div class="text-center border-x border-slate-200 dark:border-slate-700">
                        <div class="text-xl sm:text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($totalViews) }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Total Pembaca (Views)</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ $totalThreads }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Diskusi Forum</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Content Tabs Section --}}
        <div x-data="{ tab: 'pengetahuan' }" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            
            {{-- Tabs Header --}}
            <div class="flex border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 overflow-x-auto">
                <button type="button"
                        @click="tab = 'pengetahuan'"
                        :class="tab === 'pengetahuan' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-200 font-medium'"
                        class="py-4 px-4 sm:px-6 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Pengetahuan Terbit ({{ $totalKnowledge }})</span>
                </button>

                <button type="button"
                        @click="tab = 'forum'"
                        :class="tab === 'forum' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-200 font-medium'"
                        class="py-4 px-4 sm:px-6 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    <span>Diskusi Forum ({{ $totalThreads }})</span>
                </button>

                <button type="button"
                        @click="tab = 'biodata'"
                        :class="tab === 'biodata' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-200 font-medium'"
                        class="py-4 px-4 sm:px-6 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Informasi & Instansi</span>
                </button>
            </div>

            {{-- Tab 1: Pengetahuan Terbit --}}
            <div x-show="tab === 'pengetahuan'" class="p-6 sm:p-8">
                @if($knowledgeList->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($knowledgeList as $item)
                            @include('components.knowledge-card', ['item' => $item])
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($knowledgeList->hasPages())
                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                            {{ $knowledgeList->links() }}
                        </div>
                    @endif
                @else
                    <div class="py-16 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-50 dark:bg-slate-800 text-blue-500 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum ada karya pengetahuan</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                            Pengguna ini belum memiliki pengetahuan yang diterbitkan secara publik.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Tab 2: Diskusi Forum --}}
            <div x-show="tab === 'forum'" x-cloak class="p-6 sm:p-8">
                @if($forumThreads->count() > 0)
                    <div class="space-y-4">
                        @foreach($forumThreads as $thread)
                            <a href="{{ route('forum.show', $thread->id) }}"
                               class="block p-5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md bg-slate-50/50 dark:bg-slate-800/40 transition group">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                            @if($thread->category)
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                    {{ $thread->category->nama_kategori }}
                                                </span>
                                            @endif
                                            <span class="text-xs text-slate-400 dark:text-slate-500">
                                                {{ $thread->created_at?->diffForHumans() }}
                                            </span>
                                        </div>
                                        <h4 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                            {{ $thread->judul }}
                                        </h4>
                                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 line-clamp-2 mt-1">
                                            {{ Str::limit(strip_tags($thread->konten), 160) }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        <span>{{ $thread->replies_count }} balasan</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="py-16 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 dark:bg-slate-800 text-indigo-500 dark:text-indigo-400 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum ada diskusi forum</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                            Pengguna ini belum membuat topik diskusi di forum.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Tab 3: Informasi & Instansi --}}
            <div x-show="tab === 'biodata'" x-cloak class="p-6 sm:p-8">
                <div class="max-w-2xl mx-auto bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200 dark:border-slate-700/60">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Informasi Publik Pengguna
                    </h3>
                    <dl class="divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="font-semibold text-slate-500 dark:text-slate-400">Nama Lengkap</dt>
                            <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="font-semibold text-slate-500 dark:text-slate-400">Peran Platform</dt>
                            <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ $user->role ?? 'Anggota' }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="font-semibold text-slate-500 dark:text-slate-400">Instansi / Organisasi</dt>
                            <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ $user->instansi ?? 'Badan Riset dan Inovasi Nasional (BRIN)' }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="font-semibold text-slate-500 dark:text-slate-400">Pekerjaan / Jabatan</dt>
                            <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ $user->pekerjaan ?? '-' }}</dd>
                        </div>
                        @if($user->alamat)
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="font-semibold text-slate-500 dark:text-slate-400">Wilayah / Domisili</dt>
                            <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ $user->alamat }}</dd>
                        </div>
                        @endif
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="font-semibold text-slate-500 dark:text-slate-400">Tanggal Bergabung</dt>
                            <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">
                                {{ $user->created_at ? $user->created_at->translatedFormat('l, d F Y') : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

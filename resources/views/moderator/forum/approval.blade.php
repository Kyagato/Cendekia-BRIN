@extends('layouts.admin')
@section('title', 'Atur Forum')

@section('breadcrumbs')
<li class="flex items-center">
    <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-slate-800 dark:text-slate-200 font-semibold">Atur Forum</span>
</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Atur Forum</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola dan moderasi topik diskusi yang masuk dari anggota.</p>
        </div>
        <a href="{{ route('forum.index') }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Lihat Forum Publik
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm text-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $counts['pending'] }}</div>
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Menunggu Validasi</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $counts['approved'] }}</div>
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Disetujui</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-4 shadow-sm">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $counts['rejected'] }}</div>
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Ditolak</div>
            </div>
        </div>
    </div>

    {{-- Main Card Container --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

        {{-- Tab Filter --}}
        <div class="flex border-b border-slate-200 dark:border-slate-700 overflow-x-auto bg-slate-50/50 dark:bg-slate-900/50">
            @php
                $tabs = [
                    'pending'  => ['label' => 'Menunggu', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'approved' => ['label' => 'Disetujui','icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'rejected' => ['label' => 'Ditolak',  'icon' => 'M6 18L18 6M6 6l12 12'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
            <a href="{{ route('moderator.forum.approval', ['status' => $key]) }}"
               class="flex items-center gap-2 px-6 py-4 text-xs font-semibold border-b-2 whitespace-nowrap transition
                      {{ $status === $key
                          ? 'border-red-600 text-red-600 dark:text-red-400 dark:border-red-400 bg-white dark:bg-slate-800'
                          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
                {{ $tab['label'] }}
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold
                             {{ $status === $key ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300 border border-red-200 dark:border-red-800' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                    {{ $counts[$key] }}
                </span>
            </a>
            @endforeach
        </div>

        {{-- Empty State --}}
        @if($threads->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h3 class="text-base font-bold text-slate-700 dark:text-slate-300 mb-1">Tidak ada topik diskusi</h3>
            <p class="text-slate-400 dark:text-slate-500 text-xs">Tidak ada topik dengan status <span class="font-medium capitalize">{{ $status }}</span> saat ini.</p>
        </div>

        {{-- Table --}}
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Penulis</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Judul &amp; Cuplikan</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kategori</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="py-3 px-6 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($threads as $thread)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        {{-- Penulis --}}
                        <td class="py-3 px-6 min-w-[160px]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-xs font-bold shrink-0 overflow-hidden shadow-sm">
                                    @if($thread->user->foto_profil ?? null)
                                        <img src="{{ asset('storage/' . $thread->user->foto_profil) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-slate-800 dark:text-slate-100 text-xs truncate">{{ $thread->user->name ?? 'Unknown' }}</div>
                                    <div class="text-slate-400 dark:text-slate-500 text-[11px]">{{ $thread->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Judul & Cuplikan --}}
                        <td class="py-3 px-6 max-w-sm">
                            <a href="{{ route('forum.show', $thread->id) }}" target="_blank"
                               class="font-semibold text-slate-800 dark:text-slate-100 hover:text-red-600 dark:hover:text-red-400 transition line-clamp-1 block text-sm">
                                {{ $thread->judul }}
                            </a>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 line-clamp-2">
                                {{ Str::limit(strip_tags($thread->konten), 120) }}
                            </p>
                            @if($thread->rejection_note && $status === 'rejected')
                            <div class="mt-1.5 px-2.5 py-1 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded text-xs text-red-600 dark:text-red-300">
                                <span class="font-semibold">Alasan:</span> {{ Str::limit($thread->rejection_note, 80) }}
                            </div>
                            @endif
                        </td>

                        {{-- Kategori --}}
                        <td class="py-3 px-6 min-w-[120px]">
                            @if($thread->category)
                            <span class="inline-block px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-xs font-medium border border-slate-200 dark:border-slate-600">
                                {{ $thread->category->nama_kategori }}
                            </span>
                            @else
                            <span class="text-slate-400 dark:text-slate-500 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td class="py-3 px-6 min-w-[110px]">
                            @if($thread->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-100 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Menunggu
                            </span>
                            @elseif($thread->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui
                            </span>
                            @elseif($thread->status === 'rejected')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-red-100 dark:bg-red-950 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Ditolak
                            </span>
                            @endif
                        </td>

                        {{-- Aksi Dropdown (titik tiga) --}}
                        <td class="py-3 px-6 text-right">
                            <div x-data="{ open: false, showRejectModal: false }" class="relative inline-block text-left">

                                {{-- Trigger Button Titik 3 --}}
                                <button @click="open = !open" @click.outside="open = false" type="button"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>

                                {{-- Dropdown Menu --}}
                                <div x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-1 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 text-left focus:outline-none">

                                    <a href="{{ route('forum.show', $thread->id) }}" target="_blank"
                                       class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition w-full">
                                        <svg class="w-4 h-4 shrink-0 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Topik
                                    </a>

                                    @if($thread->status !== 'approved')
                                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                    <form action="{{ route('moderator.forum.approve', $thread->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="flex items-center gap-2.5 w-full px-4 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950 transition">
                                            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Setujui
                                        </button>
                                    </form>
                                    @endif

                                    @if($thread->status !== 'rejected')
                                    <button @click="open = false; showRejectModal = true"
                                            class="flex items-center gap-2.5 w-full px-4 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 transition">
                                        <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Tolak
                                    </button>
                                    @endif
                                </div>

                                {{-- Reject Modal --}}
                                <div x-show="showRejectModal"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                                     style="display:none;">
                                    <div @click.outside="showRejectModal = false"
                                         class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md text-left">
                                        <div class="p-6">
                                            <div class="flex items-start gap-3 mb-5">
                                                <div class="w-10 h-10 bg-red-100 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Tolak Topik Diskusi</h3>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">"{{ Str::limit($thread->judul, 60) }}"</p>
                                                </div>
                                            </div>
                                            <form action="{{ route('moderator.forum.reject', $thread->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-2">
                                                        Alasan Penolakan <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea name="rejection_note" rows="4" required
                                                              placeholder="Tuliskan alasan penolakan untuk disampaikan kepada penulis..."
                                                              class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-xs focus:border-red-500 focus:ring-red-500 transition resize-none"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showRejectModal = false"
                                                            class="px-4 py-2 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                            class="px-4 py-2 rounded-lg text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm">
                                                        Konfirmasi Tolak
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($threads->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
            {{ $threads->links() }}
        </div>
        @endif
        @endif

    </div>
</div>
@endsection

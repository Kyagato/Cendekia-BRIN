@extends('layouts.admin')
@section('title', 'Validasi Pengetahuan')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li>
        <a href="{{ route('knowledge.index') }}" class="hover:text-red-600 transition">Pengetahuan</a>
    </li>
    <li>
        <svg class="w-4 h-4 text-slate-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 font-semibold">Ubah</li>
@endsection

@section('content')
<div x-data="{ showTolakModal: false }" class="max-w-6xl mx-auto">

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Ubah Pengetahuan</h1>
            <p class="text-sm text-slate-500 mt-1">Tinjau konten sebelum memberikan keputusan</p>
        </div>
        <a href="{{ route('knowledge.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </div>

    {{-- Main Card --}}
    <div class="bg-white shadow-md rounded-xl p-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- LEFT COLUMN: Detail Konten (read-only) --}}
            <div class="lg:col-span-7 space-y-6">

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Judul</label>
                    <div class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-800">{{ $knowledge->judul }}</div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-3">Format</label>
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                        @foreach(['Gambar', 'Video', 'Audio', 'Teks'] as $fmt)
                        <label class="flex items-center gap-2">
                            <input type="radio" disabled {{ $knowledge->tipe === $fmt ? 'checked' : '' }} class="text-red-600 w-4 h-4">
                            <span class="text-sm font-medium text-slate-700">{{ $fmt === 'Teks' ? 'Text' : $fmt }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                @if($knowledge->url_teks)
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Url {{ $knowledge->tipe }}</label>
                    <div class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50">
                        <a href="{{ $knowledge->url_teks }}" target="_blank" class="text-red-600 hover:underline text-sm break-all">{{ $knowledge->url_teks }}</a>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Pastikan url dapat diakses</p>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">Penulis</label>
                        <div class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800">{{ $knowledge->penulis ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">Kolaborator</label>
                        <div class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800">{{ $knowledge->kolaborator ?? '-' }}</div>
                    </div>
                </div>

                @if($knowledge->deskripsi)
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Ringkasan</label>
                    <div class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 text-sm leading-relaxed min-h-[80px]">{!! nl2br(e($knowledge->deskripsi)) !!}</div>
                </div>
                @endif

                @if($knowledge->detail)
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Detail</label>
                    <div class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 text-sm leading-relaxed min-h-[120px]">{!! nl2br(e($knowledge->detail)) !!}</div>
                </div>
                @endif

                @if($knowledge->file_path)
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Thumbnail / File Lampiran</label>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center gap-3">
                        <img src="{{ asset('storage/' . $knowledge->file_path) }}" alt="Thumbnail" class="w-16 h-16 object-cover rounded border border-slate-200">
                        <p class="text-sm text-slate-600">File terpasang</p>
                    </div>
                </div>
                @endif

            </div>

            {{-- RIGHT COLUMN: Sidebar --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- Persetujuan --}}
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800">Persetujuan</h3>
                    </div>
                    <div class="px-5 py-4 flex flex-wrap gap-2">
                        <form action="{{ route('validasi.approve', $knowledge->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    onclick="return confirm('Setujui dan terbitkan konten ini?')"
                                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Setujui &amp; Terbit
                            </button>
                        </form>
                        <button type="button" @click="showTolakModal = true"
                                class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            Tolak
                        </button>
                    </div>
                </div>

                {{-- Status --}}
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800">Status</h3>
                    </div>
                    <div class="px-5 py-4 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $knowledge->status === 'Disetujui' ? 'bg-green-500' : ($knowledge->status === 'Ditolak' ? 'bg-red-500' : ($knowledge->status === 'Diajukan' ? 'bg-blue-500' : 'bg-yellow-400')) }}"></span>
                        <span class="text-sm font-medium text-slate-700">
                            {{ $knowledge->status === 'Diajukan' ? 'Menunggu Persetujuan' : $knowledge->status }}
                        </span>
                    </div>
                </div>

                {{-- Kategori --}}
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800">Kategori</h3>
                    </div>
                    <div class="px-5 py-4 flex items-center justify-between">
                        <span class="text-sm text-slate-700">{{ $knowledge->category->nama_kategori ?? '-' }}</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                </div>

                {{-- Label --}}
                @if($knowledge->tags && $knowledge->tags->count())
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800">Label</h3>
                    </div>
                    <div class="px-5 py-4 flex flex-wrap gap-2">
                        @foreach($knowledge->tags as $tag)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $tag->nama_label }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Tanggal Terbit --}}
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800">Tanggal Terbit</h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700">
                            {{ $knowledge->tanggal_terbit ? \Carbon\Carbon::parse($knowledge->tanggal_terbit)->format('d M Y') : '-' }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Tolak Pengajuan --}}
    <div x-show="showTolakModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         style="display: none;">

        <div x-show="showTolakModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-800">Tolak Pengajuan</h2>
                <button @click="showTolakModal = false" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('validasi.reject', $knowledge->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-slate-600">Apakah anda yakin ingin menolak pengajuan?</p>
                    <textarea name="alasan_tolak" rows="4" placeholder="Tulis alasan penolakan..."
                              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-red-600 focus:border-red-600 text-sm text-slate-800 resize-none"></textarea>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button type="button" @click="showTolakModal = false"
                            class="px-5 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

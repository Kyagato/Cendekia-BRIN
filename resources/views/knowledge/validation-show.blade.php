@extends('layouts.admin')

@section('title', 'Detail Validasi Pengetahuan')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1.5fr_0.9fr]">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-sm font-medium text-blue-600">Status: {{ $knowledge->status }}</p>
                <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $knowledge->judul }}</h1>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                {{ $knowledge->status == 'Disetujui' ? 'bg-green-100 text-green-700' : ($knowledge->status == 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                {{ $knowledge->status }}
            </span>
        </div>

        <div class="space-y-5 text-slate-700">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Deskripsi</p>
                <p class="mt-2 leading-relaxed whitespace-pre-line">{{ $knowledge->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</p>
                    <p class="mt-2 font-medium">{{ $knowledge->category->nama_kategori ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tipe</p>
                    <p class="mt-2 font-medium">{{ $knowledge->tipe }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pengirim</p>
                    <p class="mt-2 font-medium">{{ $knowledge->user->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal pengajuan</p>
                    <p class="mt-2 font-medium">{{ $knowledge->created_at ? $knowledge->created_at->translatedFormat('d F Y H:i') : '-' }}</p>
                </div>
            </div>

            @if($knowledge->tags->isNotEmpty())
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Label</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($knowledge->tags as $tag)
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $tag->nama_label }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($knowledge->file_path)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Lampiran</p>
                    <a href="{{ asset('storage/' . $knowledge->file_path) }}" target="_blank" class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                        Buka file lampiran
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Catatan analisis</h2>

            @if($knowledge->catatan_validasi)
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-sm text-slate-600 whitespace-pre-line">
                    {{ $knowledge->catatan_validasi }}
                </div>
            @else
                <p class="text-sm text-slate-500">Belum ada catatan analisis.</p>
            @endif

            @if($knowledge->validated_by)
                <p class="mt-4 text-xs text-slate-500">
                    Divalidasi oleh <span class="font-semibold text-slate-700">{{ $knowledge->validator->name ?? '-' }}</span>
                    pada {{ $knowledge->validated_at ? $knowledge->validated_at->translatedFormat('d F Y H:i') : '-' }}
                </p>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Tindakan validasi</h2>

            <form method="POST" action="{{ route('validasi.approve', $knowledge) }}" class="mb-4">
                @csrf
                @method('PATCH')
                <label for="approve_note" class="block text-sm font-medium text-slate-700 mb-2">Catatan persetujuan (opsional)</label>
                <textarea id="approve_note" name="catatan_validasi" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Tambahkan catatan untuk persetujuan...">{{ old('catatan_validasi', $knowledge->catatan_validasi) }}</textarea>
                <button type="submit" class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    Setujui pengetahuan
                </button>
            </form>

            <form method="POST" action="{{ route('validasi.reject', $knowledge) }}">
                @csrf
                @method('PATCH')
                <label for="reject_note" class="block text-sm font-medium text-slate-700 mb-2">Alasan penolakan</label>
                <textarea id="reject_note" name="catatan_validasi" rows="4" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-red-500 focus:ring-red-500" placeholder="Tuliskan alasan penolakan dan saran perbaikan...">{{ old('catatan_validasi', $knowledge->catatan_validasi) }}</textarea>
                <button type="submit" class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    Tolak pengetahuan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

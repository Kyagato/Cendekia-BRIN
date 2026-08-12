@extends('layouts.admin')

@section('title', 'Validasi Pengetahuan')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Menunggu validasi</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">{{ $knowledges->total() }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Disetujui</p>
            <p class="mt-3 text-3xl font-bold text-green-600">{{ \App\Models\Knowledge::where('status', 'Disetujui')->count() }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-sm text-slate-500">Ditolak</p>
            <p class="mt-3 text-3xl font-bold text-red-600">{{ \App\Models\Knowledge::where('status', 'Ditolak')->count() }}</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Daftar pengetahuan yang menunggu review</h2>
                <p class="text-sm text-slate-500">Analisis pengetahuan dapat meninjau dan memutuskan persetujuan atau penolakan.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($knowledges as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $item->judul }}</div>
                                <div class="text-xs text-slate-500">{{ $item->tipe }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->category->nama_kategori ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->created_at ? $item->created_at->translatedFormat('d F Y') : '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('validasi.show', $item) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                    Lihat & validasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada pengetahuan yang menunggu validasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($knowledges->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $knowledges->links() }}
            </div>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Review terakhir</h2>
        @forelse($recentReviews as $review)
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 py-3 last:border-b-0">
                <div>
                    <p class="font-semibold text-slate-800">{{ $review->judul }}</p>
                    <p class="text-sm text-slate-500">{{ $review->validator->name ?? 'Sistem' }} • {{ $review->validated_at ? $review->validated_at->translatedFormat('d F Y H:i') : '-' }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $review->status == 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $review->status }}
                </span>
            </div>
        @empty
            <p class="text-sm text-slate-500">Belum ada review yang selesai.</p>
        @endforelse
    </div>
</div>
@endsection

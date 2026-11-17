@extends('layouts.admin')
@section('title', 'Audit Log')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white border-l-4 border-blue-600 pl-3">Audit Log</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Riwayat aktivitas pengguna dan administrasi MojoPedia. Waktu ditampilkan dalam WIB.</p>
    </div>

    <form method="GET" action="{{ route('admin.audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
        <label class="text-sm text-slate-600 dark:text-slate-300">Dari tanggal
            <input type="date" name="from" value="{{ old('from', $filters['from'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm">
        </label>
        <label class="text-sm text-slate-600 dark:text-slate-300">Sampai tanggal
            <input type="date" name="to" value="{{ old('to', $filters['to'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm">
        </label>
        <label class="text-sm text-slate-600 dark:text-slate-300">Jenis aktivitas
            <select name="action" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm">
                <option value="">Semua aktivitas</option>
                @foreach($actions as $action)<option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ \App\Http\Controllers\Admin\AuditLogController::actionLabel($action) }}</option>@endforeach
            </select>
        </label>
        <label class="text-sm text-slate-600 dark:text-slate-300">Pelaku
            <input type="search" name="actor" value="{{ $filters['actor'] ?? '' }}" placeholder="Nama atau email" maxlength="100" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm">
        </label>
        <label class="text-sm text-slate-600 dark:text-slate-300">Hasil
            <select name="result" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white text-sm">
                <option value="">Semua hasil</option>
                <option value="success" @selected(($filters['result'] ?? '') === 'success')>Berhasil</option>
                <option value="failed" @selected(($filters['result'] ?? '') === 'failed')>Gagal / perlu pemeriksaan</option>
                <option value="recorded" @selected(($filters['result'] ?? '') === 'recorded')>Tercatat</option>
            </select>
        </label>
        <div class="sm:col-span-2 lg:col-span-5 flex gap-3">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">Terapkan Filter</button>
            <a href="{{ route('admin.audit.index') }}" class="rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300">Reset</a>
        </div>
        @if($errors->any())<p role="alert" class="sm:col-span-2 lg:col-span-5 text-sm text-red-600">{{ $errors->first() }}</p>@endif
    </form>

    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-x-auto">
        <table class="w-full min-w-[850px] text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400"><tr>
                <th scope="col" class="px-4 py-3">Waktu</th><th scope="col" class="px-4 py-3">Pelaku</th><th scope="col" class="px-4 py-3">Aktivitas</th><th scope="col" class="px-4 py-3">Target / Keterangan</th><th scope="col" class="px-4 py-3">Hasil</th><th scope="col" class="px-4 py-3">Detail</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($logs as $log)
                @php($details = \App\Http\Controllers\Admin\AuditLogController::details($log))
                <tr class="align-top text-slate-700 dark:text-slate-300">
                    <td class="px-4 py-4 whitespace-nowrap">{{ $log->created_at->copy()->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-4">{{ $log->user?->name ?? 'Sistem / tidak tersedia' }}</td>
                    <td class="px-4 py-4">{{ \App\Http\Controllers\Admin\AuditLogController::actionLabel($log->action) }}</td>
                    <td class="px-4 py-4 max-w-sm break-words">{{ $log->description }}</td>
                    <td class="px-4 py-4">{{ \App\Http\Controllers\Admin\AuditLogController::result($log) }}</td>
                    <td class="px-4 py-4"><details><summary class="cursor-pointer text-blue-600 dark:text-blue-400">Lihat</summary><div class="mt-2 space-y-1 text-xs min-w-48">
                        <p>ID catatan: {{ $log->id }}</p>
                        @forelse($details as $key => $value)
                            <p class="break-words"><strong>{{ str_replace('_', ' ', $key) }}:</strong> {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (is_scalar($value) ? $value : '-') }}</p>
                        @empty<p>Tidak ada detail tambahan.</p>@endforelse
                    </div></details></td>
                </tr>
            @empty<tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Tidak ada aktivitas yang sesuai.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $logs->links() }}</div>
</div>
@endsection

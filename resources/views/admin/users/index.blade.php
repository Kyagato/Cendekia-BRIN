@extends('layouts.admin')
@section('title', 'Manajemen Pengguna Sistem')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 font-semibold">Pengguna</li>
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Pengguna Sistem</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola data pengguna, peran, dan hak akses sistem.</p>
    </div>
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <!-- Search Bar -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pengguna..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-red-600 focus:border-red-600 text-slate-800 transition">
        </form>
        
        <!-- Add Button -->
        <a href="{{ route('admin.users.create') }}" class="shrink-0 inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Pengguna
        </a>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-max">
            <thead>
                <tr class="bg-gray-50 border-b border-slate-200">
                    <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap & Email</th>
                    <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Instansi</th>
                    <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Peran / Role</th>
                    <th class="py-3 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            @if($user->foto_profil)
                                <img src="{{ Storage::url($user->foto_profil) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-bold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-semibold text-slate-800">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600">{{ $user->instansi ?? '-' }}</td>
                    <td class="py-4 px-6">
                        @php
                            $roleClass = match($user->role) {
                                'Super Admin', 'Admin Pusat' => 'bg-red-100 text-red-700 border-red-200',
                                'Kreator Pengetahuan' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'Analisis Pengetahuan' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'Moderator' => 'bg-purple-100 text-purple-700 border-purple-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $roleClass }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300 rounded text-xs font-medium transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 rounded text-xs font-medium transition" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 px-6 text-center text-slate-500">Belum ada data pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-4 border-t border-slate-200">
        {{ $users->links() }}
    </div>
</div>
@endsection

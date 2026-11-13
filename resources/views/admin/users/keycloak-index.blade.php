@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Manajemen Pengguna</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Daftar akun dari Keycloak. Perubahan nama dan email disinkronkan ke MojoPedia.</p>
    </div>
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2">
        <label for="user-role" class="sr-only">Filter peran</label>
        <div class="relative">
        <select id="user-role" name="role" onchange="this.form.requestSubmit()" style="background-image: none" class="user-filter-select appearance-none pr-9 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm">
            <option value="">Semua Peran</option>
            @foreach(\App\Models\User::ALL_ROLES as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
            @endforeach
        </select>
            <svg class="user-filter-chevron pointer-events-none absolute right-3 top-1/2 h-4 w-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
            </svg>
        </div>
        <label for="user-sort" class="sr-only">Urutkan pengguna</label>
        <div class="relative">
        <select id="user-sort" name="sort" onchange="this.form.requestSubmit()" style="background-image: none" class="user-filter-select appearance-none pr-9 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm">
            <option value="default" @selected(request('sort', 'default') === 'default')>Default</option>
            <option value="name_asc" @selected(request('sort') === 'name_asc')>Nama A &rarr; Z</option>
            <option value="name_desc" @selected(request('sort') === 'name_desc')>Nama Z &rarr; A</option>
            <option value="role" @selected(request('sort') === 'role')>Hierarki Peran</option>
        </select>
            <svg class="user-filter-chevron pointer-events-none absolute right-3 top-1/2 h-4 w-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
            </svg>
        </div>
        <label for="user-search" class="sr-only">Cari pengguna</label>
        <input id="user-search" name="q" value="{{ request('q') }}" maxlength="255" placeholder="Nama, email, atau username..." class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm">
        <button class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-all hover:-translate-y-0.5 shadow-sm hover:shadow-md">Cari</button>
    </form>
</div>
@if(session('success'))
<div role="status" class="mb-4 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">{{ session('success') }}</div>
@endif
@if($errors->any())
<div role="alert" class="mb-4 rounded-xl bg-red-50 p-4 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $errors->first() }}</div>
@endif
@if($error)
<div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300">
    <p>{{ $error }}</p>
    <a href="{{ request()->fullUrl() }}" class="inline-block mt-3 font-semibold underline">Coba lagi</a>
</div>
@else
<div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400"><tr>
                <th class="px-5 py-3">Nama dan Email</th><th class="px-5 py-3">Username</th>
                <th class="px-5 py-3">Peran</th><th class="px-5 py-3">Status Akun</th><th class="px-5 py-3 text-right">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($users as $entry)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="px-5 py-4"><div class="flex items-center gap-3">
                        @if($entry['foto_profil'])
                            <img src="{{ asset('storage/'.$entry['foto_profil']) }}" alt="Foto profil {{ $entry['name'] }}" class="h-10 w-10 shrink-0 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                        @else
                            <span class="h-10 w-10 shrink-0 rounded-full bg-blue-50 dark:bg-slate-700 text-blue-600 dark:text-blue-300 inline-flex items-center justify-center font-bold" aria-hidden="true">{{ mb_strtoupper(mb_substr($entry['name'], 0, 1)) }}</span>
                        @endif
                        <div><div class="font-semibold text-slate-900 dark:text-white">{{ $entry['name'] }}</div><div class="text-xs text-slate-500 dark:text-slate-400">{{ $entry['email'] }}</div></div>
                    </div></td>
                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $entry['username'] }}</td>
                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $entry['role'] }}</td>
                    <td class="px-5 py-4"><span class="px-2 py-1 rounded-md text-xs {{ $entry['enabled'] ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">{{ $entry['enabled'] ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="px-5 py-4 text-right">
                    @if($entry['can_edit'])
                        <div class="relative inline-block text-left" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.stop="open = false; $refs.trigger.focus()">
                            <button type="button" x-ref="trigger" @click="open = !open" :aria-expanded="open" aria-label="Aksi pengguna" aria-controls="user-actions-{{ $loop->index }}" class="inline-flex p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700 focus-visible:ring-2 focus-visible:ring-blue-500 transition cursor-pointer">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5a2 2 0 110-4 2 2 0 010 4zm0 9a2 2 0 110-4 2 2 0 010 4zm0 9a2 2 0 110-4 2 2 0 010 4z" /></svg>
                            </button>
                            <div id="user-actions-{{ $loop->index }}" x-show="open" x-cloak x-transition.origin.top.right class="absolute right-full top-0 mr-1 z-30 w-36 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl py-1">
                                <a href="{{ route('admin.users.keycloak.edit', $entry['id']) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Edit</a>
                                <form method="POST" action="{{ route('admin.users.keycloak.destroy', $entry['id']) }}" onsubmit="return confirm('Hapus permanen akun ini dari Keycloak dan MojoPedia? Pengetahuan, forum, dan komentar terkait akun lokal juga dapat terhapus. Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="confirm_delete" value="1">
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <span class="text-xs text-slate-400">Hanya lihat</span>
                    @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-8 text-center text-slate-500 dark:text-slate-400">Tidak ada pengguna yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-200 dark:border-slate-700 p-4">{{ $users->links() }}</div>
</div>
@endif
<style>
.user-filter-chevron { transform: translateY(-50%); transition: transform 150ms ease; }
.user-filter-select:open + .user-filter-chevron { transform: translateY(-50%) rotate(180deg); }
@media (prefers-reduced-motion: reduce) { .user-filter-chevron { transition: none; } }
</style>
@endsection

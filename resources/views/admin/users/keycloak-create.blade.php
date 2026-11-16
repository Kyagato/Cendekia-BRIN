@extends('layouts.admin')
@section('title', 'Tambah Pengguna')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb"><a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Manajemen Pengguna</a><span aria-hidden="true">/</span><span class="font-semibold text-slate-800 dark:text-slate-200">Tambah Pengguna</span></nav>
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
        <header class="pb-5 border-b border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
            <div><h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">Tambah Pengguna</h1><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Buat akun baru di Keycloak dan MojoPedia dengan role yang sesuai.</p></div>
            <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-700 transition">Kembali</a>
        </header>
        @if($errors->has('keycloak'))<div role="alert" class="mt-5 p-3 rounded-lg bg-rose-50 dark:bg-rose-950/30 text-sm text-rose-600 dark:text-rose-400">{{ $errors->first('keycloak') }}</div>@endif
        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <input type="hidden" name="request_key" value="{{ $requestKey }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
<div>
<label for="username" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Username</label>
<input id="username" name="username" type="text" maxlength="255" required  value="{{ old('username') }}" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" >
@error('username')<p role="alert" class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Email</label>
<input id="email" name="email" type="email" maxlength="255" required  value="{{ old('email') }}" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" >
@error('email')<p role="alert" class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="firstName" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Nama Depan</label>
<input id="firstName" name="firstName" type="text" maxlength="100" required  value="{{ old('firstName') }}" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" >
@error('firstName')<p role="alert" class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="lastName" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Nama Belakang</label>
<input id="lastName" name="lastName" type="text" maxlength="100"   value="{{ old('lastName') }}" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" >
@error('lastName')<p role="alert" class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Password Awal</label>
<input id="password" name="password" type="password" maxlength="72" required  class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" autocomplete="new-password">
@error('password')<p role="alert" class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
</div>
<div>
<label for="password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Konfirmasi Password</label>
<input id="password_confirmation" name="password_confirmation" type="password" maxlength="72" required  class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" autocomplete="new-password">
@error('password_confirmation')<p role="alert" class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
</div>
                <div>
                    <label for="role" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Peran / Role</label>
                    <select id="role" name="role" required class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                        <option value="" disabled @selected(!old('role'))>Pilih role</option>
                        @foreach($allowedRoles as $role)<option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>@endforeach
                    </select>
                    @error('role')<p role="alert" class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex justify-end items-center gap-3 pt-5 border-t border-slate-100 dark:border-slate-700/60">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400">Batal</a>
                <button type="submit" :disabled="submitting" class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm hover:shadow-md hover:-translate-y-0.5 active:scale-95 transition-all duration-200 disabled:opacity-50 disabled:cursor-wait">
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90 motion-reduce:transform-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M12 5v14M5 12h14" /></svg>
                    <span x-text="submitting ? 'Membuat akun...' : 'Tambah Pengguna'">Tambah Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

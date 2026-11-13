@extends('layouts.admin')
@section('title', 'Edit Pengguna')

@section('content')
@php
    $displayName = trim(($remote['firstName'] ?? '').' '.($remote['lastName'] ?? '')) ?: ($remote['username'] ?? 'Pengguna');
@endphp
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showPassword: false, showConfirmation: false }">
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('admin.users.index') }}" class="hover:text-[#2563eb] dark:hover:text-blue-400 transition">Manajemen Pengguna</a>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" /></svg>
        <span class="font-semibold text-slate-800 dark:text-slate-200">Edit Pengguna</span>
    </nav>

    @if(session('success'))
        <div role="status" class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 sm:p-8">
        <header class="pb-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">Edit Pengguna</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perbarui informasi akun dan kata sandi pengguna.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition self-start sm:self-auto border border-slate-200 dark:border-slate-700 shadow-sm">
                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0 7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </header>

        <div class="mt-6 p-4 bg-slate-50/70 dark:bg-slate-900/40 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-center gap-5">
            <div class="w-24 h-24 shrink-0 rounded-full overflow-hidden bg-slate-200 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 shadow-sm flex items-center justify-center text-slate-600 dark:text-slate-200 font-bold text-2xl">
                @if($local?->foto_profil)
                    <img src="{{ asset('storage/'.$local->foto_profil) }}" alt="Foto profil {{ $displayName }}" class="w-full h-full object-cover">
                @else
                    <span>{{ mb_strtoupper(mb_substr($displayName, 0, 1)) }}</span>
                @endif
            </div>
            <div class="min-w-0 text-center sm:text-left">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100 break-words">{{ $displayName }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 break-all">{{ $remote['email'] ?? '' }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Informasi akun dan kata sandi tersinkron dengan Keycloak.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.keycloak.update', $remote['id']) }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')
            @if($errors->has('keycloak'))
                <div role="alert" class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/30 text-sm text-rose-600 dark:text-rose-400">{{ $errors->first('keycloak') }}</div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 pt-2">
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Username</label>
                    <input id="username" type="text" name="username" required maxlength="255" value="{{ old('username', $remote['username'] ?? '') }}" placeholder="Username pengguna" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition" aria-describedby="username-error">
                    @error('username')<p id="username-error" class="mt-1 text-xs text-rose-500" role="alert">{{ $message }}</p>@enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Gunakan huruf, angka, titik, garis bawah, tanda @ atau tanda hubung.</p>
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" required maxlength="255" value="{{ old('email', $remote['email'] ?? '') }}" placeholder="nama@email.com" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition" aria-describedby="email-error">
                    @error('email')<p id="email-error" class="mt-1 text-xs text-rose-500" role="alert">{{ $message }}</p>@enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Jika email berubah, status verifikasi email direset.</p>
                </div>
                <div>
                    <label for="firstName" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Nama Depan</label>
                    <input id="firstName" type="text" name="firstName" required maxlength="100" value="{{ old('firstName', $remote['firstName'] ?? '') }}" placeholder="Nama depan pengguna" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition" aria-describedby="firstName-error">
                    @error('firstName')<p id="firstName-error" class="mt-1 text-xs text-rose-500" role="alert">{{ $message }}</p>@enderror
                    
                </div>
                <div>
                    <label for="lastName" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Nama Belakang</label>
                    <input id="lastName" type="text" name="lastName"  maxlength="100" value="{{ old('lastName', $remote['lastName'] ?? '') }}" placeholder="Nama belakang pengguna" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition" aria-describedby="lastName-error">
                    @error('lastName')<p id="lastName-error" class="mt-1 text-xs text-rose-500" role="alert">{{ $message }}</p>@enderror
                    
                </div>
                <div>
                    <label for="role" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Peran / Role</label>
                    <select id="role" name="role" required class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                        @foreach($allowedRoles as $role)
                            <option value="{{ $role }}" @selected(old('role', $currentRole) === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-xs text-rose-500" role="alert">{{ $message }}</p>@enderror
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Role disimpan di Keycloak dan akun MojoPedia yang terhubung.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-700/60">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow-md hover:shadow-blue-500/25 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.users.keycloak.password', $remote['id']) }}" class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700/60 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Ubah Kata Sandi</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Gunakan tombol Ubah Kata Sandi untuk memperbarui kata sandi di Keycloak dan akun MojoPedia yang terhubung.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label for="new-password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <input id="new-password" type="password" :type="showPassword ? 'text' : 'password'" name="password" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Minimal 8 karakter" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition pr-11">
                        <button type="button" @click="showPassword = !showPassword" :aria-pressed="showPassword" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus-visible:ring-2 focus-visible:ring-blue-500 rounded-r-lg cursor-pointer">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7C20.268 16.057 16.477 19 12 19s-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10 10 0 0112 5c4.5 0 8.3 2.9 9.5 7a10 10 0 01-3 4.4M6.2 6.2A10 10 0 002.5 12C3.7 16.1 7.5 19 12 19a10 10 0 004.2-.9" /></svg>
                        </button>
                    </div>
                    @error('password', 'passwordUpdate')<p class="mt-1 text-xs text-rose-500" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password-confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input id="password-confirmation" type="password" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition pr-11">
                        <button type="button" @click="showConfirmation = !showConfirmation" :aria-pressed="showConfirmation" :aria-label="showConfirmation ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus-visible:ring-2 focus-visible:ring-blue-500 rounded-r-lg cursor-pointer">
                            <svg x-show="!showConfirmation" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7C20.268 16.057 16.477 19 12 19s-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showConfirmation" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10 10 0 0112 5c4.5 0 8.3 2.9 9.5 7a10 10 0 01-3 4.4M6.2 6.2A10 10 0 002.5 12C3.7 16.1 7.5 19 12 19a10 10 0 004.2-.9" /></svg>
                        </button>
                    </div>
                    @error('password_confirmation', 'passwordUpdate')<p class="mt-1 text-xs text-rose-500" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex justify-end pt-1">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow-md hover:shadow-blue-500/25 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 cursor-pointer">Ubah Kata Sandi</button>
            </div>
        </form>
    </div>
</div>
@endsection

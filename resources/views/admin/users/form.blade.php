@extends('layouts.admin')
@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li>
        <a href="{{ route('admin.users.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-500 transition">Pengguna</a>
    </li>
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna' }}</li>
@endsection

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lengkapi form di bawah untuk {{ isset($user) ? 'mengubah' : 'menambahkan' }} data pengguna.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-md text-sm font-medium transition shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali
    </a>
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden p-6">
    <form action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm" required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="nama@email.com" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm" required>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm" {{ !isset($user) ? 'required' : '' }}>
                @if(isset($user))
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">Kosongkan jika tidak ingin mengubah password.</p>
                @endif
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label for="gender" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Jenis Kelamin</label>
                <select id="gender" name="gender" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 text-sm" required>
                    <option value="" disabled {{ old('gender', $user->jenis_kelamin ?? '') == '' ? 'selected' : '' }}>-- Pilih Jenis Kelamin --</option>
                    <option value="L" {{ old('gender', $user->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender', $user->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <!-- Instansi -->
            <div>
                <label for="instansi" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Instansi</label>
                <input type="text" id="instansi" name="instansi" value="{{ old('instansi', $user->instansi ?? '') }}" placeholder="Contoh: BRIN Pusat" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm">
            </div>

            <!-- Role / Hak Akses -->
            <div>
                <label for="role" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Role / Hak Akses</label>
                <select id="role" name="role" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 text-sm" required>
                    <option value="" disabled {{ old('role', $user->role ?? '') == '' ? 'selected' : '' }}>-- Pilih Hak Akses --</option>
                    <option value="Admin Pusat" {{ old('role', $user->role ?? '') == 'Admin Pusat' ? 'selected' : '' }}>Admin Pusat</option>
                    <option value="Admin IPPD" {{ old('role', $user->role ?? '') == 'Admin IPPD' ? 'selected' : '' }}>Admin IPPD</option>
                    <option value="Kreator Pengetahuan" {{ old('role', $user->role ?? '') == 'Kreator Pengetahuan' ? 'selected' : '' }}>Kreator Pengetahuan (Pengguna Umum)</option>
                    <option value="Analisis Pengetahuan" {{ old('role', $user->role ?? '') == 'Analisis Pengetahuan' ? 'selected' : '' }}>Analisis Pengetahuan</option>
                    <option value="Moderator" {{ old('role', $user->role ?? '') == 'Moderator' ? 'selected' : '' }}>Moderator</option>
                </select>
            </div>

            <!-- Foto Profil -->
            <div class="lg:col-span-2">
                <label for="foto_profil" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Foto Profil (Opsional)</label>
                
                @if(isset($user) && $user->foto_profil)
                    <div class="mb-3 flex items-center gap-4">
                        <img src="{{ Storage::url($user->foto_profil) }}" alt="Foto Profil Saat Ini" class="w-16 h-16 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Foto profil saat ini</span>
                    </div>
                @endif
                
                <input type="file" id="foto_profil" name="foto_profil" accept="image/*" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-red-600 focus:border-red-600 text-slate-800 dark:text-slate-100 transition bg-white dark:bg-slate-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 dark:file:bg-red-900/30 file:text-red-700 dark:file:text-red-400 hover:file:bg-red-100 dark:hover:file:bg-red-900/50">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">Format yang diizinkan: JPG, JPEG, PNG. Maksimal ukuran file: 2MB.</p>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-slate-200 dark:border-slate-700">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-md font-medium transition shadow-sm w-full sm:w-auto text-sm flex justify-center items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>
@endsection

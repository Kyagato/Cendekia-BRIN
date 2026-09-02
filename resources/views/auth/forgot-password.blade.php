<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password — {{ config('app.name', 'MojoPedia') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 relative">

    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        {{-- Split-Screen Card --}}
        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                {{-- ========== KOLOM KIRI: Branding & Ilustrasi ========== --}}
                <div class="hidden lg:flex flex-col justify-between bg-slate-50 p-10 relative overflow-hidden min-h-[600px]">
                    {{-- Dekoratif --}}
                    <div class="absolute -top-16 -left-16 w-64 h-64 bg-red-100 rounded-full opacity-40 blur-3xl"></div>
                    <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-red-50 rounded-full opacity-50 blur-3xl"></div>

                    {{-- Logo --}}
                    <div class="relative z-10">
                        <a href="/" class="inline-flex items-center gap-2 group">
                            <span class="text-2xl font-bold text-red-600 tracking-tight">MojoPedia</span>
                        </a>
                    </div>

                    {{-- Ilustrasi --}}
                    <div class="relative z-10 flex-1 flex items-center justify-center py-8">
                        <div class="flex flex-col items-center justify-center text-center gap-4">
                            <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center text-red-600 ring-8 ring-red-50/50 mb-2">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-slate-800 font-bold text-lg">Pemulihan Akun</h3>
                                <p class="text-slate-500 text-sm max-w-xs mt-1">Sistem akan mengirimkan kode autentikasi 4 digit untuk memverifikasi kepemilikan akun Anda.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tagline --}}
                    <div class="relative z-10">
                        <p class="text-sm text-slate-400">&copy; {{ date('Y') }} MojoPedia — Badan Riset dan Inovasi Nasional</p>
                    </div>
                </div>

                {{-- ========== KOLOM KANAN: Form Input Email ========== --}}
                <div class="flex flex-col p-8 sm:p-10 lg:p-12">
                    {{-- Header: Back to Login --}}
                    <div class="flex items-center justify-between mb-8">
                        {{-- Mobile logo --}}
                        <a href="/" class="lg:hidden inline-flex items-center gap-2">
                            <span class="text-xl font-bold text-red-600">MojoPedia</span>
                        </a>
                        <div class="flex items-center gap-3 ml-auto">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-red-600 font-medium transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                Kembali ke Login
                            </a>
                        </div>
                    </div>

                    {{-- Step Indicator --}}
                    <div class="flex items-center gap-2 mb-6">
                        <span class="w-8 h-8 rounded-full bg-red-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">1</span>
                        <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Langkah 1 dari 3: Email Akun</span>
                    </div>

                    {{-- Title --}}
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Lupa Password?</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Masukkan alamat email akun Anda. Kami akan mengirimkan kode autentikasi 4 digit untuk mengatur ulang password.
                        </p>
                    </div>

                    {{-- Form --}}
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Alamat Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                   placeholder="nama@brin.go.id">
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-600 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="w-full py-3.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-full shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wider text-sm flex justify-center items-center gap-2">
                            <span>Kirim Kode Autentikasi</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </form>

                    {{-- Footer --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <p class="text-xs text-slate-400">&copy; {{ date('Y') }} MojoPedia — Badan Riset dan Inovasi Nasional</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>

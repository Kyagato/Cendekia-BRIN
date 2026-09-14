<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Kata Sandi — {{ config('app.name', 'MojoPedia') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#f8fafc] text-[#0f172a] relative">

    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        {{-- Split-Screen Card --}}
        <div class="w-full max-w-5xl bg-white rounded-xl shadow-xl overflow-hidden border border-[#e2e8f0]">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                {{-- ========== KOLOM KIRI: Branding & Ilustrasi ========== --}}
                <div class="hidden lg:flex flex-col justify-between bg-[#1e3a8a] p-10 relative overflow-hidden min-h-[600px] text-white">

                    {{-- Logo --}}
                    <div class="relative z-10">
                        <a href="/" class="inline-flex items-center gap-2 group">
                            <span class="text-2xl font-extrabold text-white tracking-tight">Mojo<span class="text-[#93c5fd]">Pedia</span></span>
                        </a>
                    </div>

                    {{-- Ilustrasi --}}
                    <div class="relative z-10 flex-1 flex items-center justify-center py-8">
                        <div class="flex flex-col items-center justify-center text-center gap-4">
                            <div class="w-20 h-20 bg-white/10 text-white rounded-2xl flex items-center justify-center ring-4 ring-white/10 mb-2 backdrop-blur-sm">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Pemulihan Akun</h3>
                                <p class="text-blue-100 text-xs max-w-xs mt-1 leading-relaxed">Sistem akan mengirimkan kode autentikasi verifikasi kepemilikan akun ke alamat email terdaftar Anda.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tagline --}}
                    <div class="relative z-10">
                        <p class="text-xs text-blue-200">&copy; {{ date('Y') }} MojoPedia — Digital Governance System</p>
                    </div>
                </div>

                {{-- ========== KOLOM KANAN: Form Input Email ========== --}}
                <div class="flex flex-col p-8 sm:p-10 lg:p-12 justify-between">
                    <div>
                        {{-- Header: Back to Login --}}
                        <div class="flex items-center justify-between mb-8">
                            {{-- Mobile logo --}}
                            <a href="/" class="lg:hidden inline-flex items-center gap-2">
                                <span class="text-xl font-bold text-[#2563eb]">MojoPedia</span>
                            </a>
                            <div class="flex items-center gap-3 ml-auto">
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs text-[#475569] hover:text-[#2563eb] font-semibold transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                    Kembali ke Login
                                </a>
                            </div>
                        </div>

                        {{-- Step Indicator --}}
                        <div class="flex items-center gap-2 mb-6">
                            <span class="w-7 h-7 rounded-full bg-[#2563eb] text-white font-bold text-xs flex items-center justify-center shadow-sm">1</span>
                            <span class="text-xs font-semibold text-[#475569] uppercase tracking-wider">Langkah 1 dari 3: Verifikasi Email</span>
                        </div>

                        {{-- Title --}}
                        <div class="mb-6">
                            <h1 class="text-2xl sm:text-3xl font-bold text-[#0f172a] mb-2 tracking-tight">Lupa Kata Sandi?</h1>
                            <p class="text-[#475569] text-xs sm:text-sm leading-relaxed">
                                Masukkan alamat email akun Anda. Kami akan mengirimkan tautan / kode autentikasi untuk mengatur ulang kata sandi.
                            </p>
                        </div>

                        {{-- Form --}}
                        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-xs font-semibold text-[#0f172a] mb-1.5">Alamat Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="w-full px-3.5 py-2.5 bg-white border border-[#cbd5e1] rounded-lg text-[#0f172a] placeholder-[#94a3b8] focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb] text-sm transition"
                                       placeholder="nama@gmail.com">
                                @error('email')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit"
                                    class="w-full py-3 bg-[#2563eb] hover:bg-[#1d4ed8] active:bg-[#1e40af] text-white font-semibold rounded-lg shadow-sm transition text-xs uppercase tracking-wider flex justify-center items-center gap-2">
                                <span>Kirim Kode Autentikasi</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-8 pt-6 border-t border-[#f1f5f9] text-center">
                        <p class="text-xs text-[#94a3b8]">&copy; {{ date('Y') }} MojoPedia — Digital Governance System</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>

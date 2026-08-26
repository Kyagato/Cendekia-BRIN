<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buat Password Baru — {{ config('app.name', 'Cendekia BRIN') }}</title>
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
                            <span class="text-2xl font-bold text-red-600 tracking-tight">Cendekia</span>
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded">BRIN</span>
                        </a>
                    </div>

                    {{-- Ilustrasi --}}
                    <div class="relative z-10 flex-1 flex items-center justify-center py-8">
                        <div class="flex flex-col items-center justify-center text-center gap-4">
                            <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center text-red-600 ring-8 ring-red-50/50 mb-2">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-slate-800 font-bold text-lg">Keamanan Password</h3>
                                <p class="text-slate-500 text-sm max-w-xs mt-1">Buat password baru yang kuat dengan kombinasi huruf, angka, dan minimal 8 karakter.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tagline --}}
                    <div class="relative z-10">
                        <p class="text-sm text-slate-400">&copy; {{ date('Y') }} Cendekia BRIN — Badan Riset dan Inovasi Nasional</p>
                    </div>
                </div>

                {{-- ========== KOLOM KANAN: Form Password Baru ========== --}}
                <div class="flex flex-col p-8 sm:p-10 lg:p-12">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-8">
                        <a href="/" class="lg:hidden inline-flex items-center gap-2">
                            <span class="text-xl font-bold text-red-600">Cendekia</span>
                            <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[0.6rem] font-semibold rounded">BRIN</span>
                        </a>
                        <div class="flex items-center gap-3 ml-auto">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-red-600 font-medium transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                Batal
                            </a>
                        </div>
                    </div>

                    {{-- Step Indicator --}}
                    <div class="flex items-center gap-2 mb-6">
                        <span class="w-8 h-8 rounded-full bg-red-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">3</span>
                        <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Langkah 3 dari 3: Password Baru</span>
                    </div>

                    {{-- Title --}}
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Buat Password Baru</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Kode autentikasi berhasil diverifikasi. Silakan masukkan password baru untuk akun <strong class="text-slate-800">{{ session('reset_email') }}</strong>.
                        </p>
                    </div>

                    {{-- Banner Sukses OTP --}}
                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-emerald-800 font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('password.otp.update') }}" class="space-y-5">
                        @csrf

                        {{-- Password Baru --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-800 mb-1.5">Password Baru</label>
                            <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                   placeholder="Masukkan password baru (min 8 karakter)">
                            @error('password')
                                <p class="mt-1.5 text-sm text-red-600 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-800 mb-1.5">Konfirmasi Password Baru</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                   placeholder="Ulangi masukan password baru">
                            @error('password_confirmation')
                                <p class="mt-1.5 text-sm text-red-600 font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="w-full py-3.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-full shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wider text-sm flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Simpan Password Baru & Login</span>
                        </button>
                    </form>

                    {{-- Footer --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <p class="text-xs text-slate-400">&copy; {{ date('Y') }} Cendekia BRIN — Badan Riset dan Inovasi Nasional</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Kode Autentikasi — {{ config('app.name', 'MojoPedia') }}</title>
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
                            <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-600 ring-8 ring-emerald-50/50 mb-2">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-6-7.036A11.959 11.959 0 0112 2.25c2.476 0 4.797.755 6.72 2.047a1.96 1.96 0 01.78 1.643l-.32 8.358a1.96 1.96 0 01-1.042 1.581l-5.638 3.132a1.96 1.96 0 01-1.92 0l-5.638-3.132A1.96 1.96 0 013.9 14.3l-.32-8.358a1.96 1.96 0 01.78-1.643z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-slate-800 font-bold text-lg">Verifikasi Autentikasi</h3>
                                <p class="text-slate-500 text-sm max-w-xs mt-1">Masukkan 4 digit kode angka yang telah kami kirimkan ke email terdaftar Anda.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tagline --}}
                    <div class="relative z-10">
                        <p class="text-sm text-slate-400">&copy; {{ date('Y') }} MojoPedia — Badan Riset dan Inovasi Nasional</p>
                    </div>
                </div>

                {{-- ========== KOLOM KANAN: Form Input Kode OTP ========== --}}
                <div class="flex flex-col p-8 sm:p-10 lg:p-12">
                    {{-- Header: Back to Email Step --}}
                    <div class="flex items-center justify-between mb-8">
                        <a href="/" class="lg:hidden inline-flex items-center gap-2">
                            <span class="text-xl font-bold text-red-600">MojoPedia</span>
                        </a>
                        <div class="flex items-center gap-3 ml-auto">
                            <a href="{{ route('password.request') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-red-600 font-medium transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                Ubah Email
                            </a>
                        </div>
                    </div>

                    {{-- Step Indicator --}}
                    <div class="flex items-center gap-2 mb-6">
                        <span class="w-8 h-8 rounded-full bg-red-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">2</span>
                        <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Langkah 2 dari 3: Verifikasi Kode</span>
                    </div>

                    {{-- Title --}}
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Kode Autentikasi</h1>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Masukkan 4 digit kode autentikasi yang telah dikirim ke <strong class="text-slate-800">{{ session('reset_email') }}</strong>.
                        </p>
                    </div>

                    {{-- Notification Banner --}}
                    @if(session('success_otp'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-bold text-emerald-900">Kode Dikirim!</p>
                                <p class="text-emerald-700 mt-0.5">{{ session('success_otp') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-6">
                        @csrf

                        {{-- OTP Input Box --}}
                        <div>
                            <label for="otp" class="block text-sm font-semibold text-slate-800 mb-2 text-center">Masukkan 4 Digit Kode OTP</label>
                            <div class="max-w-xs mx-auto">
                                <input id="otp" type="text" name="otp" maxlength="4" required autofocus autocomplete="off"
                                       class="w-full px-4 py-3.5 border-2 border-slate-300 rounded-xl text-slate-800 placeholder-slate-300 focus:border-red-600 focus:ring-red-600 text-center font-mono text-3xl font-bold tracking-[0.6em] transition duration-200"
                                       placeholder="••••">
                            </div>
                            @error('otp')
                                <p class="mt-2 text-sm text-red-600 font-medium text-center flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="w-full py-3.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-full shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wider text-sm flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Verifikasi Kode & Lanjut</span>
                        </button>
                    </form>

                    {{-- Resend Option --}}
                    <div class="mt-6 text-center">
                        <form method="POST" action="{{ route('password.email') }}" class="inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('reset_email') }}">
                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold hover:underline bg-transparent border-none p-0 cursor-pointer">
                                Tidak menerima kode? Kirim Ulang Kode OTP
                            </button>
                        </form>
                    </div>

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

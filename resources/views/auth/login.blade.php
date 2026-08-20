<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ showSuccessModal: {{ session('registered_success') || session('success') ? 'true' : 'false' }} }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — {{ config('app.name', 'Cendekia BRIN') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 relative">

    {{-- ========== POPUP MODAL KONFIRMASI REGISTRASI SUKSES ========== --}}
    @if(session('registered_success') || session('success'))
    <div x-show="showSuccessModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8 text-center border border-slate-100 relative">
            {{-- Icon Centang Sukses --}}
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 ring-8 ring-emerald-50">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 class="text-2xl font-bold text-slate-800 mb-2">Akun Berhasil Dibuat!</h3>
            <p class="text-sm text-slate-600 mb-4">
                Pendaftaran akun Cendekia BRIN Anda telah sukses. Silakan lakukan login ulang untuk masuk ke website.
            </p>

            @if(session('registered_email'))
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 mb-6">
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Email Terdaftar</p>
                    <p class="text-sm font-bold text-slate-800 break-all mt-0.5">{{ session('registered_email') }}</p>
                </div>
            @endif

            <div class="flex flex-col gap-2">
                <button @click="showSuccessModal = false; document.getElementById('email').focus()"
                        type="button"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-xl shadow-md transition-all duration-200 uppercase tracking-wider text-sm flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Konfirmasi & Lanjut Login
                </button>
            </div>
        </div>
    </div>
    @endif

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
                        <img src="https://undraw.co/api/illustrations/random?search=knowledge&color=dc2626" 
                             alt="Ilustrasi Knowledge Management" 
                             class="w-full max-w-sm opacity-90"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        {{-- Fallback SVG jika gambar gagal --}}
                        <div class="hidden flex-col items-center justify-center text-center gap-4">
                            <svg class="w-48 h-48 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <div>
                                <p class="text-slate-500 font-medium">Sistem Informasi</p>
                                <p class="text-slate-400 text-sm">Manajemen Pengetahuan BRIN</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tagline --}}
                    <div class="relative z-10">
                        <p class="text-sm text-slate-400">Platform terpadu untuk mengelola dan berbagi pengetahuan di lingkungan BRIN.</p>
                    </div>
                </div>

                {{-- ========== KOLOM KANAN: Form Login ========== --}}
                <div class="flex flex-col p-8 sm:p-10 lg:p-12">
                    {{-- Header: Toggle to Register --}}
                    <div class="flex items-center justify-between mb-8">
                        {{-- Mobile logo --}}
                        <a href="/" class="lg:hidden inline-flex items-center gap-2">
                            <span class="text-xl font-bold text-red-600">Cendekia</span>
                            <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[0.6rem] font-semibold rounded">BRIN</span>
                        </a>
                        <div class="flex items-center gap-3 ml-auto">
                            <span class="text-sm text-slate-400 hidden sm:inline">Belum punya akun?</span>
                            <a href="{{ route('register') }}" class="px-4 py-1.5 border-2 border-red-600 text-red-600 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-red-600 hover:text-white transition-all duration-200">
                                Daftar
                            </a>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Selamat Datang!</h1>
                        <p class="text-slate-400">Masuk ke akun Cendekia BRIN Anda</p>
                    </div>

                    {{-- Banner Notifikasi Sukses --}}
                    @if(session('success'))
                        <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-bold text-emerald-900">Akun Berhasil Dibuat!</p>
                                <p class="text-emerald-700 mt-0.5">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Session Status --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    {{-- Form --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', session('registered_email')) }}" required autofocus autocomplete="username"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                   placeholder="nama@brin.go.id">
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-800 mb-1.5">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                   placeholder="Masukkan password">
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" name="remember"
                                       class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                <span class="ms-2 text-sm text-slate-500">Ingat saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-red-600 hover:text-red-700 font-medium hover:underline">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="w-full py-3.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-full shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wider text-sm">
                            Masuk
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

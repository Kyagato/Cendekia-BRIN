<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar — {{ config('app.name', 'Cendekia BRIN') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50">

    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        {{-- Split-Screen Card --}}
        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">

                {{-- ========== KOLOM KIRI: Branding & Ilustrasi ========== --}}
                <div class="hidden lg:flex flex-col justify-between bg-slate-50 p-10 relative overflow-hidden min-h-[700px]">
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
                        <img src="https://undraw.co/api/illustrations/random?search=register&color=dc2626" 
                             alt="Ilustrasi Pendaftaran Akun" 
                             class="w-full max-w-sm opacity-90"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        {{-- Fallback SVG jika gambar gagal --}}
                        <div class="hidden flex-col items-center justify-center text-center gap-4">
                            <svg class="w-48 h-48 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <div>
                                <p class="text-slate-500 font-medium">Bergabunglah Bersama Kami</p>
                                <p class="text-slate-400 text-sm">Daftarkan akun Cendekia BRIN Anda</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tagline --}}
                    <div class="relative z-10">
                        <p class="text-sm text-slate-400">Bergabunglah untuk mengelola dan berbagi pengetahuan di lingkungan BRIN.</p>
                    </div>
                </div>

                {{-- ========== KOLOM KANAN: Form Register ========== --}}
                <div class="flex flex-col p-8 sm:p-10 lg:p-12">
                    {{-- Header: Toggle to Login --}}
                    <div class="flex items-center justify-between mb-8">
                        {{-- Mobile logo --}}
                        <a href="/" class="lg:hidden inline-flex items-center gap-2">
                            <span class="text-xl font-bold text-red-600">Cendekia</span>
                            <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[0.6rem] font-semibold rounded">BRIN</span>
                        </a>
                        <div class="flex items-center gap-3 ml-auto">
                            <span class="text-sm text-slate-400 hidden sm:inline">Sudah punya akun?</span>
                            <a href="{{ route('login') }}" class="px-4 py-1.5 border-2 border-red-600 text-red-600 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-red-600 hover:text-white transition-all duration-200">
                                Masuk
                            </a>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Buat Akun Baru</h1>
                        <p class="text-slate-400">Daftarkan diri Anda di Cendekia BRIN</p>
                    </div>

                    {{-- Form --}}
                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-800 mb-1.5">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                   placeholder="Masukkan nama lengkap">
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-800 mb-1.5">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                   placeholder="nama@brin.go.id">
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        {{-- Password & Confirm Password (Grid) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-800 mb-1.5">Password</label>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                       placeholder="Min. 8 karakter">
                                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-slate-800 mb-1.5">Konfirmasi Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                       placeholder="Ulangi password">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Jenis Kelamin & Instansi (Grid) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="jenis_kelamin" class="block text-sm font-semibold text-slate-800 mb-1.5">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200 bg-white">
                                    <option value="" disabled selected class="text-slate-400">Pilih</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="instansi" class="block text-sm font-semibold text-slate-800 mb-1.5">Instansi</label>
                                <input id="instansi" type="text" name="instansi" value="{{ old('instansi') }}" required autocomplete="organization"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg text-slate-800 placeholder-slate-400 focus:border-red-600 focus:ring-red-600 focus:ring-1 transition duration-200"
                                       placeholder="Nama instansi">
                                <x-input-error :messages="$errors->get('instansi')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full py-3.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-full shadow-lg shadow-red-600/30 hover:shadow-xl hover:shadow-red-600/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wider text-sm">
                                Daftar Akun
                            </button>
                        </div>
                    </form>

                    {{-- Footer --}}
                    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                        <p class="text-xs text-slate-400">&copy; {{ date('Y') }} Cendekia BRIN — Badan Riset dan Inovasi Nasional</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>

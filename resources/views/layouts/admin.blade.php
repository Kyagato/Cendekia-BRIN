<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin - Cendekia BRIN')</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Top Navbar (Sticky) -->
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-50 sticky top-0 shadow-sm">
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 group shrink-0">
                    <span class="text-xl font-bold tracking-tight text-slate-800">Cendekia</span>
                    <span class="px-2 py-0.5 bg-primary-600 text-white text-xs font-bold rounded shadow-sm">BRIN</span>
                </a>
                
                <!-- Navigation Menu (Desktop) -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('knowledge.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('knowledge.*') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        Pengetahuan
                    </a>
                    <a href="#" class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
                        Label
                    </a>
                    <a href="#" class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
                        Kategori
                    </a>
                    <a href="#" class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">
                        Komentar
                    </a>
                </nav>
            </div>
            
            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Mobile menu toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-500 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Profile Dropdown (Sleek Compact Dropdown) -->
                <div class="relative py-2" x-data="{ userMenuOpen: false }" @mouseenter="userMenuOpen = true" @mouseleave="userMenuOpen = false">
                    <button class="flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-primary-600 transition focus:outline-none">
                        <span class="max-w-[130px] truncate font-semibold hidden sm:inline">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-bold text-xs overflow-hidden shrink-0 ring-2 ring-primary-500/20">
                            @if(Auth::user()->foto_profil)
                                <img src="{{ Storage::url(Auth::user()->foto_profil) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50 overflow-hidden" 
                         style="display: none;">
                        
                        <a href="{{ url('/') }}" class="flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            Beranda Utama
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition text-left">
                                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden bg-white border-b border-slate-200 absolute top-16 w-full left-0 z-40 px-4 py-3 shadow-md space-y-1"
         style="display: none;">
        <a href="{{ route('knowledge.index') }}" class="block px-3 py-2 rounded-lg text-base font-semibold transition {{ request()->routeIs('knowledge.*') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">Pengetahuan</a>
        <a href="#" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">Label</a>
        <a href="#" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">Kategori</a>
        <a href="#" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition">Komentar</a>
    </div>

    <!-- Main Page Content wrapper (Fluid and Centered) -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Main Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50">
            <div class="max-w-7xl mx-auto w-full">
                <!-- Breadcrumbs -->
                <nav class="flex text-sm text-slate-500 font-medium mb-6">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ url('/dashboard') }}" class="flex items-center hover:text-primary-600 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                Dashboard
                            </a>
                        </li>
                        @hasSection('title')
                        <li>
                            <svg class="w-4 h-4 text-slate-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </li>
                        <li class="text-slate-800 font-semibold">@yield('title')</li>
                        @endif
                    </ol>
                </nav>

                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>

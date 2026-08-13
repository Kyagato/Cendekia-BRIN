<nav class="fixed top-0 w-full z-50 glass border-b border-slate-200 dark:border-slate-800" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Left side: Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="flex items-center gap-2 group">
                    <span class="text-2xl font-bold tracking-tight gradient-text group-hover:opacity-80 transition">Cendekia</span>
                    <span class="px-2 py-1 bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 text-xs font-semibold rounded shadow-sm">BRIN</span>
                </a>
            </div>

            <!-- Center: Desktop Navigation -->
            <div class="hidden md:flex space-x-8">
                <a href="/" class="inline-flex items-center px-1 pt-1 text-sm font-medium transition {{ request()->is('/') ? 'text-primary-600 border-b-2 border-primary-600 dark:text-primary-400 dark:border-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400' }}">Beranda</a>
                <a href="/tentang" class="inline-flex items-center px-1 pt-1 text-sm font-medium transition {{ request()->is('tentang') ? 'text-primary-600 border-b-2 border-primary-600 dark:text-primary-400 dark:border-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400' }}">Tentang</a>
                
                @auth
                    <a href="/kategori" class="inline-flex items-center px-1 pt-1 text-sm font-medium transition {{ request()->is('kategori') ? 'text-primary-600 border-b-2 border-primary-600 dark:text-primary-400 dark:border-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400' }}">Kategori</a>
                @else
                    <div class="relative group flex items-center">
                        <span class="inline-flex items-center px-1 pt-1 text-sm font-medium text-slate-400 dark:text-slate-500 cursor-not-allowed">Kategori</span>
                        <div class="absolute top-full mt-2 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-xs rounded py-1 px-2 pointer-events-none whitespace-nowrap z-10">
                            Login untuk mengakses
                        </div>
                    </div>
                @endauth
                
                <a href="/forum" class="inline-flex items-center px-1 pt-1 text-sm font-medium transition {{ request()->is('forum') ? 'text-primary-600 border-b-2 border-primary-600 dark:text-primary-400 dark:border-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400' }}">Forum</a>
                <a href="/faq" class="inline-flex items-center px-1 pt-1 text-sm font-medium transition {{ request()->is('faq') ? 'text-primary-600 border-b-2 border-primary-600 dark:text-primary-400 dark:border-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400' }}">FAQs</a>
            </div>

            <!-- Right side: Actions -->
            <div class="hidden md:flex items-center space-x-4">
                @include('components.dark-mode-toggle')
                
                @auth
                    <div class="relative py-2" x-data="{ userMenuOpen: false }" @mouseenter="userMenuOpen = true" @mouseleave="userMenuOpen = false">
                        <button class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:text-primary-600 dark:hover:text-primary-400 transition focus:outline-none">
                            <span class="hidden">{{ auth()->user()->name }}</span>
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-bold text-xs overflow-hidden shrink-0 ring-2 ring-primary-500/20">
                                @if(auth()->user()->foto_profil)
                                    <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover rounded-full">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                        </button>

                        <!-- Dropdown Menu Seamless -->
                        <div x-show="userMenuOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 py-1 z-50 overflow-hidden" 
                             style="display: none;">
                            
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                                <p class="text-xs font-semibold text-primary-600 dark:text-primary-400 mt-1">{{ auth()->user()->role ?? 'User' }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Pengaturan Profil
                            </a>

                            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition">
                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                Dashboard
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left">
                                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition">Daftar</a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden space-x-2">
                @include('components.dark-mode-toggle')
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-white transition focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden glass border-b border-slate-200 dark:border-slate-800 absolute w-full" style="display: none;">
        <div class="pt-2 pb-3 space-y-1">
            <a href="/" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition {{ request()->is('/') ? 'border-primary-600 text-primary-700 bg-primary-50 dark:bg-primary-900/50 dark:text-primary-300' : 'border-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Beranda</a>
            <a href="/tentang" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition {{ request()->is('tentang') ? 'border-primary-600 text-primary-700 bg-primary-50 dark:bg-primary-900/50 dark:text-primary-300' : 'border-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Tentang</a>
            @auth
                <a href="/kategori" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition {{ request()->is('kategori') ? 'border-primary-600 text-primary-700 bg-primary-50 dark:bg-primary-900/50 dark:text-primary-300' : 'border-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Kategori</a>
            @else
                <div class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-slate-400 dark:text-slate-500">Kategori (Login untuk mengakses)</div>
            @endauth
            <a href="/forum" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition {{ request()->is('forum') ? 'border-primary-600 text-primary-700 bg-primary-50 dark:bg-primary-900/50 dark:text-primary-300' : 'border-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Forum</a>
            <a href="/faq" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition {{ request()->is('faq') ? 'border-primary-600 text-primary-700 bg-primary-50 dark:bg-primary-900/50 dark:text-primary-300' : 'border-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">FAQs</a>
        </div>
        <div class="pt-4 pb-3 border-t border-slate-200 dark:border-slate-700">
            @auth
                <div class="flex items-center px-4">
                    <div class="ml-3">
                        <div class="text-base font-medium text-slate-800 dark:text-slate-200">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">Pengaturan Profil</a>
                    <a href="{{ url('/dashboard') }}" class="block px-4 py-2 text-base font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-base font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">Keluar</button>
                    </form>
                </div>
            @else
                <div class="flex px-4 space-x-2">
                    <a href="{{ route('login') }}" class="block text-center flex-1 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="block text-center flex-1 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 transition">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</nav>

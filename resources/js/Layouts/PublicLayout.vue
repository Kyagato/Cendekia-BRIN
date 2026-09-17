<template>
  <div class="min-h-screen bg-[#f8fafc] dark:bg-slate-950 text-[#0f172a] dark:text-slate-100 flex flex-col font-sans transition-colors duration-200">
    <!-- Navbar -->
    <header class="border-b border-[#e2e8f0] dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo & Institutional Identity -->
        <div class="flex-shrink-0 flex items-center">
          <Link href="/" class="flex items-center gap-3 group">
            <!-- Institutional Emblem / Icon -->
            <div class="w-10 h-10 flex items-center justify-center group-hover:scale-105 transition-transform">
              <img src="/images/logo-mojopedia.png" alt="Logo MojoPedia" class="w-full h-full object-contain drop-shadow-sm" />
            </div>
            <div class="flex flex-col">
              <span class="text-xl font-extrabold tracking-tight text-[#0f172a] dark:text-white leading-tight">
                Mojo<span class="text-[#2563eb]">Pedia</span>
              </span>
              <span class="text-[10px] font-semibold tracking-wider text-[#475569] dark:text-slate-400 uppercase">
                Digital Mojokerto System
              </span>
            </div>
          </Link>
        </div>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center space-x-1 text-sm font-semibold">
          <Link 
            href="/" 
            class="px-3.5 py-2 rounded-lg transition text-[#475569] dark:text-slate-200 hover:text-[#2563eb] dark:hover:text-blue-400 hover:bg-[#eff6ff] dark:hover:bg-slate-800"
          >
            Beranda
          </Link>
          <Link 
            href="/tentang" 
            class="px-3.5 py-2 rounded-lg transition text-[#475569] dark:text-slate-200 hover:text-[#2563eb] dark:hover:text-blue-400 hover:bg-[#eff6ff] dark:hover:bg-slate-800"
          >
            Tentang
          </Link>
          <Link 
            href="/kategori" 
            class="px-3.5 py-2 rounded-lg transition text-[#475569] dark:text-slate-200 hover:text-[#2563eb] dark:hover:text-blue-400 hover:bg-[#eff6ff] dark:hover:bg-slate-800"
          >
            Kategori
          </Link>
          <Link 
            href="/forum" 
            class="px-3.5 py-2 rounded-lg transition text-[#475569] dark:text-slate-200 hover:text-[#2563eb] dark:hover:text-blue-400 hover:bg-[#eff6ff] dark:hover:bg-slate-800"
          >
            Forum
          </Link>
          <Link 
            href="/faq" 
            class="px-3.5 py-2 rounded-lg transition text-[#475569] dark:text-slate-200 hover:text-[#2563eb] dark:hover:text-blue-400 hover:bg-[#eff6ff] dark:hover:bg-slate-800"
          >
            Panduan & FAQs
          </Link>
        </nav>

        <!-- Right Side Actions: Dark Mode Toggle & User Auth -->
        <div class="flex items-center space-x-3">
          <!-- Dark / Light Mode Toggle Button -->
          <button 
            @click="toggleDarkMode" 
            type="button"
            class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition"
            :title="isDark ? 'Beralih ke Light Mode' : 'Beralih ke Dark Mode'"
          >
            <!-- Sun Icon -->
            <svg v-if="isDark" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 01-8 0z" />
            </svg>
            <!-- Moon Icon -->
            <svg v-else class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </button>

          <!-- Logged In User Dropdown -->
          <div v-if="user" class="relative" ref="dropdownRef">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1 pl-2 pr-3 rounded-lg border border-[#e2e8f0] dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition focus:outline-none cursor-pointer">
              <div class="w-7 h-7 rounded-md bg-[#2563eb] text-white flex items-center justify-center font-bold text-xs overflow-hidden shrink-0">
                <img v-if="user.foto_profil" :src="`/storage/${user.foto_profil}`" :alt="user.name" class="w-full h-full object-cover">
                <span v-else>{{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
              </div>
              <span class="hidden sm:inline font-semibold text-xs text-[#0f172a] dark:text-slate-200">{{ user.name }}</span>
              <svg 
                class="w-3.5 h-3.5 text-slate-400 transition-transform duration-300 shrink-0" 
                :class="{ 'rotate-180 text-[#2563eb]': userMenuOpen }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-[#e2e8f0] dark:border-slate-700 py-1.5 z-50 text-left">
              <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700">
                <p class="text-xs font-bold text-[#0f172a] dark:text-white truncate">{{ user.name }}</p>
                <p class="text-[11px] text-[#475569] dark:text-slate-400 truncate">{{ user.email }}</p>
                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 capitalize">{{ user.role || 'Pengguna' }}</span>
              </div>

              <a href="/dashboard" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-[#eff6ff] hover:text-[#2563eb] dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Dashboard
              </a>

              <a href="/profile" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-[#eff6ff] hover:text-[#2563eb] dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Pengaturan Profil
              </a>

              <button @click="logout" class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-left">
                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Keluar
              </button>
            </div>
          </div>

          <!-- Guest Action Buttons (Shown when not logged in) -->
          <div v-else class="flex items-center space-x-2">
            <Link href="/login" class="text-xs font-semibold px-3 py-2 rounded-lg text-[#475569] dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
              Masuk
            </Link>
            <Link href="/register" class="inline-flex items-center justify-center px-4 py-2 rounded-lg shadow-sm text-xs font-semibold text-white bg-[#2563eb] hover:bg-[#1d4ed8] active:bg-[#1e40af] transition">
              Daftar
            </Link>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Body -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <div class="bg-slate-50 dark:bg-slate-900 py-16 relative overflow-hidden font-sans transition-colors duration-300">
      
      <!-- Background Watermark Text -->
      <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none flex justify-center pointer-events-none opacity-[0.03] dark:opacity-[0.02] select-none" style="font-size: 14vw; font-weight: 900; color: #0f172a; transform: translateY(30%);">
          MOJOKERTO
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <!-- Floating Card -->
          <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-slate-100 dark:border-slate-700 p-8 sm:p-12 lg:p-14 transition-colors duration-300">
              
              <!-- Grid Layout -->
              <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">
                  
                  <!-- Column 1: Brand -->
                  <div class="lg:col-span-5">
                      <div class="flex items-start gap-5">
                          <!-- Logo -->
                          <div class="w-20 h-20 rounded-2xl flex items-center justify-center flex-shrink-0">
                              <img src="/images/logo-kominfo.png" alt="Logo Kabupaten Mojokerto" class="w-full h-full object-contain drop-shadow-sm" />
                          </div>
                          <div class="mt-1">
                              <h4 class="text-[10px] font-bold text-blue-600 dark:text-blue-400 tracking-[0.2em] uppercase mb-1.5 transition-colors">Website Resmi</h4>
                              <h2 class="text-[26px] font-serif font-bold text-slate-900 dark:text-white leading-tight transition-colors">
                                  Dinas komunikasi dan<br>informatika
                              </h2>
                              <p class="text-[13px] italic text-slate-500 dark:text-slate-400 mt-2 font-serif transition-colors">Kabupaten Mojokerto</p>
                          </div>
                      </div>
                      
                      <p class="text-sm text-slate-600 dark:text-slate-300 mt-8 max-w-sm transition-colors">
                          Dinas komunikasi dan informatika website diskominfo
                      </p>
                      
                      <!-- Socials -->
                      <div class="flex items-center gap-3 mt-6">
                          <a href="#" class="w-10 h-10 rounded-full border border-blue-100 dark:border-slate-600 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                          </a>
                          <a href="#" class="w-10 h-10 rounded-full border border-blue-100 dark:border-slate-600 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                          </a>
                      </div>
                  </div>

                  <!-- Column 2: Navigasi -->
                  <div class="lg:col-span-3">
                      <h4 class="text-[10px] font-bold text-blue-600 dark:text-blue-400 tracking-[0.2em] uppercase mb-6 transition-colors">Navigasi</h4>
                      <ul class="space-y-4">
                          <li>
                              <Link href="/" class="flex items-center group">
                                  <span class="w-1 h-1 rounded-full bg-blue-400 dark:bg-blue-500 mr-3 group-hover:bg-blue-600 dark:group-hover:bg-blue-300 transition-colors"></span>
                                  <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Beranda</span>
                              </Link>
                          </li>
                          <li>
                              <Link href="/tentang" class="flex items-center group">
                                  <span class="w-1 h-1 rounded-full bg-blue-400 dark:bg-blue-500 mr-3 group-hover:bg-blue-600 dark:group-hover:bg-blue-300 transition-colors"></span>
                                  <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Tentang</span>
                              </Link>
                          </li>
                          <li>
                              <Link href="/kategori" class="flex items-center group">
                                  <span class="w-1 h-1 rounded-full bg-blue-400 dark:bg-blue-500 mr-3 group-hover:bg-blue-600 dark:group-hover:bg-blue-300 transition-colors"></span>
                                  <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Kategori</span>
                              </Link>
                          </li>
                          <li>
                              <Link href="/forum" class="flex items-center group">
                                  <span class="w-1 h-1 rounded-full bg-blue-400 dark:bg-blue-500 mr-3 group-hover:bg-blue-600 dark:group-hover:bg-blue-300 transition-colors"></span>
                                  <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Forum</span>
                              </Link>
                          </li>
                          <li>
                              <Link href="/faq" class="flex items-center group">
                                  <span class="w-1 h-1 rounded-full bg-blue-400 dark:bg-blue-500 mr-3 group-hover:bg-blue-600 dark:group-hover:bg-blue-300 transition-colors"></span>
                                  <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">FAQs</span>
                              </Link>
                          </li>
                      </ul>
                  </div>

                  <!-- Column 3: Hubungi Kami -->
                  <div class="lg:col-span-4">
                      <h4 class="text-[10px] font-bold text-blue-600 dark:text-blue-400 tracking-[0.2em] uppercase mb-6 transition-colors">Hubungi Kami</h4>
                      <div class="space-y-6">
                          
                          <!-- Address -->
                          <div class="flex items-start gap-4">
                              <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0 transition-colors">
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                              </div>
                              <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed pt-1 transition-colors">
                                  Jl. RA. Basuni Nomor 14, Jampirogo, Kecamatan Sooko, Kabupaten Mojokerto, Kode Pos 61361, Jawa Timur.
                              </div>
                          </div>

                          <!-- Phone -->
                          <div class="flex items-start gap-4">
                              <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0 transition-colors">
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                              </div>
                              <div class="pt-0.5">
                                  <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1 transition-colors">Telepon</span>
                                  <div class="flex items-center gap-2 group cursor-pointer" @click="copyToClipboard('(0321) 391268')">
                                      <span class="text-sm text-slate-600 dark:text-slate-300 transition-colors">(0321) 391268</span>
                                      <svg class="w-3.5 h-3.5 text-slate-300 dark:text-slate-500 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                  </div>
                              </div>
                          </div>

                          <!-- Email -->
                          <div class="flex items-start gap-4">
                              <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0 transition-colors">
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                              </div>
                              <div class="pt-0.5">
                                  <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1 transition-colors">Email</span>
                                  <div class="flex items-center gap-2 group cursor-pointer" @click="copyToClipboard('diskominfo@mojokertokab.go.id')">
                                      <span class="text-sm text-slate-600 dark:text-slate-300 transition-colors">diskominfo@mojokertokab.go.id</span>
                                      <svg class="w-3.5 h-3.5 text-slate-300 dark:text-slate-500 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                  </div>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>

              <!-- Divider & Copyright -->
              <div class="mt-14 pt-8 border-t border-blue-50 dark:border-slate-700 text-center transition-colors">
                  <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] transition-colors">
                      &copy; 2026 DISKOMINFO KABUPATEN MOJOKERTO
                  </p>
              </div>
          </div>
      </div>

      <!-- Scroll to Top Button -->
      <button 
          @click="scrollToTop" 
          :class="[
              'fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-white dark:bg-slate-800 border-[1.5px] border-blue-600 dark:border-blue-400 flex flex-col items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-700 hover:shadow-[0_4px_15px_rgb(37,99,235,0.2)] transition-all duration-300 focus:outline-none shadow-sm group',
              showTopBtn ? 'opacity-100 translate-y-0' : 'opacity-0 pointer-events-none translate-y-4'
          ]"
      >
          <svg class="w-4 h-4 mb-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
          <span class="text-[8px] font-bold uppercase tracking-wider">Top</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const userMenuOpen = ref(false);
const dropdownRef = ref(null);

const isDark = ref(false);
const showTopBtn = ref(false);

const toggleDarkMode = () => {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('darkMode', 'true');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('darkMode', 'false');
  }
};

// Tutup dropdown jika klik di luar area dropdown
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    userMenuOpen.value = false;
  }
};

const handleScroll = () => {
  showTopBtn.value = window.scrollY > 300;
};

onMounted(() => {
  const saved = localStorage.getItem('darkMode');
  if (saved !== null) {
    isDark.value = saved === 'true';
  } else if (user.value?.dark_mode !== undefined) {
    isDark.value = !!user.value.dark_mode;
  } else {
    isDark.value = document.documentElement.classList.contains('dark');
  }

  if (isDark.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }

  // Pasang listener click-outside
  document.addEventListener('mousedown', handleClickOutside);
  // Pasang listener scroll
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside);
  window.removeEventListener('scroll', handleScroll);
});

const logout = () => {
  router.post('/logout');
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text);
  // Optional: could add a tiny toast notification here
};
</script>

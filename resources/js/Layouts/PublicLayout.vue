<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 flex flex-col font-sans transition-colors duration-300">
    <!-- Navbar -->
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo MojoPedia -->
        <div class="flex-shrink-0 flex items-center">
          <Link href="/" class="flex items-center gap-2 group">
            <span class="text-2xl font-bold tracking-tight bg-gradient-to-r from-red-600 via-rose-500 to-amber-500 bg-clip-text text-transparent group-hover:opacity-80 transition">
              MojoPedia
            </span>
          </Link>
        </div>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center space-x-8 text-sm font-medium">
          <Link href="/" class="transition text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400">Beranda</Link>
          <Link href="/tentang" class="transition text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400">Tentang</Link>
          <Link href="/kategori" class="transition text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400">Kategori</Link>
          <Link href="/forum" class="transition text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400">Forum</Link>
          <Link href="/faq" class="transition text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400">FAQs</Link>
        </nav>

        <!-- Right Side Actions: Dark Mode Toggle & User Auth -->
        <div class="flex items-center space-x-4">
          <!-- Dark / Light Mode Toggle Button -->
          <button 
            @click="toggleDarkMode" 
            type="button"
            class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition"
            :title="isDark ? 'Beralih ke Light Mode' : 'Beralih ke Dark Mode'"
          >
            <!-- Sun Icon (shown in dark mode) -->
            <svg v-if="isDark" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 01-8 0z" />
            </svg>
            <!-- Moon Icon (shown in light mode) -->
            <svg v-else class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </button>

          <!-- Logged In User Dropdown -->
          <div v-if="user" class="relative" ref="dropdownRef">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:text-red-600 dark:hover:text-red-400 transition focus:outline-none">
              <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-xs overflow-hidden shrink-0 ring-2 ring-red-500/20">
                <img v-if="user.foto_profil" :src="`/storage/${user.foto_profil}`" :alt="user.name" class="w-full h-full object-cover">
                <span v-else>{{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
              </div>
              <span class="hidden sm:inline font-semibold text-xs text-slate-700 dark:text-slate-200">{{ user.name }}</span>
            </button>

            <!-- Dropdown Menu -->
            <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 text-left">
              <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-700">
                <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ user.name }}</p>
                <p class="text-[11px] text-slate-400 truncate">{{ user.email }}</p>
                <p class="text-[10px] font-semibold text-red-500 mt-0.5 capitalize">{{ user.role || 'Anggota' }}</p>
              </div>

              <a href="/dashboard" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Dashboard
              </a>

              <a href="/profile" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Pengaturan Profil
              </a>

              <button @click="logout" class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition text-left">
                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Keluar
              </button>
            </div>
          </div>

          <!-- Guest Action Buttons (Shown when not logged in) -->
          <div v-else class="flex items-center space-x-3">
            <Link href="/login" class="text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 transition">Masuk</Link>
            <Link href="/register" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs font-semibold text-white bg-red-600 hover:bg-red-700 transition">
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
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 py-8 text-center text-xs text-slate-500">
      <div class="max-w-7xl mx-auto px-4 space-y-2">
        <div class="text-slate-800 dark:text-slate-200 font-bold text-sm">MojoPedia</div>
        <p>&copy; 2026 MojoPedia — Sistem Informasi Manajemen Pengetahuan BRIN.</p>
      </div>
    </footer>
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
});

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside);
});

const logout = () => {
  router.post('/logout');
};
</script>

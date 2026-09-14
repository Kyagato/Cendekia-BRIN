<template>
  <PublicLayout>
    <div class="py-16 px-4 max-w-5xl mx-auto space-y-10">
      
      <!-- Hero Section -->
      <div class="text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-[#eff6ff] dark:bg-blue-950/80 border border-blue-200 dark:border-blue-900 text-[#2563eb] dark:text-blue-300 rounded-full text-xs font-semibold">
          <span>⚡ Inertia.js + Vue 3 Integrated</span>
        </div>
        <h1 class="text-4xl sm:text-6xl font-extrabold text-[#0f172a] dark:text-white tracking-tight">
          Selamat Datang di <span class="text-[#2563eb]">{{ appName || 'MojoPedia' }}</span>
        </h1>
        <p class="text-[#475569] dark:text-slate-400 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
          Platform Single Page Application (SPA) modern berbasis Vue 3 dan Inertia.js untuk Repositori Pengetahuan Terpadu MojoPedia — Digital Governance System.
        </p>
      </div>

      <!-- Interactive Counter Card (Vue State Demo) -->
      <div class="bg-white dark:bg-slate-900 border border-[#e2e8f0] dark:border-slate-800 rounded-xl p-6 shadow-sm max-w-xl mx-auto text-center space-y-4">
        <h3 class="text-lg font-bold text-[#0f172a] dark:text-white flex items-center justify-center gap-2">
          <span>🧪 Pengujian Reaktivitas Vue 3 State</span>
        </h3>
        <p class="text-xs text-[#475569] dark:text-slate-400">
          Klik tombol di bawah untuk membuktikan bahwa komponen Vue 3 berjalan secara reaktif di browser.
        </p>
        
        <div class="flex items-center justify-center gap-4 py-3">
          <button @click="count--" class="w-10 h-10 bg-[#f1f5f9] hover:bg-[#e2e8f0] dark:bg-slate-800 dark:hover:bg-slate-700 text-[#0f172a] dark:text-slate-200 border border-[#cbd5e1] dark:border-slate-700 rounded-lg font-bold text-lg transition flex items-center justify-center">
            -
          </button>
          <span class="text-3xl font-extrabold text-[#2563eb] w-16 text-center font-mono">
            {{ count }}
          </span>
          <button @click="count++" class="w-10 h-10 bg-[#2563eb] hover:bg-[#1d4ed8] text-white rounded-lg font-bold text-lg transition flex items-center justify-center shadow-sm">
            +
          </button>
        </div>

        <div class="pt-2 border-t border-[#f1f5f9] dark:border-slate-800 text-xs text-[#475569] dark:text-slate-400">
          Nilai terkuadrat: <span class="font-mono text-[#0f172a] dark:text-white font-bold">{{ count * count }}</span>
        </div>
      </div>

      <!-- Feature Highlights -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 border border-[#e2e8f0] dark:border-slate-800 p-5 rounded-lg space-y-2 card-hover">
          <div class="w-8 h-8 rounded-lg bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 flex items-center justify-center text-sm font-bold">1</div>
          <h4 class="font-bold text-[#0f172a] dark:text-white text-sm">Tanpa Reload (SPA)</h4>
          <p class="text-xs text-[#475569] dark:text-slate-400">Navigasi halaman berlangsung instan tanpa reload browser sama sekali.</p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-[#e2e8f0] dark:border-slate-800 p-5 rounded-lg space-y-2 card-hover">
          <div class="w-8 h-8 rounded-lg bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 flex items-center justify-center text-sm font-bold">2</div>
          <h4 class="font-bold text-[#0f172a] dark:text-white text-sm">Composition API</h4>
          <p class="text-xs text-[#475569] dark:text-slate-400">Menggunakan standar Vue 3 Modern Script Setup &amp; Composition API.</p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-[#e2e8f0] dark:border-slate-800 p-5 rounded-lg space-y-2 card-hover">
          <div class="w-8 h-8 rounded-lg bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 flex items-center justify-center text-sm font-bold">3</div>
          <h4 class="font-bold text-[#0f172a] dark:text-white text-sm">Laravel Backend</h4>
          <p class="text-xs text-[#475569] dark:text-slate-400">Tetap memanfaatkan Eloquent, Middleware, Auth, dan Controller Laravel.</p>
        </div>
      </div>

    </div>
  </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import PublicLayout from '../Layouts/PublicLayout.vue';

defineProps({
  appName: {
    type: String,
    default: 'MojoPedia'
  }
});

const count = ref(1);
</script>

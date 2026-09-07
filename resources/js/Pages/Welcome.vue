<template>
  <PublicLayout>
    <div class="py-16 px-4 max-w-5xl mx-auto space-y-10">
      
      <!-- Hero Section -->
      <div class="text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-500/10 border border-red-500/20 text-red-400 rounded-full text-xs font-semibold">
          <span>⚡ Inertia.js + Vue 3 Integrated</span>
        </div>
        <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight">
          Selamat Datang di <span class="text-red-500">{{ appName }}</span>
        </h1>
        <p class="text-slate-400 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
          Ini adalah bukti konsep (Proof of Concept) pengoperasian **Vue.js 3 Single Page Application (SPA)** di dalam lingkungan Cendekia-BRIN menggunakan Inertia.js.
        </p>
      </div>

      <!-- Interactive Counter Card (Vue State Demo) -->
      <div class="bg-slate-800/80 border border-slate-700 rounded-2xl p-6 shadow-xl max-w-xl mx-auto text-center space-y-4">
        <h3 class="text-lg font-bold text-white flex items-center justify-center gap-2">
          <span>🧪 Pengujian Reaktivitas Vue 3 State</span>
        </h3>
        <p class="text-xs text-slate-400">
          Klik tombol di bawah untuk membuktikan bahwa komponen Vue 3 berjalan secara reaktif di browser.
        </p>
        
        <div class="flex items-center justify-center gap-4 py-3">
          <button @click="count--" class="w-10 h-10 bg-slate-700 hover:bg-slate-600 rounded-xl font-bold text-lg text-white transition flex items-center justify-center">
            -
          </button>
          <span class="text-3xl font-extrabold text-red-400 w-16 text-center font-mono">
            {{ count }}
          </span>
          <button @click="count++" class="w-10 h-10 bg-red-600 hover:bg-red-500 rounded-xl font-bold text-lg text-white transition flex items-center justify-center shadow-lg">
            +
          </button>
        </div>

        <div class="pt-2 border-t border-slate-700/60 text-xs text-slate-400">
          Nilai terkuadrat: <span class="font-mono text-white font-bold">{{ count * count }}</span>
        </div>
      </div>

      <!-- Feature Highlights -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-slate-800/40 border border-slate-700/60 p-5 rounded-xl space-y-2">
          <div class="w-8 h-8 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center text-sm font-bold">1</div>
          <h4 class="font-bold text-white text-sm">Tanpa Reload (SPA)</h4>
          <p class="text-xs text-slate-400">Navigasi halaman berlangsung instan tanpa reload browser sama sekali.</p>
        </div>

        <div class="bg-slate-800/40 border border-slate-700/60 p-5 rounded-xl space-y-2">
          <div class="w-8 h-8 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center text-sm font-bold">2</div>
          <h4 class="font-bold text-white text-sm">Composition API</h4>
          <p class="text-xs text-slate-400">Menggunakan standar Vue 3 Modern Script Setup &amp; Composition API.</p>
        </div>

        <div class="bg-slate-800/40 border border-slate-700/60 p-5 rounded-xl space-y-2">
          <div class="w-8 h-8 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center text-sm font-bold">3</div>
          <h4 class="font-bold text-white text-sm">Laravel Backend</h4>
          <p class="text-xs text-slate-400">Tetap memanfaatkan Eloquent, Middleware, Auth, dan Controller Laravel.</p>
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

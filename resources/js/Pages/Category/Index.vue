<template>
  <PublicLayout>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-slate-900 via-red-950 to-slate-900">
      <div class="container mx-auto px-4">
        <nav class="flex text-sm text-white/70 mb-4" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <Link href="/" class="inline-flex items-center hover:text-white transition">Beranda</Link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-3 h-3 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white font-medium ml-1">Kategori & Repositori</span>
              </div>
            </li>
          </ol>
        </nav>
        <h1 class="text-4xl font-bold text-white mb-2">Repositori Pengetahuan</h1>
        <p class="text-lg text-white/80">Jelajahi dan temukan informasi yang Anda butuhkan.</p>
      </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 container mx-auto px-4">
      <!-- Filter Bar -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 mb-8">
        <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
          <div class="flex-grow w-full flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="relative w-full md:w-96">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
              </div>
              <input 
                v-model="searchQuery"
                @keyup.enter="applyFilter"
                type="text" 
                placeholder="Cari pengetahuan..." 
                class="w-full pl-10 pr-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
              />
            </div>

            <!-- Tipe Select -->
            <select v-model="selectedTipe" @change="applyFilter" class="bg-slate-900 border border-slate-700 text-slate-100 rounded-lg text-sm px-3 py-2 focus:outline-none">
              <option value="">Semua Tipe Media</option>
              <option value="Teks">Teks</option>
              <option value="Video">Video</option>
              <option value="Gambar">Gambar</option>
              <option value="Audio">Audio</option>
            </select>

            <!-- Kategori Select -->
            <select v-model="selectedKategori" @change="applyFilter" class="bg-slate-900 border border-slate-700 text-slate-100 rounded-lg text-sm px-3 py-2 focus:outline-none">
              <option value="">Semua Kategori</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.nama_kategori }} ({{ cat.knowledge_count }})
              </option>
            </select>
          </div>

          <button @click="applyFilter" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition">
            Filter Data
          </button>
        </div>
      </div>

      <!-- Knowledge Cards Grid -->
      <div v-if="knowledge && knowledge.data && knowledge.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <KnowledgeCard v-for="item in knowledge.data" :key="item.id" :item="item" />
      </div>

      <div v-else class="text-center py-16 bg-slate-800 rounded-xl border border-slate-700">
        <h3 class="text-xl font-bold text-white mb-2">Tidak ada pengetahuan ditemukan</h3>
        <p class="text-slate-400 text-sm">Coba sesuaikan kata kunci pencarian atau filter media Anda.</p>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import KnowledgeCard from '../../Components/KnowledgeCard.vue';

const props = defineProps({
  categories: Array,
  tags: Array,
  knowledge: Object,
});

const searchQuery = ref('');
const selectedTipe = ref('');
const selectedKategori = ref('');

const applyFilter = () => {
  router.get('/kategori', {
    q: searchQuery.value,
    tipe: selectedTipe.value,
    kategori: selectedKategori.value,
  }, { preserveState: true });
};
</script>

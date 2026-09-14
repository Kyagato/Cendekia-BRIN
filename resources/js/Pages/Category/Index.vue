<template>
  <PublicLayout>
    <!-- Header -->
    <section class="relative py-14 bg-[#1e3a8a] text-white overflow-hidden">

      <div class="container mx-auto px-4 relative z-10 text-center">
        <nav class="flex justify-center text-xs text-blue-200 mb-3" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li>
              <Link href="/" class="hover:text-white transition">Beranda</Link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-3 h-3 mx-1 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white font-medium ml-1">Kategori & Repositori</span>
              </div>
            </li>
          </ol>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-2 tracking-tight">Katalog Repositori Pengetahuan</h1>
        <p class="text-base text-blue-100 max-w-xl mx-auto">Telusuri seluruh koleksi karya tulis, kajian ilmiah, dan aset multimedia MojoPedia.</p>
      </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Filter Bar -->
      <div class="bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-[#e2e8f0] dark:border-slate-800 p-4 mb-8">
        <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
          <div class="flex-grow w-full flex flex-col md:flex-row gap-3">
            <!-- Search Input -->
            <div class="relative w-full md:w-80">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94a3b8]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
              </div>
              <input 
                v-model="searchQuery"
                @keyup.enter="applyFilter"
                type="text" 
                placeholder="Saring judul dokumen..." 
                class="w-full pl-9 pr-3 py-2 bg-[#f8fafc] dark:bg-slate-800 border border-[#cbd5e1] dark:border-slate-700 rounded-lg text-[#0f172a] dark:text-slate-100 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#2563eb]"
              />
            </div>

            <!-- Tipe Select -->
            <select v-model="selectedTipe" @change="applyFilter" class="bg-[#f8fafc] dark:bg-slate-800 border border-[#cbd5e1] dark:border-slate-700 text-[#0f172a] dark:text-slate-100 rounded-lg text-xs sm:text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2563eb]">
              <option value="">Semua Tipe Media</option>
              <option value="Teks">Teks</option>
              <option value="Video">Video</option>
              <option value="Gambar">Gambar</option>
              <option value="Audio">Audio</option>
            </select>

            <!-- Kategori Select -->
            <select v-model="selectedKategori" @change="applyFilter" class="bg-[#f8fafc] dark:bg-slate-800 border border-[#cbd5e1] dark:border-slate-700 text-[#0f172a] dark:text-slate-100 rounded-lg text-xs sm:text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2563eb]">
              <option value="">Semua Kategori</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.nama_kategori }} ({{ cat.knowledge_count }})
              </option>
            </select>
          </div>

          <button @click="applyFilter" class="w-full md:w-auto px-6 py-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white rounded-lg font-semibold text-xs sm:text-sm transition shrink-0 shadow-sm">
            Terapkan Filter
          </button>
        </div>
      </div>

      <!-- Knowledge Cards Grid -->
      <div v-if="knowledge && knowledge.data && knowledge.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <KnowledgeCard v-for="item in knowledge.data" :key="item.id" :item="item" />
      </div>

      <div v-else class="text-center py-16 bg-white dark:bg-slate-900 rounded-lg border border-[#e2e8f0] dark:border-slate-800">
        <h3 class="text-xl font-bold text-[#0f172a] dark:text-white mb-2">Tidak ada pengetahuan ditemukan</h3>
        <p class="text-[#475569] dark:text-slate-400 text-sm">Coba sesuaikan kata kunci pencarian atau filter media Anda.</p>
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

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
      <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-[#e2e8f0] dark:border-slate-800 p-4 mb-8">
        <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between">
          <div class="flex-grow w-full flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            <!-- Search Input -->
            <div class="relative w-full md:w-72 lg:w-80">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94a3b8]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
              </div>
              <input 
                v-model="searchQuery"
                @keyup.enter="applyFilter"
                type="text" 
                placeholder="Saring judul dokumen..." 
                class="w-full pl-9 pr-3 py-2.5 bg-[#f8fafc] dark:bg-slate-800 border border-[#cbd5e1] dark:border-slate-700 rounded-lg text-[#0f172a] dark:text-slate-100 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#2563eb] transition"
              />
            </div>

            <!-- Tipe Select -->
            <div class="w-full md:w-auto">
              <select 
                v-model="selectedTipe" 
                class="w-full md:w-auto min-w-[150px] bg-[#f8fafc] dark:bg-slate-800 border border-[#cbd5e1] dark:border-slate-700 text-[#0f172a] dark:text-slate-100 rounded-lg text-xs sm:text-sm pl-3.5 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#2563eb] transition cursor-pointer"
              >
                <option value="">Semua Tipe</option>
                <option value="Teks">Teks</option>
                <option value="Video">Video</option>
                <option value="Gambar">Gambar</option>
                <option value="Audio">Audio</option>
              </select>
            </div>

            <!-- Kategori Select (Lebih panjang dan padding kanan lega agar tidak menabrak ikon panah) -->
            <div class="w-full md:w-auto">
              <select 
                v-model="selectedKategori" 
                class="w-full md:w-auto min-w-[240px] md:min-w-[280px] lg:min-w-[320px] max-w-full bg-[#f8fafc] dark:bg-slate-800 border border-[#cbd5e1] dark:border-slate-700 text-[#0f172a] dark:text-slate-100 rounded-lg text-xs sm:text-sm pl-3.5 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#2563eb] transition cursor-pointer"
              >
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.nama_kategori }} ({{ cat.knowledge_count }})
                </option>
              </select>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2 shrink-0">
            <!-- Reset Button (Muncul jika ada filter yang aktif) -->
            <button 
              v-if="isFiltered"
              @click="resetFilter"
              type="button"
              class="px-4 py-2.5 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg text-xs sm:text-sm font-semibold transition flex items-center gap-1.5"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
              <span>Reset</span>
            </button>

            <!-- Terapkan Filter Button -->
            <button 
              @click="applyFilter" 
              :disabled="isLoading"
              type="button"
              class="w-full md:w-auto px-6 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] active:bg-[#1e40af] disabled:opacity-75 text-white rounded-lg font-semibold text-xs sm:text-sm transition shadow-sm flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isLoading ? 'Menerapkan...' : 'Terapkan Filter' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Knowledge Cards Grid -->
      <div v-if="knowledge && knowledge.data && knowledge.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <KnowledgeCard v-for="item in knowledge.data" :key="item.id" :item="item" />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16 bg-white dark:bg-slate-900 rounded-xl border border-[#e2e8f0] dark:border-slate-800">
        <div class="w-14 h-14 bg-blue-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto text-blue-600 dark:text-blue-400 mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-[#0f172a] dark:text-white mb-1.5">Tidak ada pengetahuan ditemukan</h3>
        <p class="text-[#475569] dark:text-slate-400 text-xs sm:text-sm max-w-sm mx-auto mb-4">Coba sesuaikan kata kunci pencarian atau kombinasi filter media dan kategori Anda.</p>
        <button 
          v-if="isFiltered" 
          @click="resetFilter" 
          class="px-4 py-2 bg-[#2563eb] text-white text-xs font-semibold rounded-lg hover:bg-[#1d4ed8] transition"
        >
          Tampilkan Semua Pengetahuan
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="knowledge && knowledge.links && knowledge.links.length > 3" class="mt-10 flex justify-center items-center gap-1.5 flex-wrap">
        <template v-for="(link, index) in knowledge.links" :key="index">
          <Link
            v-if="link.url"
            :href="link.url"
            v-html="link.label"
            :class="[
              'px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors',
              link.active 
                ? 'bg-[#2563eb] text-white shadow-sm' 
                : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'
            ]"
            preserve-scroll
            preserve-state
          />
          <span
            v-else
            v-html="link.label"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg text-slate-400 dark:text-slate-600 cursor-not-allowed border border-slate-100 dark:border-slate-800"
          />
        </template>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import KnowledgeCard from '../../Components/KnowledgeCard.vue';

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
  tags: {
    type: Array,
    default: () => [],
  },
  knowledge: {
    type: Object,
    default: () => ({ data: [] }),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const searchQuery = ref(props.filters?.q || '');
const selectedTipe = ref(props.filters?.tipe || '');
const selectedKategori = ref(props.filters?.kategori || '');
const isLoading = ref(false);

// Watch for filter props changes (e.g. navigation or URL changes)
watch(() => props.filters, (newFilters) => {
  if (newFilters) {
    searchQuery.value = newFilters.q || '';
    selectedTipe.value = newFilters.tipe || '';
    selectedKategori.value = newFilters.kategori || '';
  }
}, { deep: true });

const isFiltered = computed(() => {
  return !!(searchQuery.value || selectedTipe.value || selectedKategori.value);
});

const applyFilter = () => {
  isLoading.value = true;
  const params = {};
  if (searchQuery.value && searchQuery.value.trim() !== '') {
    params.q = searchQuery.value.trim();
  }
  if (selectedTipe.value && selectedTipe.value !== '') {
    params.tipe = selectedTipe.value;
  }
  if (selectedKategori.value && selectedKategori.value !== '') {
    params.kategori = selectedKategori.value;
  }

  router.get('/kategori', params, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

const resetFilter = () => {
  searchQuery.value = '';
  selectedTipe.value = '';
  selectedKategori.value = '';
  isLoading.value = true;
  router.get('/kategori', {}, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false;
    },
  });
};
</script>

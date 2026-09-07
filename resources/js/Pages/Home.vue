<template>
  <PublicLayout>
    <!-- Hero Section -->
    <section class="relative pt-32 pb-24 hero-gradient overflow-hidden bg-gradient-to-br from-slate-900 via-red-950 to-slate-900">
      <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-600/20 rounded-full blur-3xl mix-blend-multiply opacity-50"></div>
        <div class="absolute top-1/4 -right-24 w-[30rem] h-[30rem] bg-rose-500/20 rounded-full blur-3xl mix-blend-multiply opacity-50"></div>
      </div>

      <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight drop-shadow-sm">
          <span class="text-white">MojoPedia</span>
        </h1>
        <h2 class="text-2xl md:text-3xl font-semibold text-white/90 mb-6 drop-shadow">
          Sistem Informasi Manajemen Pengetahuan
        </h2>
        <p class="text-lg md:text-xl text-white/80 max-w-3xl mx-auto mb-10 leading-relaxed font-light">
          Platform terpadu untuk mengelola, berbagi, dan menemukan pengetahuan, dokumen, serta informasi strategis di lingkungan Badan Riset dan Inovasi Nasional.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <a href="/kategori" class="w-full sm:w-auto px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            Jelajahi Pengetahuan
          </a>
          <a href="/tentang" class="w-full sm:w-auto px-8 py-4 bg-transparent border-2 border-white/80 text-white hover:bg-white/10 rounded-xl font-bold transition">
            Pelajari Lebih Lanjut
          </a>
        </div>
      </div>
    </section>

    <!-- Interactive Search Section -->
    <section class="relative z-20 -mt-10 mb-16 container mx-auto px-4">
      <div class="max-w-4xl mx-auto relative">
        <div class="glass bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl p-2 rounded-2xl shadow-2xl border border-white/20 dark:border-slate-700 flex items-center relative overflow-hidden focus-within:ring-2 focus-within:ring-red-500/60">
          <div class="pl-4 text-slate-500 dark:text-slate-400">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <input 
            v-model="searchQuery"
            @input="onSearchInput"
            @focus="showSearchResults = searchQuery.length >= 2"
            type="text"
            placeholder="Cari pengetahuan, dokumen, atau topik..."
            class="w-full bg-transparent border-none focus:ring-0 text-slate-800 dark:text-slate-100 px-4 py-3 text-lg placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none"
          />

          <div v-if="searchLoading" class="pr-4 text-red-500">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>

          <a :href="`/cari?q=${encodeURIComponent(searchQuery)}`" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-semibold transition shadow-md hidden sm:block">
            Cari
          </a>
        </div>

        <!-- Autocomplete Dropdown -->
        <div v-if="showSearchResults" class="absolute left-0 right-0 max-w-4xl mx-auto mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden z-30">
          <div class="max-h-96 overflow-y-auto">
            <div v-if="searchResults.length === 0 && !searchLoading" class="p-4 text-center text-slate-500 dark:text-slate-400">
              Tidak ada hasil ditemukan
            </div>
            <a 
              v-for="item in searchResults" 
              :key="item.id"
              :href="`/knowledge/${item.id}`"
              class="flex items-start p-4 hover:bg-slate-50 dark:hover:bg-slate-700 border-b border-slate-100 dark:border-slate-700 transition"
              @click="showSearchResults = false"
            >
              <span class="px-2.5 py-1 text-xs font-semibold rounded-full mr-3 mt-0.5 whitespace-nowrap bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300">
                {{ item.tipe }}
              </span>
              <div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ item.judul }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ item.kategori?.nama_kategori }}</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Pengetahuan Unggulan Section -->
    <section v-if="featuredKnowledges && featuredKnowledges.length" class="py-12 bg-amber-500/5 dark:bg-amber-950/20 container mx-auto px-4 rounded-3xl mb-8 border border-amber-500/20">
      <div class="flex items-center gap-3 mb-8">
        <div class="h-8 w-2 bg-amber-500 rounded-full"></div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Pengetahuan Unggulan</h2>
        <span class="px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-700 rounded-full dark:bg-amber-950 dark:text-amber-400">Pilihan Editor</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <KnowledgeCard v-for="item in featuredKnowledges" :key="item.id" :item="item" />
      </div>
    </section>

    <!-- Paling Banyak Dilihat Section -->
    <section class="py-12 container mx-auto px-4">
      <div class="flex items-center gap-3 mb-8">
        <div class="h-8 w-2 bg-red-500 rounded-full"></div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Paling Banyak Dilihat</h2>
      </div>

      <div v-if="!mostViewed || mostViewed.length === 0" class="text-center py-12 bg-slate-800/50 rounded-2xl border border-slate-700">
        <p class="text-slate-400">Belum ada konten saat ini.</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <KnowledgeCard v-for="item in mostViewed" :key="item.id" :item="item" />
      </div>
    </section>

    <!-- Terbaru Section dengan Tab Reaktif Vue 3 -->
    <section class="py-12 bg-slate-950/50">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
          <div class="flex items-center gap-3">
            <div class="h-8 w-2 bg-rose-500 rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Pengetahuan Terbaru</h2>
          </div>

          <!-- Reactive Tabs -->
          <div class="flex flex-wrap gap-2 bg-white dark:bg-slate-800 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
            <button 
              v-for="tab in ['Semua', 'Teks', 'Video', 'Gambar', 'Audio']" 
              :key="tab"
              @click="activeTab = tab"
              :class="activeTab === tab ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-700'"
              class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
            >
              {{ tab }}
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <KnowledgeCard v-for="item in filteredLatest" :key="item.id" :item="item" />
        </div>

        <div class="text-center mt-10">
          <a href="/kategori" class="inline-flex items-center text-red-500 font-semibold hover:underline">
            Lihat Semua Pengetahuan
            <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </a>
        </div>
      </div>
    </section>

    <!-- Kategori & Label Populer -->
    <section class="py-12 container mx-auto px-4">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Kategori Populer -->
        <div class="lg:col-span-3">
          <div class="flex items-center gap-3 mb-6">
            <div class="h-8 w-2 bg-purple-500 rounded-full"></div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Kategori Populer</h2>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            <a 
              v-for="cat in popularCategories" 
              :key="cat.id" 
              :href="`/kategori/${cat.id}`"
              class="group bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center gap-4 transition-all hover:border-red-500 relative overflow-hidden"
            >
              <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center text-red-500 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
              </div>
              <div class="overflow-hidden">
                <h3 class="font-semibold text-slate-800 dark:text-white truncate group-hover:text-red-500 transition-colors">{{ cat.nama_kategori }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ cat.knowledge_count || 0 }} dokumen</p>
              </div>
            </a>
          </div>
        </div>

        <!-- Tag Populer -->
        <div class="lg:col-span-1">
          <div class="flex items-center gap-3 mb-6">
            <div class="h-8 w-2 bg-amber-500 rounded-full"></div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tag Populer</h2>
          </div>

          <div class="flex flex-wrap gap-2">
            <a 
              v-for="tag in popularTags" 
              :key="tag.id" 
              :href="`/kategori?label=${encodeURIComponent(tag.nama_label)}`"
              class="px-3 py-1.5 bg-slate-800 hover:bg-red-950 text-slate-300 hover:text-red-400 text-sm rounded-full transition-colors border border-slate-700 hover:border-red-500/50"
            >
              #{{ tag.nama_label }}
              <span class="text-xs text-slate-500 ml-1">({{ tag.knowledge_count }})</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '../Layouts/PublicLayout.vue';
import KnowledgeCard from '../Components/KnowledgeCard.vue';

const props = defineProps({
  featuredKnowledges: Array,
  mostViewed: Array,
  latest: Array,
  popularCategories: Array,
  popularTags: Array,
});

const activeTab = ref('Semua');
const searchQuery = ref('');
const searchResults = ref([]);
const searchLoading = ref(false);
const showSearchResults = ref(false);

const filteredLatest = computed(() => {
  if (!props.latest) return [];
  if (activeTab.value === 'Semua') return props.latest;
  return props.latest.filter(item => item.tipe === activeTab.value);
});

let searchTimeout = null;
const onSearchInput = () => {
  clearTimeout(searchTimeout);
  if (searchQuery.value.length < 2) {
    searchResults.value = [];
    showSearchResults.value = false;
    return;
  }
  searchLoading.value = true;
  searchTimeout = setTimeout(async () => {
    try {
      const res = await fetch(`/api/search?q=${encodeURIComponent(searchQuery.value)}`);
      searchResults.value = await res.json();
      showSearchResults.value = true;
    } catch (e) {
      console.error(e);
    }
    searchLoading.value = false;
  }, 300);
};
</script>

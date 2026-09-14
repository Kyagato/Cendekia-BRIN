<template>
  <PublicLayout>
    <!-- Institutional Hero Section -->
    <section class="relative bg-[#1e3a8a] text-white pt-16 pb-24 lg:pt-20 lg:pb-32 overflow-hidden">

      <div class="max-w-5xl mx-auto px-4 sm:px-6 relative z-10 text-center">
        <!-- Brand Pill Badge -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[#dbeafe] text-xs font-semibold tracking-wide uppercase mb-6 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#60a5fa] animate-pulse"></span>
          MojoPedia • Digital Governance & Knowledge System
        </div>

        <!-- Hero Headline -->
        <h1 class="text-3xl sm:text-4xl lg:text-[44px] font-extrabold text-white tracking-[-0.02em] leading-tight lg:leading-[52px] mb-4">
          Pusat Manajemen Pengetahuan <span class="text-[#bfdbfe]">MojoPedia</span>
        </h1>
        <p class="text-base sm:text-lg text-blue-100 max-w-2xl mx-auto mb-8 font-normal leading-[26px]">
          Sistem terpadu tata kelola dokumen riset, kajian strategis, serta aset multimedia ilmu pengetahuan untuk birokrasi dan publik.
        </p>

        <!-- Integrated Pill Search Module (Spec: Pill 9999px, Solid #ffffff, Inline #2563eb submit) -->
        <div class="max-w-2xl mx-auto relative mb-6">
          <div class="bg-white rounded-full shadow-xl border border-slate-200/80 p-1.5 flex items-center transition-all search-focus-glow">
            <div class="pl-4 pr-2 text-[#94a3b8]">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <input 
              v-model="searchQuery"
              @input="onSearchInput"
              @focus="showSearchResults = searchQuery.length >= 2"
              type="text" 
              placeholder="Cari judul riset, nama penulis, topik, atau kata kunci dokumen..." 
              class="w-full bg-transparent border-none text-[#0f172a] px-2 py-2 text-sm sm:text-base placeholder-[#94a3b8] focus:outline-none focus:ring-0"
            />

            <div v-if="searchLoading" class="px-3 text-[#2563eb]">
              <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>

            <a 
              :href="`/cari?q=${encodeURIComponent(searchQuery)}`" 
              class="bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-6 sm:px-8 py-2.5 rounded-full text-sm font-semibold transition shrink-0 shadow-sm"
            >
              Cari
            </a>
          </div>

          <!-- Autocomplete Dropdown -->
          <div v-if="showSearchResults" class="absolute left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-[#e2e8f0] dark:border-slate-700 overflow-hidden z-30 text-left">
            <div class="max-h-80 overflow-y-auto divide-y divide-[#f1f5f9] dark:divide-slate-700">
              <div v-if="searchResults.length === 0 && !searchLoading" class="p-4 text-center text-sm text-[#475569] dark:text-slate-400">
                Tidak ada dokumen riset yang cocok dengan kata kunci tersebut.
              </div>
              <a 
                v-for="item in searchResults" 
                :key="item.id" 
                :href="`/knowledge/${item.id}`"
                class="flex items-center justify-between p-3.5 hover:bg-[#eff6ff] dark:hover:bg-slate-700 transition"
                @click="showSearchResults = false"
              >
                <div class="min-w-0 pr-3">
                  <h4 class="text-sm font-semibold text-[#0f172a] dark:text-white truncate">{{ item.judul }}</h4>
                  <p class="text-xs text-[#475569] dark:text-slate-400 mt-0.5">{{ item.kategori?.nama_kategori }}</p>
                </div>
                <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 shrink-0">
                  {{ item.tipe }}
                </span>
              </a>
            </div>
          </div>
        </div>

        <!-- Quick Filter Pills -->
        <div class="flex flex-wrap items-center justify-center gap-2 text-xs text-blue-100">
          <span class="text-blue-200 font-medium">Format:</span>
          <a href="/cari?tipe=Teks" class="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white transition flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span> Dokumen Teks
          </a>
          <a href="/cari?tipe=Video" class="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white transition flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Video Kajian
          </a>
          <a href="/cari?tipe=Gambar" class="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white transition flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Infografis
          </a>
          <a href="/kategori" class="px-3 py-1 rounded-full bg-white/15 hover:bg-white/25 text-white font-semibold transition">
            Lihat Semua Direktori &rarr;
          </a>
        </div>
      </div>

      <!-- Organic Gentle Wave Base Separator -->
      <div class="absolute bottom-0 left-0 right-0 leading-none pointer-events-none">
        <svg viewBox="0 0 1440 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-8 sm:h-12 text-[#f8fafc] dark:text-slate-950 preserve-3d" preserveAspectRatio="none">
          <path d="M0 48H1440V24C1200 4 960 44 720 24C480 4 240 44 0 24V48Z" fill="currentColor"/>
        </svg>
      </div>
    </section>

    <!-- Institutional Highlights Strip -->
    <div class="border-b border-[#e2e8f0] dark:border-slate-800 bg-white dark:bg-slate-900 py-4 shadow-sm relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-[#e2e8f0] dark:divide-slate-800">
          <div class="px-2">
            <span class="text-xs font-bold text-[#0f172a] dark:text-white uppercase tracking-wider block">Akses Terbuka</span>
            <span class="text-[11px] text-[#475569] dark:text-slate-400">Bebas diakses publik & ASN</span>
          </div>
          <div class="px-2">
            <span class="text-xs font-bold text-[#0f172a] dark:text-white uppercase tracking-wider block">Multi-Format</span>
            <span class="text-[11px] text-[#475569] dark:text-slate-400">Teks, Video, Audio & Gambar</span>
          </div>
          <div class="px-2">
            <span class="text-xs font-bold text-[#0f172a] dark:text-white uppercase tracking-wider block">Terkurasi</span>
            <span class="text-[11px] text-[#475569] dark:text-slate-400">Ditinjau oleh dewan penelaah</span>
          </div>
          <div class="px-2">
            <span class="text-xs font-bold text-[#0f172a] dark:text-white uppercase tracking-wider block">Terstandarisasi</span>
            <span class="text-[11px] text-[#475569] dark:text-slate-400">Sesuai tata kelola SPBE</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Pengetahuan Unggulan Section -->
    <section v-if="featuredKnowledges && featuredKnowledges.length" class="py-12 container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6 pb-3 border-b border-[#e2e8f0] dark:border-slate-800">
        <div class="flex items-center gap-3">
          <div class="w-1.5 h-6 bg-[#2563eb] rounded-full"></div>
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#0f172a] dark:text-white tracking-tight">Koleksi Unggulan</h2>
          </div>
          <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 border border-blue-200/50">
            Rekomendasi
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <KnowledgeCard v-for="item in featuredKnowledges" :key="item.id" :item="item" />
      </div>
    </section>

    <!-- Publikasi Pengetahuan Terbaru dengan Filter Tabs -->
    <section class="py-12 bg-[#f1f5f9]/60 dark:bg-slate-900/60 border-y border-[#e2e8f0] dark:border-slate-800">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 pb-3 border-b border-[#e2e8f0] dark:border-slate-800">
          <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-[#2563eb] rounded-full"></div>
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-[#0f172a] dark:text-white tracking-tight">Publikasi Pengetahuan Terbaru</h2>
              <p class="text-xs text-[#475569] dark:text-slate-400 mt-0.5">Aset pengetahuan terverifikasi dan paling mutakhir</p>
            </div>
          </div>

          <!-- Structured Filter Tabs (Spec: Active Solid #2563eb, Inactive White with #e2e8f0 border) -->
          <div class="inline-flex rounded-lg border border-[#e2e8f0] dark:border-slate-700 bg-white dark:bg-slate-800 p-1 shadow-sm">
            <button 
              v-for="tab in ['Semua', 'Teks', 'Video', 'Gambar', 'Audio']" 
              :key="tab"
              @click="activeTab = tab"
              :class="activeTab === tab ? 'bg-[#2563eb] text-white shadow-sm' : 'text-[#475569] dark:text-slate-300 hover:text-[#0f172a] dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700'"
              class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition"
            >
              {{ tab }}
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <KnowledgeCard v-for="item in filteredLatest" :key="item.id" :item="item" />
        </div>

        <div class="text-center mt-8">
          <a href="/kategori" class="inline-flex items-center gap-2 text-sm font-semibold text-[#2563eb] dark:text-blue-400 hover:underline">
            Jelajahi Semua Arsip Pengetahuan
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
          </a>
        </div>
      </div>
    </section>

    <!-- Paling Banyak Diakses Section -->
    <section class="py-12 container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center gap-3 mb-6 pb-3 border-b border-[#e2e8f0] dark:border-slate-800">
        <div class="w-1.5 h-6 bg-[#2563eb] rounded-full"></div>
        <div>
          <h2 class="text-xl sm:text-2xl font-bold text-[#0f172a] dark:text-white tracking-tight">Paling Sering Dibaca</h2>
          <p class="text-xs text-[#475569] dark:text-slate-400 mt-0.5">Dokumen referensi terpopuler di lingkungan pemerintahan</p>
        </div>
      </div>

      <div v-if="!mostViewed || mostViewed.length === 0" class="text-center py-10 bg-white dark:bg-slate-900 rounded-lg border border-[#e2e8f0] dark:border-slate-800">
        <p class="text-[#94a3b8] text-sm">Belum ada konten tercatat saat ini.</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <KnowledgeCard v-for="item in mostViewed" :key="item.id" :item="item" />
      </div>
    </section>

    <!-- Kategori Directory Cards (Spec: 44x44 icon container, #eff6ff bg, #2563eb stroke, #f8fafc count badge) -->
    <section class="py-12 bg-white dark:bg-slate-900 border-t border-[#e2e8f0] dark:border-slate-800">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
          <!-- Kategori Populer -->
          <div class="lg:col-span-3">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-1.5 h-6 bg-[#2563eb] rounded-full"></div>
              <h2 class="text-lg font-bold text-[#0f172a] dark:text-white uppercase tracking-wider text-sm">Direktori Kategori</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
              <a 
                v-for="cat in popularCategories" 
                :key="cat.id" 
                :href="`/kategori/${cat.id}`"
                class="group p-4 rounded-lg border border-[#e2e8f0] dark:border-slate-800 bg-white dark:bg-slate-900 card-hover flex flex-col justify-between"
              >
                <div class="flex items-start gap-3 mb-3">
                  <!-- 44x44 Rounded Square Icon Container with #eff6ff and #2563eb -->
                  <div class="w-11 h-11 rounded-lg bg-[#eff6ff] dark:bg-blue-950/80 text-[#2563eb] dark:text-blue-300 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <h3 class="text-sm font-semibold text-[#0f172a] dark:text-white group-hover:text-[#2563eb] dark:group-hover:text-blue-400 transition truncate">
                      {{ cat.nama_kategori }}
                    </h3>
                    <p class="text-xs text-[#475569] dark:text-slate-400 mt-0.5 line-clamp-1">
                      {{ cat.deskripsi || 'Koleksi dokumen dan aset terdaftar' }}
                    </p>
                  </div>
                </div>

                <!-- Bottom Chip / Count Badge (Spec: Solid #f8fafc pill, label-sm 11px font-semibold) -->
                <div class="pt-2 border-t border-[#f1f5f9] dark:border-slate-800 flex items-center justify-between text-[11px] font-semibold text-[#475569] dark:text-slate-400">
                  <span class="px-2 py-0.5 rounded-full bg-[#f8fafc] dark:bg-slate-800 border border-[#e2e8f0] dark:border-slate-700">
                    {{ cat.knowledge_count || 0 }} Pengetahuan
                  </span>
                  <svg class="w-3.5 h-3.5 text-[#94a3b8] group-hover:text-[#2563eb] group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </a>
            </div>
          </div>

          <!-- Topik Populer -->
          <div class="lg:col-span-1">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-1.5 h-6 bg-[#2563eb] rounded-full"></div>
              <h2 class="text-lg font-bold text-[#0f172a] dark:text-white uppercase tracking-wider text-sm">Topik Kata Kunci</h2>
            </div>

            <div class="flex flex-wrap gap-2">
              <a 
                v-for="tag in popularTags" 
                :key="tag.id" 
                :href="`/kategori?label=${encodeURIComponent(tag.nama_label)}`"
                class="px-2.5 py-1 bg-[#f8fafc] dark:bg-slate-800 hover:bg-[#eff6ff] hover:text-[#2563eb] dark:hover:bg-blue-950 dark:hover:text-blue-300 text-[#475569] dark:text-slate-300 text-xs font-semibold rounded-md transition border border-[#e2e8f0] dark:border-slate-700 inline-flex items-center gap-1"
              >
                <span>#{{ tag.nama_label }}</span>
                <span class="text-[10px] text-[#94a3b8]">({{ tag.knowledge_count }})</span>
              </a>
            </div>
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

<template>
  <PublicLayout>
    <!-- Header Banner -->
    <section class="bg-[#1e3a8a] text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex text-xs text-blue-200 mb-3" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li>
              <Link href="/" class="hover:text-white transition">Beranda</Link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-3 h-3 mx-1 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-white font-medium ml-1">Bookmark</span>
              </div>
            </li>
          </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-amber-400/20 border border-amber-300/30 flex items-center justify-center text-amber-300">
                <svg class="w-5 h-5 fill-amber-300" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
              </div>
              <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Koleksi Bookmark Saya</h1>
            </div>
            <p class="text-base sm:text-lg text-blue-100 mt-2">Daftar artikel dan aset pengetahuan yang Anda tandai untuk dibaca kembali.</p>
          </div>

          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-xs sm:text-sm font-semibold text-blue-100 self-start md:self-auto">
            <span>Total Tersimpan:</span>
            <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-900 font-extrabold text-xs">
              {{ knowledges.total || 0 }}
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- Filter & Search Bar -->
    <div class="sticky top-16 z-30 bg-white dark:bg-slate-900 shadow-sm border-b border-slate-200 dark:border-slate-800 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <form @submit.prevent="applyFilters" class="flex flex-col sm:flex-row gap-3">
          <!-- Search Input -->
          <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari dalam bookmark Anda..." 
              class="w-full pl-11 pr-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition"
            />
          </div>

          <!-- Custom Dropdown Filter Tipe Media -->
          <div class="flex items-center gap-2">
            <div class="relative w-full sm:w-auto" ref="dropdownRef">
              <button 
                type="button" 
                @click.stop="isDropdownOpen = !isDropdownOpen"
                class="group w-full sm:w-auto min-w-[155px] flex items-center justify-between gap-3 px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm hover:border-[#2563eb] dark:hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 transition cursor-pointer select-none shadow-sm"
              >
                <span class="truncate font-medium pr-1">{{ selectedTipe ? selectedTipe : 'Semua Format' }}</span>
                <!-- Ikon Panah dengan animasi putar balik saat hover dan saat open -->
                <svg 
                  class="w-4 h-4 text-slate-400 dark:text-slate-400 shrink-0 transition-transform duration-300 ease-out group-hover:-rotate-180 group-hover:text-[#2563eb] dark:group-hover:text-blue-400" 
                  :class="{ '!rotate-180 !text-[#2563eb] dark:!text-blue-400': isDropdownOpen }" 
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu Options -->
              <div 
                v-show="isDropdownOpen" 
                class="absolute right-0 sm:left-0 z-40 mt-1.5 w-full min-w-[160px] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 focus:outline-none"
              >
                <button 
                  type="button" 
                  @click="chooseTipe('')"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="!selectedTipe ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>Semua Format</span>
                  <svg v-if="!selectedTipe" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
                <button 
                  v-for="t in ['Teks', 'Video', 'Gambar', 'Audio']" 
                  :key="t"
                  type="button" 
                  @click="chooseTipe(t)"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="selectedTipe === t ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>{{ t }}</span>
                  <svg v-if="selectedTipe === t" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
              </div>
            </div>

            <button 
              type="submit" 
              class="px-4 py-2 rounded-xl bg-[#2563eb] hover:bg-blue-700 text-white text-sm font-semibold transition shadow-sm cursor-pointer"
            >
              Cari
            </button>

            <button 
              v-if="searchQuery || selectedTipe"
              type="button" 
              @click="resetFilters" 
              class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-medium transition cursor-pointer"
            >
              Reset
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-[500px]">
      <div v-if="knowledges.data && knowledges.data.length > 0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <KnowledgeCard 
            v-for="item in knowledges.data" 
            :key="item.id" 
            :item="{ ...item, is_bookmarked: true }" 
          />
        </div>

        <!-- Pagination -->
        <div v-if="knowledges.links && knowledges.links.length > 3" class="mt-10 flex justify-center">
          <div class="flex flex-wrap gap-1">
            <template v-for="(link, i) in knowledges.links" :key="i">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                class="px-3.5 py-2 text-xs font-semibold rounded-lg transition"
                :class="link.active ? 'bg-[#2563eb] text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-slate-100 dark:bg-slate-800/50 text-slate-400 border border-transparent cursor-not-allowed"
              />
            </template>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="py-20 text-center max-w-md mx-auto">
        <div class="w-20 h-20 rounded-3xl bg-amber-50 dark:bg-slate-800 border border-amber-200/50 dark:border-slate-700 flex items-center justify-center text-amber-500 mx-auto mb-5 shadow-sm">
          <svg class="w-10 h-10 fill-amber-500/20 stroke-amber-500" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tidak ada bookmark ditemukan</h3>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
          {{ searchQuery || selectedTipe ? 'Tidak ada artikel tersimpan yang sesuai dengan kata kunci atau filter pencarian Anda.' : 'Anda belum menyimpan artikel atau karya ilmiah apapun. Tandai artikel yang menarik dengan menekan tombol pita simpan.' }}
        </p>
        <div class="mt-6">
          <Link 
            href="/kategori" 
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#2563eb] hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Jelajahi Repositori Pengetahuan
          </Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import KnowledgeCard from '../../Components/KnowledgeCard.vue';

const props = defineProps({
  knowledges: {
    type: Object,
    default: () => ({ data: [], links: [] })
  },
  filters: {
    type: Object,
    default: () => ({ q: '', tipe: '' })
  }
});

const searchQuery = ref(props.filters?.q || '');
const selectedTipe = ref(props.filters?.tipe || '');
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

const chooseTipe = (tipe) => {
  selectedTipe.value = tipe;
  isDropdownOpen.value = false;
  applyFilters();
};

const handleOutsideClick = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isDropdownOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleOutsideClick);
});

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick);
});

const applyFilters = () => {
  router.get('/bookmarks', {
    q: searchQuery.value,
    tipe: selectedTipe.value,
  }, {
    preserveState: true,
    replace: true,
  });
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedTipe.value = '';
  router.get('/bookmarks', {}, {
    preserveState: true,
    replace: true,
  });
};
</script>

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
                <span class="text-white font-medium ml-1">Kategori & Repositori</span>
              </div>
            </li>
          </ol>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2 tracking-tight">Katalog Repositori Pengetahuan</h1>
        <p class="text-base sm:text-lg text-white/80">Telusuri seluruh koleksi karya tulis, kajian ilmiah, dan aset multimedia MojoPedia.</p>
      </div>
    </section>

    <!-- Search & Filter Sticky Bar -->
    <div class="sticky top-16 z-30 bg-white dark:bg-slate-900 shadow-sm border-b border-slate-200 dark:border-slate-800 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <form @submit.prevent="submitSearch" class="flex flex-col md:flex-row gap-3">
          <!-- Search Input -->
          <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input 
              v-model="form.q" 
              type="text" 
              placeholder="Cari berdasarkan judul, deskripsi, label, penulis, atau instansi..." 
              class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition"
            />
          </div>

          <!-- Filters Row with Animated Custom Dropdowns -->
          <div class="flex gap-2 flex-wrap items-center">
            <!-- Tipe Custom Dropdown -->
            <div class="relative w-full sm:w-auto" ref="tipeDropdownRef">
              <button 
                type="button" 
                @click.stop="toggleTipe"
                class="w-full sm:w-auto min-w-[140px] flex items-center justify-between gap-2.5 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition cursor-pointer select-none"
              >
                <span class="truncate font-medium">{{ selectedTipeLabel }}</span>
                <svg 
                  class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-300 shrink-0 ml-1.5" 
                  :class="{ 'rotate-180 text-[#2563eb] dark:text-blue-400': isTipeOpen }" 
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div 
                v-show="isTipeOpen" 
                class="absolute left-0 z-40 mt-1.5 w-full min-w-[150px] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 focus:outline-none"
              >
                <button 
                  type="button" 
                  @click="selectTipe('')"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="!form.tipe ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>Semua Tipe</span>
                  <svg v-if="!form.tipe" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
                <button 
                  v-for="t in ['Teks', 'Video', 'Gambar', 'Audio']" 
                  :key="t"
                  type="button" 
                  @click="selectTipe(t)"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="form.tipe === t ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>{{ t }}</span>
                  <svg v-if="form.tipe === t" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
              </div>
            </div>

            <!-- Kategori Custom Dropdown -->
            <div class="relative w-full sm:w-auto" ref="kategoriDropdownRef">
              <button 
                type="button" 
                @click.stop="toggleKategori"
                class="w-full sm:w-auto min-w-[170px] max-w-[240px] flex items-center justify-between gap-2.5 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition cursor-pointer select-none"
              >
                <span class="truncate font-medium">{{ selectedKategoriLabel }}</span>
                <svg 
                  class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-300 shrink-0 ml-1.5" 
                  :class="{ 'rotate-180 text-[#2563eb] dark:text-blue-400': isKategoriOpen }" 
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div 
                v-show="isKategoriOpen" 
                class="absolute left-0 z-40 mt-1.5 w-full min-w-[220px] max-w-[300px] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 max-h-72 overflow-y-auto focus:outline-none"
              >
                <button 
                  type="button" 
                  @click="selectKategori('')"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="!form.kategori ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>Semua Kategori</span>
                  <svg v-if="!form.kategori" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
                <button 
                  v-for="cat in categories" 
                  :key="cat.id"
                  type="button" 
                  @click="selectKategori(cat.id)"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="String(form.kategori) === String(cat.id) ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span class="truncate">{{ cat.nama_kategori }}</span>
                  <svg v-if="String(form.kategori) === String(cat.id)" class="w-4 h-4 text-[#2563eb] shrink-0 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
              </div>
            </div>

            <!-- Sort Custom Dropdown -->
            <div class="relative w-full sm:w-auto" ref="sortDropdownRef">
              <button 
                type="button" 
                @click.stop="toggleSort"
                class="w-full sm:w-auto min-w-[130px] flex items-center justify-between gap-2.5 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition cursor-pointer select-none"
              >
                <span class="truncate font-medium">{{ selectedSortLabel }}</span>
                <svg 
                  class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-300 shrink-0 ml-1.5" 
                  :class="{ 'rotate-180 text-[#2563eb] dark:text-blue-400': isSortOpen }" 
                  fill="none" 
                  viewBox="0 0 24 24" 
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div 
                v-show="isSortOpen" 
                class="absolute left-0 z-40 mt-1.5 w-full min-w-[150px] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 focus:outline-none"
              >
                <button 
                  type="button" 
                  @click="selectSort('terbaru')"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="form.sort === 'terbaru' ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>Terbaru</span>
                  <svg v-if="form.sort === 'terbaru'" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
                <button 
                  type="button" 
                  @click="selectSort('terpopuler')"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="form.sort === 'terpopuler' ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>Terpopuler</span>
                  <svg v-if="form.sort === 'terpopuler'" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
                <button 
                  type="button" 
                  @click="selectSort('az')"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="form.sort === 'az' ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>A — Z</span>
                  <svg v-if="form.sort === 'az'" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
                <button 
                  type="button" 
                  @click="selectSort('za')"
                  class="w-full text-left px-3.5 py-2 text-xs sm:text-sm transition flex items-center justify-between cursor-pointer"
                  :class="form.sort === 'za' ? 'bg-blue-50 dark:bg-slate-700 text-[#2563eb] dark:text-blue-400 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                >
                  <span>Z — A</span>
                  <svg v-if="form.sort === 'za'" class="w-4 h-4 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
              </div>
            </div>

            <!-- Cari Button -->
            <button 
              type="submit" 
              class="btn-search-pulse bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-6 py-2.5 font-semibold rounded-xl shadow-sm text-sm transition cursor-pointer focus:outline-none shrink-0"
            >
              Cari
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Content & Sidebar Section -->
    <section class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Results Column -->
        <div class="flex-grow min-w-0">
          <!-- Summary Header -->
          <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
              <template v-if="filters.q">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                  Hasil untuk "<span class="text-[#2563eb]">{{ filters.q }}</span>"
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                  Ditemukan {{ knowledge?.total || 0 }} pengetahuan
                </p>
              </template>
              <template v-else>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Seluruh Pengetahuan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                  Menampilkan {{ knowledge?.total || 0 }} item
                </p>
              </template>
            </div>

            <!-- Reset Filter Button (HANYA MUNCUL KETIKA PENGGUNA MEMANG MENGGUNAKAN FILTER/PILIHAN) -->
            <button 
              v-if="hasActiveFilters"
              @click="resetAllFilters" 
              type="button"
              class="text-sm text-red-600 hover:text-red-500 font-medium flex items-center gap-1.5 transition cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              <span>Reset Filter</span>
            </button>
          </div>

          <!-- Active Filter Badges (Chips) -->
          <div v-if="hasActiveFilterChips" class="flex flex-wrap gap-2 mb-6">
            <!-- Query Chip -->
            <span 
              v-if="filters.q" 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
            >
              <span>Pencarian: "{{ filters.q }}"</span>
              <button @click="removeFilter('q')" class="hover:text-blue-900 dark:hover:text-white transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>

            <!-- Tipe Chip -->
            <span 
              v-if="filters.tipe" 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
            >
              <span>Tipe: {{ filters.tipe }}</span>
              <button @click="removeFilter('tipe')" class="hover:text-blue-900 dark:hover:text-white transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>

            <!-- Kategori Chip -->
            <span 
              v-if="activeCategoryName" 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700"
            >
              <span>Kategori: {{ activeCategoryName }}</span>
              <button @click="removeFilter('kategori')" class="hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>

            <!-- Label Chip -->
            <span 
              v-if="filters.label" 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"
            >
              <span>Tagline: {{ filters.label }}</span>
              <button @click="removeFilter('label')" class="hover:text-emerald-900 dark:hover:text-white transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>

            <!-- Sort Chip (hanya jika sort bukan 'terbaru') -->
            <span 
              v-if="filters.sort && filters.sort !== 'terbaru'" 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800"
            >
              <span>Urutan: {{ selectedSortLabel }}</span>
              <button @click="removeFilter('sort')" class="hover:text-indigo-900 dark:hover:text-white transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </span>
          </div>

          <!-- Knowledge Cards Grid (Menggunakan KnowledgeCard dengan Thumbnail) -->
          <div v-if="knowledge?.data && knowledge.data.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              <KnowledgeCard 
                v-for="item in knowledge.data" 
                :key="item.id" 
                :item="item" 
              />
            </div>

            <!-- Pagination -->
            <div v-if="knowledge.links && knowledge.links.length > 3" class="mt-10 flex justify-center items-center gap-1.5 flex-wrap">
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
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <svg class="mx-auto h-16 w-16 text-slate-300 dark:text-slate-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Tidak ada hasil ditemukan</h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6 text-sm leading-relaxed">
              <template v-if="filters.q">
                Tidak ditemukan pengetahuan yang cocok dengan pencarian "<strong>{{ filters.q }}</strong>". Coba gunakan kata kunci lain atau perluas filter Anda.
              </template>
              <template v-else>
                Belum ada pengetahuan yang sesuai dengan kombinasi filter yang Anda pilih.
              </template>
            </p>
            <button 
              @click="resetAllFilters" 
              type="button" 
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <span>Reset Pencarian</span>
            </button>
          </div>
        </div>

        <!-- Right Sidebar Filters -->
        <aside class="w-full lg:w-80 shrink-0 space-y-6">
          <!-- Kategori Card -->
          <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-800 dark:text-white mb-3.5 flex items-center gap-2 text-sm sm:text-base">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              <span>Kategori</span>
            </h3>
            <ul class="space-y-1">
              <li v-for="cat in categories" :key="cat.id">
                <button 
                  type="button"
                  @click="setFilter('kategori', String(form.kategori) === String(cat.id) ? '' : cat.id)" 
                  class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs sm:text-sm transition cursor-pointer text-left"
                  :class="String(form.kategori) === String(cat.id) 
                    ? 'bg-blue-50 text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-300 font-semibold' 
                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                >
                  <span class="truncate">{{ cat.nama_kategori }}</span>
                  <span class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 px-2 py-0.5 rounded-full shrink-0 ml-2">
                    {{ cat.knowledge_count || 0 }}
                  </span>
                </button>
              </li>
            </ul>
          </div>

          <!-- Tipe Konten Card -->
          <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-800 dark:text-white mb-3.5 flex items-center gap-2 text-sm sm:text-base">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              <span>Tipe Konten</span>
            </h3>
            <div class="space-y-1">
              <button 
                v-for="t in ['Teks', 'Video', 'Gambar', 'Audio']" 
                :key="t"
                type="button"
                @click="setFilter('tipe', form.tipe === t ? '' : t)" 
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs sm:text-sm transition cursor-pointer text-left"
                :class="form.tipe === t 
                  ? 'bg-blue-50 text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-300 font-semibold' 
                  : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
              >
                <svg v-if="t === 'Teks'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg v-else-if="t === 'Video'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else-if="t === 'Gambar'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <svg v-else class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10l12-3m0 0l-3 12m3-12L9 19m0 0l-3-2m3 2l3-12" />
                </svg>
                <span>{{ t }}</span>
              </button>
            </div>
          </div>

          <!-- Tag Populer Card -->
          <div v-if="tags && tags.length > 0" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-800 dark:text-white mb-3.5 flex items-center gap-2 text-sm sm:text-base">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <span>Tag Populer</span>
            </h3>
            <div class="flex flex-wrap gap-2">
              <button 
                v-for="tag in tags" 
                :key="tag.id"
                type="button"
                @click="setFilter('label', form.label === tag.nama_label ? '' : tag.nama_label)" 
                class="text-xs px-3 py-1 rounded-full transition cursor-pointer"
                :class="form.label === tag.nama_label 
                  ? 'bg-[#2563eb] text-white font-semibold shadow-sm' 
                  : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-blue-50 hover:text-[#2563eb]'"
              >
                {{ tag.nama_label }}
                <span class="text-[10px] opacity-75">({{ tag.knowledge_count }})</span>
              </button>
            </div>
          </div>
        </aside>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
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
    default: () => ({ data: [], links: [] }),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

// Dropdown open states
const isTipeOpen = ref(false);
const isKategoriOpen = ref(false);
const isSortOpen = ref(false);

const tipeDropdownRef = ref(null);
const kategoriDropdownRef = ref(null);
const sortDropdownRef = ref(null);

const toggleTipe = () => {
  isTipeOpen.value = !isTipeOpen.value;
  isKategoriOpen.value = false;
  isSortOpen.value = false;
};

const toggleKategori = () => {
  isKategoriOpen.value = !isKategoriOpen.value;
  isTipeOpen.value = false;
  isSortOpen.value = false;
};

const toggleSort = () => {
  isSortOpen.value = !isSortOpen.value;
  isTipeOpen.value = false;
  isKategoriOpen.value = false;
};

const closeAllDropdowns = () => {
  isTipeOpen.value = false;
  isKategoriOpen.value = false;
  isSortOpen.value = false;
};

const onDocumentClick = (e) => {
  if (tipeDropdownRef.value && !tipeDropdownRef.value.contains(e.target)) {
    isTipeOpen.value = false;
  }
  if (kategoriDropdownRef.value && !kategoriDropdownRef.value.contains(e.target)) {
    isKategoriOpen.value = false;
  }
  if (sortDropdownRef.value && !sortDropdownRef.value.contains(e.target)) {
    isSortOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', onDocumentClick);
});

onUnmounted(() => {
  document.removeEventListener('click', onDocumentClick);
});

const form = reactive({
  q: props.filters?.q || '',
  tipe: props.filters?.tipe || '',
  kategori: props.filters?.kategori || '',
  sort: props.filters?.sort || 'terbaru',
  label: props.filters?.label || '',
  instansi: props.filters?.instansi || '',
});

// Update internal form when props change
watch(
  () => props.filters,
  (newFilters) => {
    form.q = newFilters?.q || '';
    form.tipe = newFilters?.tipe || '';
    form.kategori = newFilters?.kategori || '';
    form.sort = newFilters?.sort || 'terbaru';
    form.label = newFilters?.label || '';
    form.instansi = newFilters?.instansi || '';
  },
  { deep: true }
);

// Labels
const selectedTipeLabel = computed(() => {
  return form.tipe || 'Semua Tipe';
});

const selectedKategoriLabel = computed(() => {
  if (!form.kategori) return 'Semua Kategori';
  const cat = props.categories.find(c => String(c.id) === String(form.kategori));
  return cat ? cat.nama_kategori : 'Semua Kategori';
});

const selectedSortLabel = computed(() => {
  switch (form.sort) {
    case 'terpopuler': return 'Terpopuler';
    case 'az': return 'A — Z';
    case 'za': return 'Z — A';
    case 'terbaru':
    default:
      return 'Terbaru';
  }
});

const activeCategoryName = computed(() => {
  if (!props.filters.kategori) return null;
  const cat = props.categories.find(c => String(c.id) === String(props.filters.kategori));
  return cat ? cat.nama_kategori : null;
});

// Tombol Reset Filter HANYA MUNCUL jika pengguna memang menggunakan filter / pilihan
const hasActiveFilters = computed(() => {
  const hasQ = !!(props.filters?.q && String(props.filters.q).trim());
  const hasTipe = !!(props.filters?.tipe && String(props.filters.tipe).trim());
  const hasKategori = !!(props.filters?.kategori && String(props.filters.kategori).trim());
  const hasLabel = !!(props.filters?.label && String(props.filters.label).trim());
  const hasInstansi = !!(props.filters?.instansi && String(props.filters.instansi).trim());
  const hasSort = !!(props.filters?.sort && props.filters.sort !== 'terbaru');

  return hasQ || hasTipe || hasKategori || hasLabel || hasInstansi || hasSort;
});

const hasActiveFilterChips = computed(() => {
  return !!(
    (props.filters?.q && String(props.filters.q).trim()) ||
    (props.filters?.tipe && String(props.filters.tipe).trim()) ||
    (props.filters?.kategori && String(props.filters.kategori).trim()) ||
    (props.filters?.label && String(props.filters.label).trim()) ||
    (props.filters?.sort && props.filters.sort !== 'terbaru')
  );
});

const submitSearch = () => {
  closeAllDropdowns();
  const params = {};
  if (form.q && form.q.trim()) params.q = form.q.trim();
  if (form.tipe) params.tipe = form.tipe;
  if (form.kategori) params.kategori = form.kategori;
  if (form.sort && form.sort !== 'terbaru') params.sort = form.sort;
  if (form.label) params.label = form.label;
  if (form.instansi) params.instansi = form.instansi;

  router.get('/kategori', params, {
    preserveState: true,
    preserveScroll: true,
  });
};

const selectTipe = (value) => {
  form.tipe = value;
  isTipeOpen.value = false;
  submitSearch();
};

const selectKategori = (value) => {
  form.kategori = value;
  isKategoriOpen.value = false;
  submitSearch();
};

const selectSort = (value) => {
  form.sort = value;
  isSortOpen.value = false;
  submitSearch();
};

const setFilter = (key, value) => {
  form[key] = value;
  submitSearch();
};

const removeFilter = (key) => {
  if (key === 'sort') {
    form.sort = 'terbaru';
  } else {
    form[key] = '';
  }
  submitSearch();
};

const resetAllFilters = () => {
  form.q = '';
  form.tipe = '';
  form.kategori = '';
  form.sort = 'terbaru';
  form.label = '';
  form.instansi = '';
  closeAllDropdowns();

  router.get('/kategori', {}, {
    preserveScroll: true,
  });
};
</script>

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
                <span class="text-white font-medium ml-1">Pencarian</span>
              </div>
            </li>
          </ol>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2 tracking-tight">Pencarian Pengetahuan</h1>
        <p class="text-base sm:text-lg text-white/80">Temukan dokumen, artikel, panduan, dan diskusi di seluruh repositori MojoPedia.</p>
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

          <!-- Filters Row -->
          <div class="flex gap-2 flex-wrap items-center">
            <!-- Tipe Select -->
            <select 
              v-model="form.tipe" 
              @change="submitSearch"
              class="px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition cursor-pointer"
            >
              <option value="">Semua Tipe</option>
              <option value="Teks">Teks</option>
              <option value="Video">Video</option>
              <option value="Gambar">Gambar</option>
              <option value="Audio">Audio</option>
            </select>

            <!-- Kategori Select -->
            <select 
              v-model="form.kategori" 
              @change="submitSearch"
              class="px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition cursor-pointer max-w-[190px] truncate"
            >
              <option value="">Semua Kategori</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nama_kategori }}</option>
            </select>

            <!-- Sort Select -->
            <select 
              v-model="form.sort" 
              @change="submitSearch"
              class="px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm focus:border-[#2563eb] focus:ring-2 focus:ring-[#2563eb]/20 transition cursor-pointer"
            >
              <option value="terbaru">Terbaru</option>
              <option value="terpopuler">Terpopuler</option>
              <option value="az">A — Z</option>
              <option value="za">Z — A</option>
            </select>

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
                  Ditemukan {{ results?.total || 0 }} pengetahuan
                  <span v-if="forumResults?.length > 0">
                    dan {{ forumResults.length }} topik forum
                  </span>
                </p>
              </template>
              <template v-else>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Seluruh Pengetahuan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                  Menampilkan {{ results?.total || 0 }} item
                </p>
              </template>
            </div>

            <!-- Reset Filter Button -->
            <button 
              v-if="hasActiveFilters"
              @click="resetAllFilters" 
              class="text-sm text-[#2563eb] hover:text-[#1d4ed8] font-medium flex items-center gap-1.5 transition cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              <span>Reset Filter</span>
            </button>
          </div>

          <!-- Active Filter Badges (Chips) -->
          <div v-if="hasActiveFilterChips" class="flex flex-wrap gap-2 mb-6">
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
          </div>

          <!-- Forum Results Section (if query matched forum threads) -->
          <div v-if="filters.q && forumResults && forumResults.length > 0" class="mb-8">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
              </svg>
              Hasil dari Forum Diskusi
            </h3>
            <div class="space-y-3">
              <Link 
                v-for="thread in forumResults" 
                :key="thread.id" 
                :href="`/forum/${thread.id}`"
                class="block bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 hover:border-[#2563eb]/60 transition shadow-sm"
              >
                <h4 class="font-semibold text-slate-800 dark:text-white hover:text-[#2563eb] transition mb-1">{{ thread.judul }}</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                  {{ truncateText(stripHtml(thread.konten), 120) }}
                </p>
                <div class="flex items-center gap-4 mt-2 text-xs text-slate-400">
                  <span>{{ thread.user?.name || 'Anonymous' }}</span>
                  <span>{{ thread.replies_count || 0 }} balasan</span>
                  <span>{{ timeAgo(thread.created_at) }}</span>
                </div>
              </Link>
            </div>
            <hr class="my-6 border-slate-200 dark:border-slate-800">
          </div>

          <!-- Knowledge Cards Grid -->
          <div v-if="results?.data && results.data.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              <div 
                v-for="item in results.data" 
                :key="item.id" 
                class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm hover:shadow-lg hover:border-[#2563eb]/50 transition-all duration-300 flex flex-col justify-between"
              >
                <!-- Card Header (Tipe Badge & Views) -->
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                  <span 
                    :class="getTipeBadgeClass(item.tipe)" 
                    class="text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1.5"
                  >
                    <!-- Tipe Icon -->
                    <svg v-if="item.tipe === 'Video'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else-if="item.tipe === 'Audio'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                    <svg v-else-if="item.tipe === 'Gambar'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ item.tipe || 'Teks' }}
                  </span>

                  <!-- Views Count -->
                  <span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ item.views_count || 0 }}
                  </span>
                </div>

                <!-- Card Body -->
                <div class="px-5 pb-3 flex-grow">
                  <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-[#2563eb] transition line-clamp-2 leading-snug mb-1.5">
                    <a :href="`/knowledge/${item.id}`">
                      {{ item.judul }}
                    </a>
                  </h3>
                  <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-3 leading-relaxed">
                    {{ truncateText(stripHtml(item.deskripsi), 95) }}
                  </p>

                  <!-- Tags List -->
                  <div v-if="item.tags && item.tags.length > 0" class="flex flex-wrap gap-1.5 pt-1">
                    <button 
                      v-for="tag in item.tags.slice(0, 3)" 
                      :key="tag.id"
                      @click="setFilter('label', tag.nama_label)"
                      type="button"
                      class="text-[11px] px-2 py-0.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded transition cursor-pointer"
                    >
                      {{ tag.nama_label }}
                    </button>
                    <span 
                      v-if="item.tags.length > 3" 
                      class="text-[11px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-400 rounded"
                    >
                      +{{ item.tags.length - 3 }}
                    </span>
                  </div>
                </div>

                <!-- Card Footer (Author & Date) -->
                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                  <div class="flex items-center gap-2 min-w-0">
                    <!-- User Avatar Circle -->
                    <div class="w-6 h-6 rounded-full bg-blue-100 text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 flex items-center justify-center font-bold text-[10px] shrink-0 overflow-hidden">
                      <img 
                        v-if="item.user?.foto_profil" 
                        :src="`/storage/${item.user.foto_profil}`" 
                        :alt="item.user.name" 
                        class="w-full h-full object-cover"
                      />
                      <span v-else>{{ item.user?.name ? item.user.name.charAt(0).toUpperCase() : 'U' }}</span>
                    </div>
                    <span class="truncate max-w-[130px] font-medium text-slate-700 dark:text-slate-300">
                      {{ item.user?.name || 'Anonim' }}
                    </span>
                  </div>
                  <span class="shrink-0 text-slate-400">{{ timeAgo(item.created_at) }}</span>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="results.links && results.links.length > 3" class="mt-10 flex justify-center items-center gap-1.5 flex-wrap">
              <template v-for="(link, index) in results.links" :key="index">
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
                  @click="setFilter('kategori', filters.kategori == cat.id ? '' : cat.id)" 
                  class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs sm:text-sm transition cursor-pointer text-left"
                  :class="filters.kategori == cat.id 
                    ? 'bg-blue-50 text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-300 font-semibold' 
                    : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                >
                  <span class="truncate">{{ cat.nama_kategori }}</span>
                  <span class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 px-2 py-0.5 rounded-full">
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
                @click="setFilter('tipe', filters.tipe === t ? '' : t)" 
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs sm:text-sm transition cursor-pointer text-left"
                :class="filters.tipe === t 
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
                <span>{{ t }}</span>
              </button>
            </div>
          </div>

          <!-- Tag Populer Card -->
          <div v-if="popularTags && popularTags.length > 0" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <h3 class="font-bold text-slate-800 dark:text-white mb-3.5 flex items-center gap-2 text-sm sm:text-base">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <span>Tag Populer</span>
            </h3>
            <div class="flex flex-wrap gap-2">
              <button 
                v-for="tag in popularTags" 
                :key="tag.id"
                type="button"
                @click="setFilter('label', filters.label === tag.nama_label ? '' : tag.nama_label)" 
                class="text-xs px-3 py-1 rounded-full transition cursor-pointer"
                :class="filters.label === tag.nama_label 
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
import { reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const props = defineProps({
  results: Object,
  categories: Array,
  popularTags: Array,
  forumResults: Array,
  filters: {
    type: Object,
    default: () => ({})
  }
});

const form = reactive({
  q: props.filters?.q || '',
  tipe: props.filters?.tipe || '',
  kategori: props.filters?.kategori || '',
  label: props.filters?.label || '',
  instansi: props.filters?.instansi || '',
  sort: props.filters?.sort || 'terbaru',
});

const hasActiveFilters = computed(() => {
  return !!(form.q || form.tipe || form.kategori || form.label || form.instansi || (form.sort && form.sort !== 'terbaru'));
});

const hasActiveFilterChips = computed(() => {
  return !!(props.filters?.tipe || props.filters?.kategori || props.filters?.label);
});

const activeCategoryName = computed(() => {
  if (!props.filters?.kategori || !props.categories) return null;
  const found = props.categories.find(c => String(c.id) === String(props.filters.kategori));
  return found ? found.nama_kategori : null;
});

const submitSearch = () => {
  const params = {};
  if (form.q) params.q = form.q;
  if (form.tipe) params.tipe = form.tipe;
  if (form.kategori) params.kategori = form.kategori;
  if (form.label) params.label = form.label;
  if (form.instansi) params.instansi = form.instansi;
  if (form.sort && form.sort !== 'terbaru') params.sort = form.sort;

  router.get('/cari', params, {
    preserveState: true,
    preserveScroll: true,
  });
};

const setFilter = (key, value) => {
  form[key] = value;
  submitSearch();
};

const removeFilter = (key) => {
  form[key] = '';
  submitSearch();
};

const resetAllFilters = () => {
  form.q = '';
  form.tipe = '';
  form.kategori = '';
  form.label = '';
  form.instansi = '';
  form.sort = 'terbaru';
  router.get('/cari', {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getTipeBadgeClass = (tipe) => {
  switch (tipe) {
    case 'Video':
      return 'bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-300 border border-red-200/60 dark:border-red-900/60';
    case 'Audio':
      return 'bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/60 dark:border-purple-900/60';
    case 'Gambar':
      return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900/60';
    default:
      return 'bg-blue-50 text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/60 dark:border-blue-900/60';
  }
};

const stripHtml = (html) => {
  if (!html) return '';
  return html.replace(/<[^>]*>/g, '');
};

const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

const timeAgo = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const now = new Date();
  const diffInSeconds = Math.floor((now - date) / 1000);

  if (diffInSeconds < 60) return 'Baru saja';
  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60) return `${diffInMinutes} menit yang lalu`;
  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) return `${diffInHours} jam yang lalu`;
  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 7) return `${diffInDays} hari yang lalu`;
  const diffInWeeks = Math.floor(diffInDays / 7);
  if (diffInWeeks < 4) return `${diffInWeeks} minggu yang lalu`;
  const diffInMonths = Math.floor(diffInDays / 30);
  if (diffInMonths < 12) return `${diffInMonths} bulan yang lalu`;
  const diffInYears = Math.floor(diffInDays / 365);
  return `${diffInYears} tahun yang lalu`;
};
</script>

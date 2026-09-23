<template>
  <PublicLayout>
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      <!-- Breadcrumbs -->
      <nav class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-6 flex-wrap" aria-label="Breadcrumb">
        <Link href="/" class="hover:text-[#2563eb] dark:hover:text-blue-400 transition font-medium">Beranda</Link>
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-500 dark:text-slate-400">Profil Pengguna</span>
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-800 dark:text-slate-200 font-semibold line-clamp-1">{{ user.name }}</span>
      </nav>

      <!-- Main Profile Card Header -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-8">

        <!-- Banner Cover -->
        <div class="h-36 sm:h-48 relative overflow-hidden profile-animated-banner"></div>

        <!-- User Info Section -->
        <div class="px-6 sm:px-8 pb-8 pt-0 relative">
          <!-- Top Row: Avatar (overlapping banner) & Action Buttons (mepet di bawah banner) -->
          <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4 mb-4">
            <!-- Avatar (overlaps banner) -->
            <div class="relative shrink-0 -mt-16 sm:-mt-20">
              <img
                v-if="user.foto_profil"
                :src="`/storage/${user.foto_profil}`"
                :alt="user.name"
                class="w-28 h-28 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-white dark:border-slate-900 shadow-xl bg-slate-100 dark:bg-slate-800"
              />
              <div
                v-else
                class="w-28 h-28 sm:w-32 sm:h-32 rounded-full border-4 border-white dark:border-slate-900 shadow-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-extrabold text-4xl"
              >
                {{ userInitials }}
              </div>
              <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full" title="Akun Aktif"></span>
            </div>

            <!-- Tombol Aksi Profil (Mepet dengan banner namun di luar/bawah banner) -->
            <div class="mt-2 sm:mt-3 shrink-0 w-full sm:w-auto flex flex-wrap items-center justify-center sm:justify-end gap-2.5">
              <!-- Tombol Pengaturan Profil (Hanya tampil jika user login melihat profil miliknya sendiri) -->
              <Link
                v-if="isOwner"
                href="/profile"
                class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 shadow-sm group"
              >
                <svg class="w-4 h-4 text-slate-500 group-hover:text-[#2563eb] dark:group-hover:text-blue-400 transition-all duration-500 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Pengaturan Profil</span>
              </Link>

              <!-- Tombol Bagikan Profil -->
              <button
                type="button"
                @click="shareProfile"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-sm text-white"
                :class="isCopied ? 'bg-emerald-600' : 'bg-[#2563eb] hover:bg-[#1d4ed8] active:bg-[#1e40af]'"
              >
                <template v-if="!isCopied">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                  </svg>
                  <span>Bagikan Profil</span>
                </template>
                <template v-else>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span>Tautan Berhasil Disalin!</span>
                </template>
              </button>
            </div>
          </div>

          <!-- Identitas (di bawah avatar dan banner) -->
          <div class="space-y-1.5 text-center sm:text-left mb-6">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
              <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                {{ user.name }}
              </h1>
              <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                {{ user.role || 'Anggota' }}
              </span>
            </div>

            <p v-if="user.pekerjaan || user.instansi" class="text-sm font-medium text-slate-600 dark:text-slate-300">
              {{ user.pekerjaan || 'Anggota' }} &bull; {{ user.instansi || 'Badan Riset dan Inovasi Nasional (BRIN)' }}
            </p>

            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-slate-500 dark:text-slate-400 pt-1">
              <span v-if="user.alamat" class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ user.alamat }}</span>
              </span>

              <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Bergabung {{ formattedJoinDate }}</span>
              </span>
            </div>
          </div>

          <!-- Statistik Ringkas Pengguna -->
          <div class="grid grid-cols-3 gap-3 sm:gap-6 p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800">
            <div class="text-center">
              <div class="text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ totalKnowledge }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Pengetahuan Terbit</div>
            </div>
            <div class="text-center border-x border-slate-200 dark:border-slate-700">
              <div class="text-xl sm:text-3xl font-extrabold text-[#2563eb] dark:text-blue-400">{{ Number(totalViews).toLocaleString('id-ID') }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Total Pembaca (Views)</div>
            </div>
            <div class="text-center">
              <div class="text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ totalThreads }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Diskusi Forum</div>
            </div>
          </div>

        </div>
      </div>

      <!-- Content Tabs Section -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">

        <!-- Tabs Header -->
        <div class="flex border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 overflow-x-auto">
          <button
            type="button"
            @click="activeTab = 'pengetahuan'"
            :class="activeTab === 'pengetahuan' ? 'text-[#2563eb] dark:text-blue-400 border-[#2563eb] dark:border-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-200 font-medium'"
            class="py-4 px-4 sm:px-6 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Pengetahuan Terbit ({{ totalKnowledge }})</span>
          </button>

          <button
            type="button"
            @click="activeTab = 'forum'"
            :class="activeTab === 'forum' ? 'text-[#2563eb] dark:text-blue-400 border-[#2563eb] dark:border-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-200 font-medium'"
            class="py-4 px-4 sm:px-6 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
            </svg>
            <span>Diskusi Forum ({{ totalThreads }})</span>
          </button>

          <button
            type="button"
            @click="activeTab = 'biodata'"
            :class="activeTab === 'biodata' ? 'text-[#2563eb] dark:text-blue-400 border-[#2563eb] dark:border-blue-400 font-bold' : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-200 font-medium'"
            class="py-4 px-4 sm:px-6 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Informasi & Instansi</span>
          </button>
        </div>

        <!-- Tab 1: Pengetahuan Terbit -->
        <div v-show="activeTab === 'pengetahuan'" class="p-6 sm:p-8">
          <div v-if="knowledgeList.data && knowledgeList.data.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="item in knowledgeList.data"
                :key="item.id"
                class="bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden card-hover flex flex-col h-full"
              >
                <div class="p-5 flex-grow flex flex-col justify-between">
                  <div>
                    <!-- Header Tipe & Waktu -->
                    <div class="flex justify-between items-center mb-3">
                      <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full"
                        :class="getTypeBadgeClass(item.tipe)"
                      >
                        {{ item.tipe }}
                      </span>
                      <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ timeAgo(item.created_at) }}
                      </span>
                    </div>

                    <!-- Konten Utama: Judul & Deskripsi di kiri, Small Thumbnail Box di kanan mentok -->
                    <div class="flex items-start justify-between gap-4 mt-2 mb-2">
                      <div class="flex-grow min-w-0">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2 line-clamp-2 leading-snug">
                          <a :href="`/knowledge/${item.id}`" class="hover:text-[#2563eb] dark:hover:text-blue-400 transition">
                            {{ item.judul }}
                          </a>
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-2">
                          {{ truncate(item.deskripsi, 65) }}
                        </p>
                      </div>

                      <!-- Box Thumbnail Kecil -->
                      <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 shadow-sm flex items-center justify-center">
                        <img
                          v-if="isImageFile(item.file_path)"
                          :src="`/storage/${item.file_path}`"
                          :alt="item.judul"
                          class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center text-slate-400 dark:text-slate-400 bg-slate-100 dark:bg-slate-900">
                          <svg v-if="item.tipe === 'Video'" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                          <svg v-else-if="item.tipe === 'Gambar'" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                          <svg v-else-if="item.tipe === 'Audio'" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                          <svg v-else class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Footer Kategori & Views -->
                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-700/80 flex justify-between items-center text-xs text-slate-500 dark:text-slate-400">
                  <div class="flex items-center gap-1 truncate max-w-[60%]">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                    <span class="truncate">{{ item.category?.nama_kategori || 'Umum' }}</span>
                  </div>
                  <div class="flex items-center gap-3 shrink-0">
                    <span class="flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                      {{ item.views_count || 0 }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="knowledgeList.links && knowledgeList.links.length > 3" class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-center gap-1">
              <Component
                :is="link.url ? Link : 'span'"
                v-for="(link, i) in knowledgeList.links"
                :key="i"
                :href="link.url"
                v-html="link.label"
                :class="[
                  'px-3.5 py-2 text-xs font-semibold rounded-lg transition',
                  link.active 
                    ? 'bg-[#2563eb] text-white shadow-sm' 
                    : link.url 
                      ? 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' 
                      : 'text-slate-300 dark:text-slate-600 cursor-not-allowed'
                ]"
              />
            </div>
          </div>

          <div v-else class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-50 dark:bg-slate-800 text-blue-500 dark:text-blue-400 flex items-center justify-center">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum ada karya pengetahuan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
              Pengguna ini belum memiliki pengetahuan yang diterbitkan secara publik.
            </p>
          </div>
        </div>

        <!-- Tab 2: Diskusi Forum -->
        <div v-show="activeTab === 'forum'" class="p-6 sm:p-8">
          <div v-if="forumThreads && forumThreads.length > 0" class="space-y-4">
            <Link
              v-for="thread in forumThreads"
              :key="thread.id"
              :href="`/forum/${thread.id}`"
              class="block p-5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md bg-slate-50/50 dark:bg-slate-800/40 transition group"
            >
              <div class="flex items-start justify-between gap-4">
                <div>
                  <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span v-if="thread.category" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                      {{ thread.category.nama_kategori }}
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">
                      {{ timeAgo(thread.created_at) }}
                    </span>
                  </div>
                  <h4 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-[#2563eb] dark:group-hover:text-blue-400 transition">
                    {{ thread.judul }}
                  </h4>
                  <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 line-clamp-2 mt-1">
                    {{ truncate(thread.konten, 160) }}
                  </p>
                </div>
                <div class="shrink-0 flex items-center gap-1 text-xs font-semibold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  <span>{{ thread.replies_count || 0 }} balasan</span>
                </div>
              </div>
            </Link>
          </div>

          <div v-else class="py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-indigo-50 dark:bg-slate-800 text-indigo-500 dark:text-indigo-400 flex items-center justify-center">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Belum ada diskusi forum</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
              Pengguna ini belum membuat topik diskusi di forum.
            </p>
          </div>
        </div>

        <!-- Tab 3: Informasi & Instansi -->
        <div v-show="activeTab === 'biodata'" class="p-6 sm:p-8">
          <div class="max-w-2xl mx-auto bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200 dark:border-slate-700/60">
            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Informasi Publik Pengguna
            </h3>
            <dl class="divide-y divide-slate-200 dark:divide-slate-700 text-sm">
              <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="font-semibold text-slate-500 dark:text-slate-400">Nama Lengkap</dt>
                <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ user.name }}</dd>
              </div>
              <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="font-semibold text-slate-500 dark:text-slate-400">Peran Platform</dt>
                <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ user.role || 'Anggota' }}</dd>
              </div>
              <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="font-semibold text-slate-500 dark:text-slate-400">Instansi / Organisasi</dt>
                <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ user.instansi || 'Badan Riset dan Inovasi Nasional (BRIN)' }}</dd>
              </div>
              <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="font-semibold text-slate-500 dark:text-slate-400">Pekerjaan / Jabatan</dt>
                <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ user.pekerjaan || '-' }}</dd>
              </div>
              <div v-if="user.alamat" class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="font-semibold text-slate-500 dark:text-slate-400">Wilayah / Domisili</dt>
                <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">{{ user.alamat }}</dd>
              </div>
              <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="font-semibold text-slate-500 dark:text-slate-400">Tanggal Bergabung</dt>
                <dd class="sm:col-span-2 font-medium text-slate-900 dark:text-slate-100">
                  {{ formattedFullJoinDate }}
                </dd>
              </div>
            </dl>
          </div>
        </div>

      </div>

    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const page = usePage();
const isOwner = computed(() => {
  return page.props.auth?.user?.id === props.user?.id;
});

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  knowledgeList: {
    type: Object,
    default: () => ({ data: [], links: [] }),
  },
  totalKnowledge: {
    type: Number,
    default: 0,
  },
  totalViews: {
    type: Number,
    default: 0,
  },
  forumThreads: {
    type: Array,
    default: () => [],
  },
  totalThreads: {
    type: Number,
    default: 0,
  },
});

const activeTab = ref('pengetahuan');
const isCopied = ref(false);

const userInitials = computed(() => {
  return props.user?.name ? props.user.name.charAt(0).toUpperCase() : 'U';
});

const formattedJoinDate = computed(() => {
  if (!props.user?.created_at) return '-';
  const d = new Date(props.user.created_at);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
});

const formattedFullJoinDate = computed(() => {
  if (!props.user?.created_at) return '-';
  const d = new Date(props.user.created_at);
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
});

const shareProfile = async () => {
  if (navigator.share) {
    try {
      await navigator.share({
        title: `Profil ${props.user.name} - Cendekia BRIN`,
        url: window.location.href,
      });
      return;
    } catch (e) {
      // User cancelled or share failed, fallback to copy
    }
  }

  try {
    await navigator.clipboard.writeText(window.location.href);
    isCopied.value = true;
    setTimeout(() => {
      isCopied.value = false;
    }, 3000);
  } catch (err) {
    prompt('Salin tautan profil ini:', window.location.href);
  }
};

const getTypeBadgeClass = (type) => {
  switch (type) {
    case 'Teks':
      return 'bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300';
    case 'Video':
      return 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-300';
    case 'Gambar':
      return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/60 dark:text-yellow-300';
    default:
      return 'bg-purple-100 text-purple-700 dark:bg-purple-900/60 dark:text-purple-300';
  }
};

const isImageFile = (path) => {
  if (!path) return false;
  const ext = path.split('.').pop().toLowerCase();
  return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
};

const truncate = (text, len) => {
  if (!text) return '';
  const clean = text.replace(/<[^>]*>/g, '');
  return clean.length > len ? clean.substring(0, len) + '...' : clean;
};

const timeAgo = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const now = new Date();
  const diffInSeconds = Math.floor((now - date) / 1000);
  if (diffInSeconds < 60) return 'baru saja';
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

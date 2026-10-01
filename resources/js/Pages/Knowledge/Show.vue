<template>
  <PublicLayout>
    <div class="bg-white dark:bg-slate-900 min-h-screen pb-16 transition-colors duration-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

        <!-- Toast Notification (Auto-dismiss 5 detik) -->
        <transition
          enter-active-class="transition ease-out duration-300"
          enter-from-class="opacity-0 -translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-300"
          leave-from-class="opacity-100 transform scale-100"
          leave-to-class="opacity-0 transform scale-95"
        >
          <div
            v-if="toast.show"
            class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between shadow-sm"
          >
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="font-medium">{{ toast.message }}</span>
            </div>
            <button
              type="button"
              @click="toast.show = false"
              class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 transition p-1"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </transition>

        <!-- Breadcrumbs Dynamic -->
        <nav class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 mb-4 flex-wrap" aria-label="Breadcrumb">
          <Link href="/" class="hover:text-blue-600 dark:hover:text-blue-400 transition font-medium">
            Beranda
          </Link>
          <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          <span class="text-slate-800 dark:text-slate-200 font-semibold line-clamp-1">
            {{ knowledge.judul }}
          </span>
        </nav>

        <hr class="border-slate-200 dark:border-slate-700 mb-6" />

        <!-- Judul Besar -->
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-4 break-words [overflow-wrap:anywhere]">
          {{ knowledge.judul }}
        </h1>

        <!-- Meta: Dibuat oleh, Tags, Diperbarui, Tombol Bookmark -->
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-500 dark:text-slate-400 mb-8">
          <div class="flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>
              Dibuat oleh:
              <template v-if="knowledge.user">
                <UserPreviewPopover :user="knowledge.user">
                  <template #default="{ user }">
                    <a
                      :href="`/users/${user.id}`"
                      class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition"
                    >
                      {{ knowledge.penulis || user.name }}
                    </a>
                  </template>
                </UserPreviewPopover>
              </template>
              <template v-else>
                <span class="font-semibold text-slate-700 dark:text-slate-300">
                  {{ knowledge.penulis || 'Anonim' }}
                </span>
              </template>
              <span v-if="knowledge.kolaborator">, {{ knowledge.kolaborator }}</span>
            </span>
          </div>

          <!-- Tags -->
          <div v-if="knowledge.tags && knowledge.tags.length > 0" class="flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span>
              {{ knowledge.tags.map(t => t.nama_label).join(', ') }}
            </span>
          </div>

          <!-- Diperbarui -->
          <div class="flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Diperbarui: {{ formatDate(knowledge.updated_at || knowledge.created_at) }}</span>
          </div>

          <!-- Tombol Bookmark / Simpan -->
          <div class="ml-auto shrink-0 flex items-center gap-2">
            <button
              type="button"
              @click="toggleBookmark"
              :disabled="isBookmarkLoading"
              class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 border cursor-pointer"
              :class="bookmarked 
                ? 'bg-amber-50 text-amber-700 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-700/60 shadow-sm' 
                : 'bg-slate-50 text-slate-700 border-slate-200 hover:border-slate-300 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
            >
              <svg
                class="w-4 h-4 transition-transform duration-200"
                :class="bookmarked ? 'text-amber-500 fill-amber-500 scale-110' : 'text-slate-400 fill-none'"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
              </svg>
              <span>{{ bookmarked ? 'Tersimpan' : 'Simpan Artikel' }}</span>
              <span
                class="ml-1 px-1.5 py-0.2 rounded-full text-[11px]"
                :class="bookmarked ? 'bg-amber-200/80 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
              >
                {{ totalBookmarks }}
              </span>
            </button>
          </div>
        </div>

        <!-- Container: Konten Utama (Kiri 74%) + Sidebar Sticky (Kanan 24%) -->
        <div class="flex flex-col lg:flex-row gap-8 items-start justify-between">

          <!-- Kolom Kiri: Konten Utama (74%) -->
          <div class="w-full lg:w-[74%] space-y-10 min-w-0">

            <!-- Media Preview (Video / Audio / Gambar / YouTube) -->
            <section v-if="hasMedia" class="mb-4">
              <!-- YouTube Embed -->
              <div v-if="youtubeId" class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black shadow-sm">
                <div class="relative w-full pb-[56.25%] h-0 overflow-hidden">
                  <iframe
                    :src="`https://www.youtube.com/embed/${youtubeId}`"
                    class="absolute top-0 left-0 w-full h-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                  ></iframe>
                </div>
              </div>

              <!-- Video Biasa (Non-YouTube) -->
              <div v-else-if="isVideo" class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black shadow-sm">
                <video controls class="w-full max-h-[500px]">
                  <source :src="mediaSrc">
                  Browser Anda tidak mendukung pemutar video.
                </video>
              </div>

              <!-- Gambar Preview -->
              <div v-else-if="isImage" class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 shadow-sm">
                <img :src="mediaSrc" :alt="knowledge.judul" class="w-full max-h-[550px] object-contain mx-auto" />
              </div>

              <!-- Audio Player -->
              <div v-else-if="isAudio" class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <audio controls class="w-full">
                  <source :src="mediaSrc">
                  Browser Anda tidak mendukung pemutar audio.
                </audio>
              </div>
            </section>

            <!-- Ringkasan -->
            <section v-if="knowledge.deskripsi" id="ringkasan" class="scroll-mt-28">
              <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Ringkasan</h2>
              <div
                class="text-slate-700 dark:text-slate-300 leading-relaxed text-base break-words [overflow-wrap:anywhere] prose dark:prose-invert max-w-none rich-editor-content"
                v-html="knowledge.deskripsi"
              ></div>
            </section>

            <!-- Detail -->
            <section v-if="knowledge.detail" id="detail" class="scroll-mt-28">
              <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Detail</h2>
              <div
                class="text-slate-700 dark:text-slate-300 leading-relaxed text-base break-words [overflow-wrap:anywhere] prose dark:prose-invert max-w-none rich-editor-content"
                v-html="knowledge.detail"
              ></div>
            </section>

            <!-- Meta Data Table -->
            <section id="metadata" class="scroll-mt-28">
              <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Meta Data</h2>
              <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                <dl class="divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Nomor ID</dt>
                    <dd class="text-slate-600 dark:text-slate-300 break-all">{{ knowledge.id }}</dd>
                  </div>
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Judul</dt>
                    <dd class="text-slate-600 dark:text-slate-300">{{ knowledge.judul }}</dd>
                  </div>
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Penulis</dt>
                    <dd class="text-slate-600 dark:text-slate-300">
                      <template v-if="knowledge.user">
                        <a :href="`/users/${knowledge.user.id}`" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                          {{ knowledge.penulis || knowledge.user.name }}
                        </a>
                      </template>
                      <template v-else>
                        {{ knowledge.penulis || '-' }}
                      </template>
                    </dd>
                  </div>
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Kategori</dt>
                    <dd class="text-slate-600 dark:text-slate-300">{{ knowledge.category?.nama_kategori || '-' }}</dd>
                  </div>
                  <div v-if="knowledge.deskripsi" class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Deskripsi</dt>
                    <dd class="text-slate-600 dark:text-slate-300 break-words" v-html="knowledge.deskripsi"></dd>
                  </div>
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Waktu</dt>
                    <dd class="text-slate-600 dark:text-slate-300">
                      {{ formatLongDate(knowledge.tanggal_terbit || knowledge.created_at) }}
                    </dd>
                  </div>
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Format</dt>
                    <dd class="text-slate-600 dark:text-slate-300">{{ knowledge.tipe }}</dd>
                  </div>
                  <div v-if="knowledge.tags && knowledge.tags.length > 0" class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Label</dt>
                    <dd class="flex flex-wrap gap-2">
                      <span
                        v-for="tag in knowledge.tags"
                        :key="tag.id"
                        class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs rounded-md"
                      >
                        {{ tag.nama_label }}
                      </span>
                    </dd>
                  </div>
                  <div v-if="knowledge.kolaborator" class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Kontributor</dt>
                    <dd class="text-slate-600 dark:text-slate-300">{{ knowledge.kolaborator }}</dd>
                  </div>
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Status Publikasi</dt>
                    <dd class="text-slate-600 dark:text-slate-300">{{ knowledge.status }}</dd>
                  </div>
                  <div v-if="knowledge.url_teks" class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">URL</dt>
                    <dd>
                      <a :href="knowledge.url_teks" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline break-all">
                        {{ knowledge.url_teks }}
                      </a>
                    </dd>
                  </div>
                  <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[180px_1fr] gap-4">
                    <dt class="font-semibold text-slate-900 dark:text-slate-100">Dilihat</dt>
                    <dd class="text-slate-600 dark:text-slate-300">{{ knowledge.views_count || 0 }} kali</dd>
                  </div>
                </dl>
              </div>
            </section>

            <!-- Rating / Like & Interaksi Bar -->
            <div class="mt-8 p-6 bg-gradient-to-r from-slate-50 via-blue-50/20 to-slate-50 dark:from-slate-800/90 dark:via-slate-800 dark:to-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
              <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                  <span>Beri Rating & Tanggapan</span>
                  <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 font-semibold">Feedback</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  Apakah materi pengetahuan ini bermanfaat untuk Anda? Berikan apresiasi atau tinggalkan diskusi.
                </p>
              </div>

              <div class="flex items-center gap-3 shrink-0">
                <!-- Tombol Like -->
                <button
                  type="button"
                  @click="toggleLike"
                  :disabled="isLikeLoading"
                  class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border cursor-pointer"
                  :class="liked 
                    ? 'bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-700/60 shadow-sm' 
                    : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700'"
                >
                  <svg
                    class="w-5 h-5 transition-transform duration-200"
                    :class="liked ? 'text-rose-500 fill-rose-500 scale-110' : 'text-slate-400 fill-none'"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                  <span>{{ liked ? 'Disukai' : 'Suka' }}</span>
                  <span
                    class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold"
                    :class="liked ? 'bg-rose-200/80 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
                  >
                    {{ totalLikes }}
                  </span>
                </button>

                <!-- Tombol Lompat ke Komentar -->
                <a
                  href="#komentar"
                  class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700"
                >
                  <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <span>Komentar</span>
                  <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                    {{ totalComments }}
                  </span>
                </a>
              </div>
            </div>

            <!-- Section Komentar & Diskusi -->
            <section id="komentar" class="scroll-mt-28 space-y-6 pt-4">
              <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                  </svg>
                  Diskusi & Komentar
                  <span class="text-xs px-2.5 py-1 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold">
                    {{ totalComments }}
                  </span>
                </h2>
              </div>

              <!-- Daftar Komentar -->
              <div v-if="commentsList.length > 0" class="space-y-4">
                <div
                  v-for="comment in commentsList"
                  :key="comment.id"
                  :id="`comment-${comment.id}`"
                  class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm space-y-3"
                >
                  <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-sm text-slate-700 dark:text-slate-200">
                        <img
                          v-if="comment.user?.foto_profil"
                          :src="`/storage/${comment.user.foto_profil}`"
                          :alt="comment.user.name"
                          class="w-full h-full object-cover"
                        />
                        <span v-else>
                          {{ (comment.user?.name || 'A').charAt(0).toUpperCase() }}
                        </span>
                      </div>
                      <div>
                        <div class="flex items-center gap-2">
                          <span class="font-bold text-sm text-slate-900 dark:text-white">
                            {{ comment.user?.name || 'Anonim' }}
                          </span>
                          <span
                            v-if="comment.user && ['Super Admin', 'Admin Pusat', 'Admin'].includes(comment.user.role)"
                            class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300 font-semibold"
                          >
                            Admin
                          </span>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">
                          {{ formatRelativeTime(comment.created_at) }}
                        </span>
                      </div>
                    </div>

                    <!-- Tombol Hapus Komentar (Author / Admin) -->
                    <button
                      v-if="canDeleteComment(comment)"
                      type="button"
                      @click="deleteComment(comment.id)"
                      class="text-xs text-slate-400 hover:text-rose-600 transition p-1 cursor-pointer"
                      title="Hapus komentar"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>

                  <div class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed pl-1 sm:pl-3">
                    {{ comment.konten }}
                  </div>

                  <!-- Tombol Balas -->
                  <div v-if="currentUser" class="pl-1 sm:pl-3 pt-1">
                    <button
                      type="button"
                      @click="setReply(comment, comment.user?.name || 'Anonim')"
                      class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 transition cursor-pointer"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                      </svg>
                      Balas
                    </button>
                  </div>

                  <!-- Balasan Bersarang (Replies) -->
                  <div
                    v-if="comment.replies && comment.replies.length > 0"
                    class="ml-4 sm:ml-8 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 space-y-3"
                  >
                    <div
                      v-for="reply in comment.replies"
                      :key="reply.id"
                      :id="`comment-${reply.id}`"
                      class="bg-slate-50 dark:bg-slate-750/50 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700 space-y-2"
                    >
                      <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                          <div class="w-8 h-8 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-200">
                            <img
                              v-if="reply.user?.foto_profil"
                              :src="`/storage/${reply.user.foto_profil}`"
                              :alt="reply.user.name"
                              class="w-full h-full object-cover"
                            />
                            <span v-else>
                              {{ (reply.user?.name || 'A').charAt(0).toUpperCase() }}
                            </span>
                          </div>
                          <div>
                            <span class="font-bold text-xs text-slate-900 dark:text-white">
                              {{ reply.user?.name || 'Anonim' }}
                            </span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 block">
                              {{ formatRelativeTime(reply.created_at) }}
                            </span>
                          </div>
                        </div>

                        <button
                          v-if="canDeleteComment(reply)"
                          type="button"
                          @click="deleteComment(reply.id)"
                          class="text-xs text-slate-400 hover:text-rose-600 transition p-1 cursor-pointer"
                          title="Hapus balasan"
                        >
                          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>

                      <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed pl-1 sm:pl-2">
                        {{ reply.konten }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Placeholder Belum Ada Komentar -->
              <div v-else class="p-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto mb-3">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                </div>
                <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Belum ada komentar</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  Jadilah yang pertama memberikan tanggapan atau pertanyaan pada pengetahuan ini!
                </p>
              </div>

              <!-- Kotak Tulis Komentar Sticky / Floating -->
              <div v-if="currentUser" class="sticky bottom-4 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-300 dark:border-slate-700 p-4 transition-all duration-300">
                <div class="flex items-center justify-between" :class="{ 'mb-3': !isCollapsed }">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                      <span>{{ replyTo ? ('Membalas: ' + replyTo.name) : 'Tuliskan Komentar Anda' }}</span>
                      <button
                        v-if="replyTo"
                        type="button"
                        @click="cancelReply"
                        class="ml-2 text-xs text-rose-500 hover:text-rose-600 font-semibold transition cursor-pointer"
                      >
                        ✕ Batal
                      </button>
                    </h3>
                  </div>

                  <!-- Minimize / Expand Toggle Button -->
                  <button
                    type="button"
                    @click="isCollapsed = !isCollapsed"
                    class="text-xs text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white flex items-center gap-1 px-2.5 py-1 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 transition select-none cursor-pointer font-medium"
                  >
                    <span>{{ isCollapsed ? 'Buka Form' : 'Sembunyikan' }}</span>
                    <svg
                      class="w-3.5 h-3.5 transition-transform duration-200"
                      :class="isCollapsed ? 'rotate-180' : ''"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </div>

                <form v-show="!isCollapsed" @submit.prevent="submitComment">
                  <textarea
                    ref="commentTextarea"
                    v-model="commentText"
                    rows="3"
                    required
                    placeholder="Tuliskan komentar atau pertanyaan Anda di sini..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition resize-none shadow-xs"
                  ></textarea>
                  <div class="flex items-center justify-between mt-2.5">
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 hidden sm:inline">
                      Komentar Anda akan dapat dibaca oleh pembaca lainnya.
                    </span>
                    <button
                      type="submit"
                      :disabled="isSubmitting || !commentText.trim()"
                      class="ml-auto px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm cursor-pointer"
                    >
                      <template v-if="!isSubmitting">
                        <div class="flex items-center gap-2">
                          <!-- Heroicons v2 right-pointing paper-airplane icon -->
                          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                          </svg>
                          <span>Kirim Komentar</span>
                        </div>
                      </template>
                      <template v-else>
                        <div class="flex items-center gap-2">
                          <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                          </svg>
                          <span>Mengirim...</span>
                        </div>
                      </template>
                    </button>
                  </div>
                </form>
              </div>

              <!-- Guest Notice (Login to comment) -->
              <div v-else class="text-center py-6 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Silakan masuk ke akun Anda untuk memberikan rating dan komentar.</p>
                <a href="/login" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm">
                  Masuk / Login
                </a>
              </div>
            </section>

          </div>

          <!-- Kolom Kanan: Sidebar Sticky (24%) -->
          <div class="w-full lg:w-[24%] lg:sticky lg:top-24 space-y-6 shrink-0">

            <!-- Estimasi Waktu Baca -->
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
              <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ readingTime }} menit dibaca</span>
            </div>

            <!-- Box Penulis -->
            <div v-if="knowledge.user" class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm space-y-3">
              <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Penulis</span>
              <div class="flex items-center gap-3">
                <img
                  v-if="knowledge.user.foto_profil"
                  :src="`/storage/${knowledge.user.foto_profil}`"
                  class="w-11 h-11 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0"
                  :alt="knowledge.user.name"
                />
                <div v-else class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-base flex items-center justify-center shrink-0">
                  {{ (knowledge.user.name || 'A').charAt(0).toUpperCase() }}
                </div>
                <div class="min-w-0">
                  <a :href="`/users/${knowledge.user.id}`" class="font-bold text-sm text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition truncate block">
                    {{ knowledge.user.name }}
                  </a>
                  <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                    {{ knowledge.user.role || 'Anggota' }}
                  </div>
                </div>
              </div>
              <a
                :href="`/users/${knowledge.user.id}`"
                class="block text-center py-2 px-3 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-xl transition"
              >
                Lihat Profil Penulis &rarr;
              </a>
            </div>

            <!-- Di halaman ini (TOC Navigation) -->
            <div class="space-y-3">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Di halaman ini</h3>
              <nav class="space-y-2 text-sm">
                <a
                  v-if="knowledge.deskripsi"
                  href="#ringkasan"
                  class="block text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition font-medium"
                >
                  Ringkasan
                </a>
                <a
                  v-if="knowledge.detail"
                  href="#detail"
                  class="block text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition"
                >
                  Detail
                </a>
                <a
                  href="#metadata"
                  class="block text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition"
                >
                  Meta Data
                </a>
                <a
                  href="#komentar"
                  class="block text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition"
                >
                  Komentar & Rating
                </a>
              </nav>
            </div>

          </div>

        </div>

      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import UserPreviewPopover from '../../Components/UserPreviewPopover.vue';

const props = defineProps({
  knowledge: {
    type: Object,
    required: true,
  },
  readingTime: {
    type: Number,
    default: 1,
  },
  isBookmarked: {
    type: Boolean,
    default: false,
  },
  bookmarksCount: {
    type: Number,
    default: 0,
  },
  isLiked: {
    type: Boolean,
    default: false,
  },
  likesCount: {
    type: Number,
    default: 0,
  },
  comments: {
    type: Array,
    default: () => [],
  },
  commentsCount: {
    type: Number,
    default: 0,
  },
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

// Toast Notification
const toast = ref({
  show: false,
  message: '',
  timer: null,
});

const showToast = (msg) => {
  toast.value.message = msg;
  toast.value.show = true;
  if (toast.value.timer) clearTimeout(toast.value.timer);
  toast.value.timer = setTimeout(() => {
    toast.value.show = false;
  }, 5000);
};

// Bookmark State
const bookmarked = ref(props.isBookmarked);
const totalBookmarks = ref(props.bookmarksCount);
const isBookmarkLoading = ref(false);

const toggleBookmark = async () => {
  if (!currentUser.value) {
    window.location.href = '/login';
    return;
  }
  if (isBookmarkLoading.value) return;
  isBookmarkLoading.value = true;
  try {
    const res = await axios.post(`/knowledge/${props.knowledge.id}/bookmark`);
    if (res.data?.status === 'success') {
      bookmarked.value = res.data.bookmarked;
      totalBookmarks.value = res.data.total_bookmarks;
    }
  } catch (err) {
    console.error('Error toggling bookmark:', err);
  } finally {
    isBookmarkLoading.value = false;
  }
};

// Like State
const liked = ref(props.isLiked);
const totalLikes = ref(props.likesCount);
const isLikeLoading = ref(false);

const toggleLike = async () => {
  if (!currentUser.value) {
    window.location.href = '/login';
    return;
  }
  if (isLikeLoading.value) return;
  isLikeLoading.value = true;
  try {
    const res = await axios.post(`/knowledge/${props.knowledge.id}/like`);
    if (res.data?.status === 'success') {
      liked.value = res.data.liked;
      totalLikes.value = res.data.total_likes;
    }
  } catch (err) {
    console.error('Error toggling like:', err);
  } finally {
    isLikeLoading.value = false;
  }
};

// Media Detection
const youtubeId = computed(() => {
  const url = props.knowledge.url_teks;
  if (!url) return null;
  const match = url.match(/(?:youtube\.com\/(?:watch\?.*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  return match ? match[1] : null;
});

const isVideo = computed(() => {
  return props.knowledge.tipe === 'Video' && !youtubeId.value;
});

const isImage = computed(() => {
  return props.knowledge.tipe === 'Gambar';
});

const isAudio = computed(() => {
  return props.knowledge.tipe === 'Audio';
});

const mediaSrc = computed(() => {
  const k = props.knowledge;
  if (k.url_teks && (k.url_teks.startsWith('http://') || k.url_teks.startsWith('https://'))) {
    return k.url_teks;
  }
  if (k.file_path) {
    return `/storage/${k.file_path}`;
  }
  return k.url_teks || '';
});

const hasMedia = computed(() => {
  return Boolean(youtubeId.value || (isVideo.value && mediaSrc.value) || (isImage.value && mediaSrc.value) || (isAudio.value && mediaSrc.value));
});

// Comments State
const commentsList = ref([...(props.comments || [])]);
const totalComments = ref(props.commentsCount || 0);

const replyTo = ref(null);
const commentText = ref('');
const isCollapsed = ref(false);
const isSubmitting = ref(false);
const commentTextarea = ref(null);

const setReply = (comment, userName) => {
  replyTo.value = { id: comment.id, name: userName };
  isCollapsed.value = false;
  setTimeout(() => {
    commentTextarea.value?.focus();
  }, 100);
};

const cancelReply = () => {
  replyTo.value = null;
};

const canDeleteComment = (comment) => {
  if (!currentUser.value) return false;
  return (
    currentUser.value.id === comment.user_id ||
    ['Super Admin', 'Admin Pusat', 'Admin'].includes(currentUser.value.role)
  );
};

const submitComment = async () => {
  if (!commentText.value.trim()) return;
  if (!currentUser.value) {
    window.location.href = '/login';
    return;
  }
  isSubmitting.value = true;
  try {
    const payload = {
      konten: commentText.value,
      parent_id: replyTo.value ? replyTo.value.id : null,
    };
    const res = await axios.post(`/knowledge/${props.knowledge.id}/comment`, payload);
    if (res.data?.status === 'success') {
      const newComment = res.data.comment;
      if (payload.parent_id) {
        const parent = commentsList.value.find(c => c.id === payload.parent_id);
        if (parent) {
          if (!parent.replies) parent.replies = [];
          parent.replies.push(newComment);
        }
      } else {
        if (!newComment.replies) newComment.replies = [];
        commentsList.value.push(newComment);
      }
      totalComments.value++;
      commentText.value = '';
      cancelReply();
      showToast(res.data.message || 'Komentar Anda berhasil dikirim.');
    }
  } catch (err) {
    console.error('Error submitting comment:', err);
    alert(err.response?.data?.message || 'Gagal mengirim komentar.');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteComment = async (commentId) => {
  if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) return;
  try {
    const res = await axios.delete(`/knowledge/comment/${commentId}`);
    if (res.data?.status === 'success') {
      const idx = commentsList.value.findIndex(c => c.id === commentId);
      if (idx !== -1) {
        const repliesCount = commentsList.value[idx].replies?.length || 0;
        commentsList.value.splice(idx, 1);
        totalComments.value = Math.max(0, totalComments.value - (1 + repliesCount));
      } else {
        for (const parent of commentsList.value) {
          if (parent.replies) {
            const rIdx = parent.replies.findIndex(r => r.id === commentId);
            if (rIdx !== -1) {
              parent.replies.splice(rIdx, 1);
              totalComments.value = Math.max(0, totalComments.value - 1);
              break;
            }
          }
        }
      }
      showToast(res.data.message || 'Komentar berhasil dihapus.');
    }
  } catch (err) {
    console.error('Error deleting comment:', err);
  }
};

// Format Helpers
const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const year = d.getFullYear();
  return `${day}-${month}-${year}`;
};

const formatLongDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

const formatRelativeTime = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const now = new Date();
  const diffSec = Math.floor((now - date) / 1000);
  if (diffSec < 10) return 'Baru saja';
  if (diffSec < 60) return `${diffSec} detik yang lalu`;
  const diffMin = Math.floor(diffSec / 60);
  if (diffMin < 60) return `${diffMin} menit yang lalu`;
  const diffHour = Math.floor(diffMin / 60);
  if (diffHour < 24) return `${diffHour} jam yang lalu`;
  const diffDay = Math.floor(diffHour / 24);
  if (diffDay < 30) return `${diffDay} hari yang lalu`;
  return formatDate(dateStr);
};
</script>

<style scoped>
:deep(.rich-editor-content ul) {
  list-style-type: disc !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem !important;
  margin-bottom: 0.5rem !important;
}
:deep(.rich-editor-content ol) {
  list-style-type: decimal !important;
  padding-left: 1.5rem !important;
  margin-top: 0.5rem !important;
  margin-bottom: 0.5rem !important;
}
:deep(.rich-editor-content li) {
  display: list-item !important;
}
</style>

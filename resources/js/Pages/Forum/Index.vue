<template>
  <PublicLayout>
    <!-- Header -->
    <section class="relative py-12 bg-[#1e3a8a] text-white overflow-hidden">

      <div class="container mx-auto px-4 relative z-10 text-center">
        <nav class="flex justify-center text-sm text-blue-200 mb-4" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <Link href="/" class="inline-flex items-center hover:text-white transition">Beranda</Link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-3 h-3 mx-1 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white font-medium ml-1">Forum</span>
              </div>
            </li>
          </ol>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-2 tracking-tight">Forum Diskusi MojoPedia</h1>
        <p class="text-base text-blue-100 max-w-2xl mx-auto">Ruang kolaborasi, tanya jawab, dan berbagi telaah pengetahuan antar aparatur dan peneliti.</p>
      </div>
    </section>

    <section class="pt-8 pb-32 sm:pb-44 container mx-auto px-4 max-w-5xl">
      <!-- Action Bar -->
      <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 bg-white dark:bg-slate-900 p-4 rounded-lg shadow-sm border border-[#e2e8f0] dark:border-slate-800">
        <!-- Sort Filters -->
        <div class="flex flex-wrap gap-2 w-full md:w-auto items-center relative">
          <Link 
            href="/forum?sort=terbaru" 
            :class="[
              'btn-slide-fill px-4 py-2 rounded-lg text-sm transition cursor-pointer select-none border',
              sort === 'terbaru' 
                ? 'is-active font-semibold shadow-xs' 
                : 'border-[#e2e8f0] dark:border-slate-800 text-[#475569] dark:text-slate-400 font-medium'
            ]"
          >
            <span>Terbaru</span>
          </Link>

          <!-- Dropdown Terpopuler -->
          <div class="relative">
            <button 
              @click="popOpen = !popOpen" 
              type="button"
              :class="[
                'btn-slide-fill inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm transition cursor-pointer select-none border',
                isPopuler 
                  ? 'is-active font-semibold shadow-xs' 
                  : 'border-[#e2e8f0] dark:border-slate-800 text-[#475569] dark:text-slate-400 font-medium'
              ]"
            >
              <span>{{ activePopulerLabel }}</span>
              <svg class="w-4 h-4 transition-transform relative z-10" :class="popOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>

            <div v-if="popOpen" class="absolute left-0 top-full mt-2 w-64 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-[#e2e8f0] dark:border-slate-800 py-1.5 z-50 overflow-hidden">
              <Link href="/forum?sort=populer_umum" @click="popOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[#eff6ff] dark:hover:bg-slate-800 transition">
                <div>
                  <div class="font-medium text-[#0f172a] dark:text-slate-100">Populer Umum</div>
                  <div class="text-xs text-[#94a3b8]">Gabungan komentar & tayangan</div>
                </div>
              </Link>
              <Link href="/forum?sort=populer_tayangan" @click="popOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[#eff6ff] dark:hover:bg-slate-800 transition">
                <div>
                  <div class="font-medium text-[#0f172a] dark:text-slate-100">Populer Tayangan</div>
                  <div class="text-xs text-[#94a3b8]">Berdasarkan jumlah tayangan</div>
                </div>
              </Link>
              <Link href="/forum?sort=populer_komentar" @click="popOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[#eff6ff] dark:hover:bg-slate-800 transition">
                <div>
                  <div class="font-medium text-[#0f172a] dark:text-slate-100">Populer Komentar</div>
                  <div class="text-xs text-[#94a3b8]">Berdasarkan jumlah komentar</div>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Action Button -->
        <div class="w-full md:w-auto text-right">
          <Link href="/forum/create" class="group inline-flex justify-center items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-5 py-2.5 rounded-lg font-semibold transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm w-full md:w-auto">
            <svg class="w-5 h-5 group-hover-spin-brief" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span>Buat Topik Baru</span>
          </Link>
        </div>
      </div>

      <!-- Thread List -->
      <div class="space-y-4">
        <div v-if="threads && threads.data && threads.data.length > 0">
          <div 
            v-for="thread in threads.data" 
            :key="thread.id"
            :class="[
              thread.is_pinned ? 'border-[#2563eb]' : 'border-[#e2e8f0] dark:border-slate-800',
              openMenuThreadId === thread.id ? 'z-30 relative' : 'relative'
            ]"
            class="bg-white dark:bg-slate-900 p-5 rounded-lg shadow-sm border card-hover transition mb-4 cursor-pointer"
            @click="router.visit(`/forum/${thread.id}`)"
          >
            <div class="flex items-start gap-4">
              <!-- Avatar -->
              <div class="hidden sm:block shrink-0" @click.stop>
                <UserPreviewPopover :user="thread.user">
                  <template #default="{ user }">
                    <a :href="`/users/${user.id}`" class="w-11 h-11 bg-[#2563eb] text-white rounded-full flex items-center justify-center font-bold text-base shrink-0 hover:opacity-90 transition overflow-hidden">
                      <img v-if="user.foto_profil" :src="`/storage/${user.foto_profil}`" class="w-full h-full object-cover" :alt="user.name" />
                      <span v-else>{{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
                    </a>
                  </template>
                </UserPreviewPopover>
              </div>

              <div class="flex-grow min-w-0">
                <div class="flex items-start justify-between gap-3 mb-1">
                  <div class="flex items-center gap-2 min-w-0 flex-wrap">
                    <span v-if="thread.is_pinned" class="bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 text-xs px-2.5 py-0.5 rounded-full font-semibold border border-blue-200 shrink-0">
                      Pinned
                    </span>
                    <span v-if="thread.is_locked" class="bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 text-xs px-2.5 py-0.5 rounded-full font-semibold shrink-0">
                      🔒 Dikunci
                    </span>
                    <h3 class="text-base font-bold text-[#0f172a] dark:text-white truncate">
                      <Link :href="`/forum/${thread.id}`" class="hover:text-[#2563eb] dark:hover:text-blue-400 transition">
                        {{ thread.judul }}
                      </Link>
                    </h3>
                  </div>

                  <!-- Tombol Titik 3 (Aksi Topik: Edit, Pin, Kunci, Hapus) -->
                  <div v-if="hasThreadMenu(thread)" class="relative shrink-0" @click.stop :ref="el => setMenuRef(el, thread.id)">
                    <button
                      type="button"
                      @click.stop="toggleThreadMenu(thread.id, $event)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                      :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200': openMenuThreadId === thread.id }"
                      title="Opsi topik"
                      aria-label="Opsi topik"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                      </svg>
                    </button>

                    <!-- Dropdown Menu Titik 3 (Auto Up/Down agar tidak menembus footer) -->
                    <div
                      v-if="openMenuThreadId === thread.id"
                      :class="[
                        'absolute right-0 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 text-left',
                        menuDropdownPositions[thread.id] === 'up' ? 'bottom-full mb-1.5' : 'top-full mt-1.5'
                      ]"
                    >
                      <!-- 1. Edit -->
                      <a
                        v-if="canEditThread(thread)"
                        :href="`/forum/${thread.id}/edit`"
                        class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition"
                      >
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit</span>
                      </a>

                      <!-- 2. Pin / Unpin (Moderator / Admin) -->
                      <button
                        v-if="canManageForum"
                        type="button"
                        @click.stop="togglePin(thread)"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition text-left cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                        </svg>
                        <span>{{ thread.is_pinned ? 'Lepas Pin' : 'Pin' }}</span>
                      </button>

                      <!-- 3. Kunci / Buka Kunci (Moderator / Admin) -->
                      <button
                        v-if="canManageForum"
                        type="button"
                        @click.stop="toggleLock(thread)"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition text-left cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ thread.is_locked ? 'Buka Kunci' : 'Kunci' }}</span>
                      </button>

                      <div v-if="canDeleteThread(thread) && (canEditThread(thread) || canManageForum)" class="border-t border-slate-100 dark:border-slate-700 my-1"></div>

                      <!-- 4. Hapus (Owner atau Moderator / Admin) -->
                      <button
                        v-if="canDeleteThread(thread)"
                        type="button"
                        @click.stop="confirmDelete(thread)"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition text-left cursor-pointer"
                      >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Hapus</span>
                      </button>
                    </div>
                  </div>
                </div>

                <p class="text-sm text-[#475569] dark:text-slate-300 line-clamp-2 mb-3">
                  {{ thread.konten ? thread.konten.replace(/<[^>]*>/g, '').substring(0, 100) : '' }}
                </p>

                <div class="flex flex-wrap items-center gap-4 text-xs text-[#94a3b8] dark:text-slate-400">
                  <div @click.stop>
                    <UserPreviewPopover :user="thread.user">
                      <template #default="{ user }">
                        <a :href="`/users/${user.id}`" class="font-medium text-[#475569] dark:text-slate-300 hover:text-[#2563eb] dark:hover:text-blue-400 hover:underline transition">
                          {{ user.name }}
                        </a>
                      </template>
                    </UserPreviewPopover>
                  </div>

                  <span v-if="thread.category" class="bg-[#f1f5f9] dark:bg-slate-800 px-2 py-0.5 rounded text-[#475569] dark:text-slate-300 border border-[#e2e8f0] dark:border-slate-700">
                    {{ thread.category.nama_kategori }}
                  </span>

                  <div class="flex items-center gap-3 ml-auto">
                    <span class="flex items-center gap-1 font-semibold text-[#2563eb] dark:text-blue-400">
                      💬 {{ thread.replies_count || 0 }}
                    </span>
                    <span class="flex items-center gap-1">
                      👁️ {{ thread.views_count || 0 }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-16 bg-white dark:bg-slate-900 rounded-lg border border-[#e2e8f0] dark:border-slate-800 shadow-sm">
          <h3 class="text-xl font-bold text-[#0f172a] dark:text-white mb-2">Belum ada topik diskusi</h3>
          <p class="text-[#475569] dark:text-slate-400 mb-6">Jadilah yang pertama memulai diskusi di forum ini!</p>
          <Link href="/forum/create" class="group inline-flex items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-6 py-2.5 rounded-lg font-semibold transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm">
            <svg class="w-5 h-5 group-hover-spin-brief" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span>Buat Topik Baru</span>
          </Link>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import UserPreviewPopover from '../../Components/UserPreviewPopover.vue';

const props = defineProps({
  threads: Object,
  sort: {
    type: String,
    default: 'terbaru'
  }
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

const canManageForum = computed(() => {
  if (!currentUser.value) return false;
  return ['Super Admin', 'Admin Pusat', 'Admin', 'Moderator'].includes(currentUser.value.role);
});

const canEditThread = (thread) => {
  if (!currentUser.value || thread.deleted_at) return false;
  return currentUser.value.id === thread.user_id || canManageForum.value;
};

const canDeleteThread = (thread) => {
  if (!currentUser.value || thread.deleted_at) return false;
  return currentUser.value.id === thread.user_id || canManageForum.value;
};

const hasThreadMenu = (thread) => {
  return canEditThread(thread) || canManageForum.value || canDeleteThread(thread);
};

// 3-Dots Dropdown State & Click-outside handling
const openMenuThreadId = ref(null);
const menuDropdownPositions = ref({});
const menuRefs = new Map();

const setMenuRef = (el, id) => {
  if (el) {
    menuRefs.set(id, el);
  } else {
    menuRefs.delete(id);
  }
};

const toggleThreadMenu = (id, event) => {
  if (openMenuThreadId.value === id) {
    openMenuThreadId.value = null;
    return;
  }
  openMenuThreadId.value = id;
  if (event && event.currentTarget) {
    const rect = event.currentTarget.getBoundingClientRect();
    const spaceBelow = window.innerHeight - rect.bottom;
    // Jika sisa ruang di bawah kurang dari 230px, buka dropdown ke atas (dropup)
    menuDropdownPositions.value[id] = spaceBelow < 230 ? 'up' : 'down';
  } else {
    menuDropdownPositions.value[id] = 'down';
  }
};

const handleDocumentClick = (event) => {
  if (!openMenuThreadId.value) return;
  const currentEl = menuRefs.get(openMenuThreadId.value);
  if (currentEl && !currentEl.contains(event.target)) {
    openMenuThreadId.value = null;
  }
};

onMounted(() => {
  document.addEventListener('click', handleDocumentClick);
});

onUnmounted(() => {
  document.removeEventListener('click', handleDocumentClick);
});

const togglePin = (thread) => {
  openMenuThreadId.value = null;
  router.patch(`/forum/${thread.id}/pin`, {}, {
    preserveScroll: true,
  });
};

const toggleLock = (thread) => {
  openMenuThreadId.value = null;
  router.patch(`/forum/${thread.id}/lock`, {}, {
    preserveScroll: true,
  });
};

const confirmDelete = (thread) => {
  openMenuThreadId.value = null;
  if (confirm(`Pindahkan topik "${thread.judul}" ke tong sampah? Anda masih dapat memulihkannya kapan saja.`)) {
    router.delete(`/forum/${thread.id}`, {
      preserveScroll: true,
    });
  }
};

const popOpen = ref(false);

const isPopuler = computed(() => ['populer_umum', 'populer_tayangan', 'populer_komentar'].includes(props.sort));

const activePopulerLabel = computed(() => {
  if (props.sort === 'populer_umum') return 'Populer Umum';
  if (props.sort === 'populer_tayangan') return 'Populer Tayangan';
  if (props.sort === 'populer_komentar') return 'Populer Komentar';
  return 'Terpopuler';
});
</script>

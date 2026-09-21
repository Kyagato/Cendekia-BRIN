<template>
  <PublicLayout>
    <!-- Header -->
    <section class="relative py-12 bg-[#1e3a8a] text-white overflow-hidden">

      <div class="container mx-auto px-4 relative z-10">
        <nav class="flex text-sm text-blue-200 mb-4" aria-label="Breadcrumb">
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
        <p class="text-base text-blue-100">Ruang kolaborasi, tanya jawab, dan berbagi telaah pengetahuan antar aparatur dan peneliti.</p>
      </div>
    </section>

    <section class="py-8 container mx-auto px-4 max-w-5xl">
      <!-- Action Bar -->
      <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 bg-white dark:bg-slate-900 p-4 rounded-lg shadow-sm border border-[#e2e8f0] dark:border-slate-800">
        <!-- Sort Filters -->
        <div class="flex flex-wrap gap-2 w-full md:w-auto items-center relative">
          <Link 
            href="/forum?sort=terbaru" 
            :class="sort === 'terbaru' ? 'bg-[#2563eb] text-white font-semibold' : 'text-[#475569] dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition"
          >
            Terbaru
          </Link>

          <!-- Dropdown Terpopuler -->
          <div class="relative">
            <button 
              @click="popOpen = !popOpen" 
              type="button"
              :class="isPopuler ? 'bg-[#2563eb] text-white font-semibold' : 'text-[#475569] dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition cursor-pointer"
            >
              {{ activePopulerLabel }}
              <svg class="w-4 h-4 transition-transform" :class="popOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
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
          <Link href="/forum/create" class="inline-flex justify-center items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-5 py-2.5 rounded-lg font-semibold transition shadow-sm w-full md:w-auto">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Buat Topik Baru
          </Link>
        </div>
      </div>

      <!-- Thread List -->
      <div class="space-y-4">
        <div v-if="threads && threads.data && threads.data.length > 0">
          <div 
            v-for="thread in threads.data" 
            :key="thread.id"
            :class="thread.is_pinned ? 'border-[#2563eb]' : 'border-[#e2e8f0] dark:border-slate-800'"
            class="bg-white dark:bg-slate-900 p-5 rounded-lg shadow-sm border card-hover transition mb-4 cursor-pointer"
            @click="router.visit(`/forum/${thread.id}`)"
          >
            <div class="flex items-start gap-4">
              <!-- Avatar -->
              <div class="hidden sm:block shrink-0" @click.stop>
                <UserPreviewPopover :user="thread.user">
                  <template #default="{ user }">
                    <a :href="`/users/${user.id}`" class="w-11 h-11 bg-[#2563eb] text-white rounded-lg flex items-center justify-center font-bold text-base shrink-0 hover:opacity-90 transition overflow-hidden">
                      <img v-if="user.foto_profil" :src="`/storage/${user.foto_profil}`" class="w-full h-full object-cover" :alt="user.name" />
                      <span v-else>{{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
                    </a>
                  </template>
                </UserPreviewPopover>
              </div>

              <div class="flex-grow min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <span v-if="thread.is_pinned" class="bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950 dark:text-blue-300 text-xs px-2.5 py-0.5 rounded-full font-semibold border border-blue-200">
                    Pinned
                  </span>
                  <h3 class="text-base font-bold text-[#0f172a] dark:text-white truncate">
                    <Link :href="`/forum/${thread.id}`" class="hover:text-[#2563eb] dark:hover:text-blue-400 transition">
                      {{ thread.judul }}
                    </Link>
                  </h3>
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
          <Link href="/forum/create" class="inline-flex items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-6 py-2.5 rounded-lg font-semibold transition shadow-sm">
            Buat Topik Baru
          </Link>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import UserPreviewPopover from '../../Components/UserPreviewPopover.vue';

const props = defineProps({
  threads: Object,
  sort: {
    type: String,
    default: 'terbaru'
  }
});

const popOpen = ref(false);

const isPopuler = computed(() => ['populer_umum', 'populer_tayangan', 'populer_komentar'].includes(props.sort));

const activePopulerLabel = computed(() => {
  if (props.sort === 'populer_umum') return 'Populer Umum';
  if (props.sort === 'populer_tayangan') return 'Populer Tayangan';
  if (props.sort === 'populer_komentar') return 'Populer Komentar';
  return 'Terpopuler';
});
</script>

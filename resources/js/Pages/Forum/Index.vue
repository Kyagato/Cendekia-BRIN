<template>
  <PublicLayout>
    <!-- Header -->
    <section class="py-12 bg-gradient-to-br from-slate-900 via-red-950 to-slate-900">
      <div class="container mx-auto px-4">
        <nav class="flex text-sm text-white/70 mb-4" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <Link href="/" class="inline-flex items-center hover:text-white transition">Beranda</Link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-3 h-3 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white font-medium ml-1">Forum</span>
              </div>
            </li>
          </ol>
        </nav>
        <h1 class="text-4xl font-bold text-white mb-2">Forum Diskusi SPBE</h1>
        <p class="text-lg text-white/80">Ruang kolaborasi, tanya jawab, dan berbagi pengetahuan antar pengguna.</p>
      </div>
    </section>

    <section class="py-8 container mx-auto px-4 max-w-5xl">
      <!-- Action Bar -->
      <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
        <!-- Sort Filters -->
        <div class="flex flex-wrap gap-2 w-full md:w-auto items-center relative">
          <Link 
            href="/forum?sort=terbaru" 
            :class="sort === 'terbaru' ? 'bg-red-500/10 text-red-500 font-semibold' : 'text-slate-400 hover:bg-slate-700'"
            class="px-4 py-2 rounded-full text-sm font-medium transition"
          >
            Terbaru
          </Link>

          <!-- Dropdown Terpopuler -->
          <div class="relative">
            <button 
              @click="popOpen = !popOpen" 
              type="button"
              :class="isPopuler ? 'bg-red-500/10 text-red-500 font-semibold' : 'text-slate-400 hover:bg-slate-700'"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium transition cursor-pointer"
            >
              {{ activePopulerLabel }}
              <svg class="w-4 h-4 transition-transform" :class="popOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>

            <div v-if="popOpen" class="absolute left-0 top-full mt-2 w-64 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 overflow-hidden">
              <Link href="/forum?sort=populer_umum" @click="popOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <div>
                  <div class="font-medium text-slate-800 dark:text-slate-100">Populer Umum</div>
                  <div class="text-xs text-slate-400">Gabungan komentar & tayangan</div>
                </div>
              </Link>
              <Link href="/forum?sort=populer_tayangan" @click="popOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <div>
                  <div class="font-medium text-slate-800 dark:text-slate-100">Populer Tayangan</div>
                  <div class="text-xs text-slate-400">Berdasarkan jumlah tayangan</div>
                </div>
              </Link>
              <Link href="/forum?sort=populer_komentar" @click="popOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <div>
                  <div class="font-medium text-slate-800 dark:text-slate-100">Populer Komentar</div>
                  <div class="text-xs text-slate-400">Berdasarkan jumlah komentar</div>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Action Button -->
        <div class="w-full md:w-auto text-right">
          <Link href="/forum/create" class="inline-flex justify-center items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-semibold transition shadow-md w-full md:w-auto">
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
            :class="thread.is_pinned ? 'border-yellow-500' : 'border-slate-100 dark:border-slate-700'"
            class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-sm border card-hover transition mb-4 cursor-pointer"
            @click="router.visit(`/forum/${thread.id}`)"
          >
            <div class="flex items-start gap-4">
              <!-- Avatar -->
              <div class="hidden sm:flex w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-full items-center justify-center text-white font-bold text-lg shrink-0 shadow-inner">
                {{ thread.user?.name ? thread.user.name.charAt(0).toUpperCase() : 'U' }}
              </div>

              <div class="flex-grow min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <span v-if="thread.is_pinned" class="bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-300 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
                    Pinned
                  </span>
                  <h3 class="text-lg font-bold text-slate-800 dark:text-white truncate">
                    <Link :href="`/forum/${thread.id}`" class="hover:text-red-500 transition">
                      {{ thread.judul }}
                    </Link>
                  </h3>
                </div>

                <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-2 mb-3">
                  {{ thread.konten ? thread.konten.replace(/<[^>]*>/g, '').substring(0, 100) : '' }}
                </p>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                  <span class="font-medium text-slate-700 dark:text-slate-300">
                    {{ thread.user?.name || 'Anonymous' }}
                  </span>

                  <span v-if="thread.category" class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-slate-600 dark:text-slate-300">
                    {{ thread.category.nama_kategori }}
                  </span>

                  <div class="flex items-center gap-3 ml-auto">
                    <span class="flex items-center gap-1 font-semibold text-red-500">
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

        <div v-else class="text-center py-16 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
          <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Belum ada topik diskusi</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-6">Jadilah yang pertama memulai diskusi di forum ini!</p>
          <Link href="/forum/create" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">
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

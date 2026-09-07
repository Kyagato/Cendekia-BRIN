<template>
  <PublicLayout>
    <div class="py-8 container mx-auto px-4 max-w-4xl space-y-6">

      <!-- Flash Notification -->
      <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="flashSuccess && showFlash" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3 text-sm shadow-sm">
          <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          {{ flashSuccess }}
        </div>
      </transition>

      <!-- Top Action Bar -->
      <div class="flex flex-wrap items-center justify-between gap-4">
        <Link href="/forum" class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 text-sm font-semibold transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          Kembali ke Forum
        </Link>

        <!-- Moderator Actions -->
        <div v-if="canManageForum" class="flex items-center gap-2">
          <button @click="togglePin" :class="thread.is_pinned ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 border-yellow-300' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700'" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md font-semibold border transition">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
            {{ thread.is_pinned ? 'Unpin' : 'Pin' }}
          </button>
          <button @click="toggleLock" :class="thread.is_locked ? 'bg-orange-100 dark:bg-orange-900/40 text-orange-800 dark:text-orange-300 border-orange-300' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700'" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md font-semibold border transition">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
            {{ thread.is_locked ? 'Buka Kunci' : 'Kunci' }}
          </button>
          <button @click="confirmDelete" class="inline-flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md font-semibold bg-red-600 hover:bg-red-700 text-white transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus
          </button>
        </div>
      </div>

      <!-- Thread Card -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border dark:border-slate-700 overflow-hidden" :class="thread.is_pinned ? 'border-yellow-400' : 'border-slate-200'">
        <div class="p-6 sm:p-8">
          <!-- Meta Badges -->
          <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-4">
            <span v-if="thread.is_pinned" class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
              📌 Pinned
            </span>
            <span v-if="thread.is_locked" class="bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 text-xs px-2 py-0.5 rounded flex items-center gap-1 font-semibold">
              🔒 Dikunci
            </span>
            <span v-if="thread.category" class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded text-xs font-medium">
              {{ thread.category.nama_kategori }}
            </span>
            <span class="text-xs">{{ formatDate(thread.created_at) }}</span>
            <span class="flex items-center gap-1 text-xs">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              {{ thread.views_count || 0 }} tayangan
            </span>
          </div>

          <!-- Linked Knowledge Banner -->
          <div v-if="thread.knowledge" class="mb-6 p-4 bg-red-50 dark:bg-red-950/30 border border-red-100 dark:border-red-900/50 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
              </div>
              <div>
                <span class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider block">Membahas Pengetahuan</span>
                <a :href="`/knowledge/${thread.knowledge.id}`" class="text-sm font-semibold text-slate-800 dark:text-slate-200 hover:text-red-600 transition">
                  {{ thread.knowledge.judul }}
                </a>
              </div>
            </div>
            <a :href="`/knowledge/${thread.knowledge.id}`" class="shrink-0 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
              Lihat Materi
            </a>
          </div>

          <!-- Thread Title & Content -->
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-6">{{ thread.judul }}</h1>
          <div class="prose prose-slate dark:prose-invert max-w-none text-sm text-slate-700 dark:text-slate-300 leading-relaxed mb-8" v-html="thread.konten"></div>

          <!-- Author Info -->
          <div class="flex items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-700">
            <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
              {{ thread.user?.name ? thread.user.name.charAt(0).toUpperCase() : 'U' }}
            </div>
            <div>
              <p class="font-semibold text-slate-800 dark:text-slate-100 text-sm">{{ thread.user?.name || 'Anonymous' }}</p>
              <p class="text-xs text-slate-400">{{ thread.user?.instansi || '' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Reply Count -->
      <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">
        {{ replies?.total || 0 }} Balasan
      </h2>

      <!-- Replies List -->
      <div v-if="replies?.data?.length > 0" class="space-y-4">
        <div v-for="reply in replies.data" :key="reply.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
          <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-500 to-rose-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
              {{ reply.user?.name ? reply.user.name.charAt(0).toUpperCase() : 'U' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-2">
                <span class="font-semibold text-sm text-slate-800 dark:text-slate-100">{{ reply.user?.name || 'Anonymous' }}</span>
                <span class="text-xs text-slate-400">{{ formatDate(reply.created_at) }}</span>
              </div>
              <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed" v-html="reply.konten"></div>

              <!-- Nested Replies -->
              <div v-if="reply.replies?.length > 0" class="mt-4 space-y-3 pl-4 border-l-2 border-slate-200 dark:border-slate-600">
                <div v-for="nested in reply.replies" :key="nested.id" class="flex items-start gap-2">
                  <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs shrink-0">
                    {{ nested.user?.name ? nested.user.name.charAt(0).toUpperCase() : 'U' }}
                  </div>
                  <div>
                    <div class="flex items-center gap-2 mb-1">
                      <span class="font-semibold text-xs text-slate-700 dark:text-slate-200">{{ nested.user?.name }}</span>
                      <span class="text-xs text-slate-400">{{ formatDate(nested.created_at) }}</span>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-300" v-html="nested.konten"></div>
                  </div>
                </div>
              </div>

              <!-- Reply Button -->
              <button v-if="!thread.is_locked && user" @click="replyTo = reply" class="mt-3 text-xs text-red-500 hover:text-red-700 font-semibold">
                Balas
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Reply Form -->
      <div v-if="user && !thread.is_locked" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-5">
        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-3">
          {{ replyTo ? `Membalas: ${replyTo.user?.name}` : 'Tulis Balasan Anda' }}
          <button v-if="replyTo" @click="replyTo = null" class="ml-2 text-xs text-slate-400 hover:text-red-500 transition">✕ Batal</button>
        </h3>
        <form @submit.prevent="submitReply">
          <textarea v-model="replyForm.konten" rows="4" required placeholder="Tulis balasan Anda di sini..." class="w-full px-3 py-2.5 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-sm focus:border-red-500 focus:outline-none transition resize-none"></textarea>
          <div class="flex justify-end mt-3">
            <button type="submit" :disabled="replyForm.processing" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-2">
              <svg v-if="replyForm.processing" class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
              Kirim Balasan
            </button>
          </div>
        </form>
      </div>

      <div v-else-if="!user" class="text-center py-8 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">Silakan masuk untuk memberikan balasan.</p>
        <Link href="/login" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition">
          Masuk Sekarang
        </Link>
      </div>

      <div v-else-if="thread.is_locked" class="text-center py-6 bg-orange-50 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-900 rounded-xl">
        <p class="text-sm text-orange-600 dark:text-orange-400 font-semibold">🔒 Topik ini sudah dikunci. Balasan baru tidak dapat ditambahkan.</p>
      </div>

    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const props = defineProps({
  thread: Object,
  replies: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

watch(flashSuccess, (val) => {
  if (val) {
    showFlash.value = true;
    setTimeout(() => { showFlash.value = false; }, 3000);
  }
}, { immediate: true });

const canManageForum = computed(() => {
  if (!user.value) return false;
  return ['Super Admin', 'Admin Pusat', 'Admin IPPD', 'Moderator'].includes(user.value.role);
});

const replyTo = ref(null);

const replyForm = useForm({
  konten: '',
  parent_id: null,
});

const submitReply = () => {
  replyForm.parent_id = replyTo.value?.id ?? null;
  replyForm.post(`/forum/${props.thread.id}/reply`, {
    onSuccess: () => {
      replyForm.reset();
      replyTo.value = null;
    }
  });
};

const togglePin = () => {
  router.patch(`/forum/${props.thread.id}/pin`);
};

const toggleLock = () => {
  router.patch(`/forum/${props.thread.id}/lock`);
};

const confirmDelete = () => {
  if (confirm('Yakin ingin menghapus topik ini?')) {
    router.delete(`/forum/${props.thread.id}`);
  }
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
};
</script>

<template>
  <PublicLayout>
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      <!-- Flash Notification Auto-dismiss 3 Detik -->
      <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-[-10px]"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="flashSuccess && showFlash" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm text-sm">
          <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>{{ flashSuccess }}</span>
        </div>
      </transition>

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Atur Forum</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola dan moderasi topik diskusi yang masuk dari anggota.</p>
        </div>
        <Link href="/forum" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 border border-slate-700 hover:bg-slate-700 text-slate-200 rounded-lg text-sm font-medium transition shadow-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Lihat Forum Publik
        </Link>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center gap-4 shadow-sm">
          <div class="w-10 h-10 bg-amber-950 border border-amber-800 rounded-lg flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-100">{{ counts?.pending || 0 }}</div>
            <div class="text-xs font-semibold text-slate-400">Menunggu Validasi</div>
          </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center gap-4 shadow-sm">
          <div class="w-10 h-10 bg-emerald-950 border border-emerald-800 rounded-lg flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-100">{{ counts?.approved || 0 }}</div>
            <div class="text-xs font-semibold text-slate-400">Disetujui</div>
          </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center gap-4 shadow-sm">
          <div class="w-10 h-10 bg-red-950 border border-red-800 rounded-lg flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-100">{{ counts?.rejected || 0 }}</div>
            <div class="text-xs font-semibold text-slate-400">Ditolak</div>
          </div>
        </div>
      </div>

      <!-- Main Container & Tab Filter -->
      <div class="bg-slate-800 rounded-xl border border-slate-700 shadow-sm overflow-hidden">
        <div class="flex border-b border-slate-700 overflow-x-auto bg-slate-900/50">
          <Link 
            v-for="t in ['pending', 'approved', 'rejected']" 
            :key="t"
            :href="`/moderator/forum/approval?status=${t}`"
            :class="status === t ? 'border-red-500 text-red-400 bg-slate-800' : 'border-transparent text-slate-400 hover:text-slate-200'"
            class="flex items-center gap-2 px-6 py-4 text-xs font-semibold border-b-2 whitespace-nowrap transition"
          >
            <span class="capitalize">{{ t === 'pending' ? 'Menunggu' : (t === 'approved' ? 'Disetujui' : 'Ditolak') }}</span>
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-700 text-slate-300">
              {{ counts?.[t] || 0 }}
            </span>
          </Link>
        </div>

        <!-- Table Content -->
        <div v-if="threads && threads.data && threads.data.length > 0" class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-900 border-b border-slate-700">
                <th class="py-3 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wide">Penulis</th>
                <th class="py-3 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wide">Judul &amp; Cuplikan</th>
                <th class="py-3 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kategori</th>
                <th class="py-3 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                <th class="py-3 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wide text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
              <tr v-for="thread in threads.data" :key="thread.id" class="hover:bg-slate-700/50 transition">
                <td class="py-3 px-6 min-w-[160px]">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white text-xs font-bold shrink-0">
                      {{ thread.user?.name ? thread.user.name.charAt(0).toUpperCase() : 'U' }}
                    </div>
                    <div class="min-w-0">
                      <div class="font-medium text-slate-100 text-xs truncate">{{ thread.user?.name || 'Unknown' }}</div>
                    </div>
                  </div>
                </td>

                <td class="py-3 px-6 max-w-sm">
                  <a :href="`/forum/${thread.id}`" target="_blank" class="font-semibold text-slate-100 hover:text-red-400 transition line-clamp-1 text-sm block">
                    {{ thread.judul }}
                  </a>
                  <p class="text-slate-400 text-xs mt-0.5 line-clamp-2">
                    {{ thread.konten ? thread.konten.replace(/<[^>]*>/g, '').substring(0, 120) : '' }}
                  </p>
                  <div v-if="thread.rejection_note && status === 'rejected'" class="mt-1.5 px-2.5 py-1 bg-red-950 border border-red-800 rounded text-xs text-red-300">
                    <span class="font-semibold">Alasan:</span> {{ thread.rejection_note }}
                  </div>
                </td>

                <td class="py-3 px-6 min-w-[120px]">
                  <span v-if="thread.category" class="inline-block px-2 py-1 bg-slate-700 text-slate-300 rounded text-xs font-medium border border-slate-600">
                    {{ thread.category.nama_kategori }}
                  </span>
                  <span v-else class="text-slate-500 text-xs">—</span>
                </td>

                <td class="py-3 px-6 min-w-[110px]">
                  <span v-if="thread.status === 'pending'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-950 border border-amber-800 text-amber-300 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Menunggu
                  </span>
                  <span v-else-if="thread.status === 'approved'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-950 border border-emerald-800 text-emerald-300 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui
                  </span>
                  <span v-else-if="thread.status === 'rejected'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-red-950 border border-red-800 text-red-300 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Ditolak
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-3 px-6 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button v-if="thread.status !== 'approved'" @click="approveThread(thread.id)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold transition">
                      Setujui
                    </button>
                    <button v-if="thread.status !== 'rejected'" @click="openRejectModal(thread)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold transition">
                      Tolak
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-20 text-center">
          <h3 class="text-base font-bold text-slate-300 mb-1">Tidak ada topik diskusi</h3>
          <p class="text-slate-500 text-xs">Tidak ada topik dengan status <span class="capitalize">{{ status }}</span> saat ini.</p>
        </div>
      </div>

      <!-- Reject Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-md w-full p-6 text-left space-y-4 shadow-2xl">
          <h3 class="text-base font-bold text-slate-100">Tolak Topik Diskusi</h3>
          <p class="text-xs text-slate-400">"{{ selectedThread?.judul }}"</p>

          <div>
            <label class="block text-xs font-semibold text-slate-200 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
            <textarea v-model="rejectionNote" rows="4" required placeholder="Tuliskan alasan penolakan..." class="w-full px-3 py-2.5 rounded-lg border border-slate-600 bg-slate-900 text-slate-100 text-xs focus:border-red-500 focus:outline-none"></textarea>
          </div>

          <div class="flex justify-end gap-3">
            <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 hover:bg-slate-700 transition">Batal</button>
            <button type="button" @click="confirmReject" class="px-4 py-2 rounded-lg text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition">Konfirmasi Tolak</button>
          </div>
        </div>
      </div>

    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const props = defineProps({
  threads: Object,
  status: String,
  counts: Object,
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

watch(flashSuccess, (newVal) => {
  if (newVal) {
    showFlash.value = true;
    setTimeout(() => {
      showFlash.value = false;
    }, 3000);
  }
}, { immediate: true });

const showModal = ref(false);
const selectedThread = ref(null);
const rejectionNote = ref('');

const approveThread = (id) => {
  router.patch(`/moderator/forum/${id}/approve`);
};

const openRejectModal = (thread) => {
  selectedThread.value = thread;
  rejectionNote.value = '';
  showModal.value = true;
};

const confirmReject = () => {
  if (!rejectionNote.value) return;
  router.patch(`/moderator/forum/${selectedThread.value.id}/reject`, {
    rejection_note: rejectionNote.value
  }, {
    onSuccess: () => {
      showModal.value = false;
    }
  });
};
</script>

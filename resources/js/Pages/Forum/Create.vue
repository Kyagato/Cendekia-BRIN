<template>
  <PublicLayout>
    <div class="py-10 container mx-auto px-4 max-w-4xl space-y-6">
      <!-- Back to Forum -->
      <div>
        <Link
          href="/forum"
          class="inline-flex items-center gap-2 text-[#475569] dark:text-slate-400 hover:text-[#2563eb] dark:hover:text-blue-400 transition font-medium text-sm"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Kembali ke Forum
        </Link>
      </div>

      <!-- Main Card -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-[#e2e8f0] dark:border-slate-800 overflow-hidden">
        <!-- Card Header -->
        <div class="p-6 sm:p-8 border-b border-[#e2e8f0] dark:border-slate-800">
          <h1 class="text-2xl font-bold text-[#0f172a] dark:text-slate-100">Buat Topik Diskusi Baru</h1>
          <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Mulai diskusi, ajukan pertanyaan, atau bagikan wawasan dengan komunitas SPBE.</p>
        </div>

        <!-- Card Body / Form -->
        <div class="p-6 sm:p-8">
          <form @submit.prevent="submit">
            <!-- Hubungkan ke Materi Pengetahuan (Opsional) -->
            <div class="mb-6 relative">
              <label for="knowledge_search_input" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                Hubungkan ke Materi Pengetahuan <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span>
              </label>

              <!-- Single Search Input Field -->
              <div class="relative" ref="dropdownRef">
                <div class="relative">
                  <input
                    type="text"
                    id="knowledge_search_input"
                    v-model="searchKnowledge"
                    @focus="isDropdownOpen = true"
                    placeholder="🔍 Cari dan pilih materi pengetahuan..."
                    autocomplete="off"
                    class="w-full pl-10 pr-10 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] transition text-sm"
                  />
                  <!-- Search Icon -->
                  <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>

                  <!-- Clear Button -->
                  <button
                    type="button"
                    v-if="searchKnowledge"
                    @click="clearKnowledge"
                    class="absolute right-3 top-3.5 text-slate-400 hover:text-red-500 dark:hover:text-red-400"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>

                <!-- Floating Results Dropdown -->
                <div
                  v-if="isDropdownOpen && filteredKnowledges.length > 0"
                  class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-h-60 overflow-y-auto py-1 text-sm"
                >
                  <div
                    v-for="item in filteredKnowledges"
                    :key="item.id"
                    @click="selectKnowledge(item)"
                    class="px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-between transition border-b border-slate-50 dark:border-slate-800/50 last:border-0"
                  >
                    <div>
                      <span class="font-medium text-slate-800 dark:text-slate-200 block">{{ item.judul }}</span>
                      <span class="text-xs text-slate-400 dark:text-slate-500">{{ item.category?.nama_kategori || 'Umum' }}</span>
                    </div>
                    <span v-if="form.knowledge_id == item.id" class="text-blue-600 dark:text-blue-400 text-xs font-semibold">✓ Terpilih</span>
                  </div>
                </div>

                <div
                  v-if="isDropdownOpen && searchKnowledge && filteredKnowledges.length === 0"
                  class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl p-4 text-center text-xs text-slate-400 dark:text-slate-500"
                >
                  Tidak ada materi pengetahuan yang cocok dengan "{{ searchKnowledge }}"
                </div>
              </div>
              <div v-if="form.errors.knowledge_id" class="text-sm text-red-500 mt-1">
                {{ form.errors.knowledge_id }}
              </div>
            </div>

            <!-- Judul Topik -->
            <div class="mb-6">
              <label for="judul" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                Judul Topik <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                id="judul"
                v-model="form.judul"
                required
                placeholder="Contoh: Bagaimana cara mengimplementasikan arsitektur SPBE?"
                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] transition text-sm"
              />
              <div v-if="form.errors.judul" class="text-sm text-red-500 mt-1">
                {{ form.errors.judul }}
              </div>
            </div>

            <!-- Kategori -->
            <div class="mb-6">
              <label for="category_id" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                Kategori <span class="text-red-500">*</span>
              </label>
              <select
                id="category_id"
                v-model="form.category_id"
                required
                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] transition text-sm"
              >
                <option value="" disabled class="text-slate-400 dark:text-slate-500">Pilih Kategori</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.nama_kategori }}
                </option>
              </select>
              <div v-if="form.errors.category_id" class="text-sm text-red-500 mt-1">
                {{ form.errors.category_id }}
              </div>
            </div>

            <!-- Konten / Pertanyaan -->
            <div class="mb-8">
              <label for="konten" class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                Konten / Pertanyaan <span class="text-red-500">*</span>
              </label>
              <textarea
                id="konten"
                v-model="form.konten"
                rows="8"
                required
                placeholder="Jelaskan secara detail topik diskusi atau pertanyaan Anda di sini..."
                class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-[#2563eb] focus:ring-1 focus:ring-[#2563eb] transition resize-y text-sm"
              ></textarea>
              <div v-if="form.errors.konten" class="text-sm text-red-500 mt-1">
                {{ form.errors.konten }}
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
              <Link
                href="/forum"
                class="px-6 py-3 rounded-lg font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-sm flex items-center justify-center"
              >
                Batal
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-3 bg-[#2563eb] hover:bg-[#1d4ed8] text-white font-semibold rounded-lg shadow-sm transition text-sm disabled:opacity-50 flex items-center gap-2"
              >
                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>{{ form.processing ? 'Mengirim...' : 'Kirim Topik' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
  knowledges: {
    type: Array,
    default: () => [],
  },
  linkedKnowledge: {
    type: Object,
    default: null,
  },
});

const form = useForm({
  knowledge_id: props.linkedKnowledge?.id || '',
  judul: props.linkedKnowledge ? `Diskusi: ${props.linkedKnowledge.judul}` : '',
  category_id: props.linkedKnowledge?.category_id || '',
  konten: '',
});

const searchKnowledge = ref(props.linkedKnowledge?.judul || '');
const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

const filteredKnowledges = computed(() => {
  if (!searchKnowledge.value) {
    return props.knowledges;
  }
  const currentSelected = props.knowledges.find(k => k.id == form.knowledge_id);
  if (currentSelected && searchKnowledge.value === currentSelected.judul) {
    return props.knowledges;
  }
  const q = searchKnowledge.value.toLowerCase();
  return props.knowledges.filter(k => 
    k.judul?.toLowerCase().includes(q) || 
    (k.category?.nama_kategori && k.category.nama_kategori.toLowerCase().includes(q))
  );
});

const selectKnowledge = (item) => {
  form.knowledge_id = item.id;
  searchKnowledge.value = item.judul;
  isDropdownOpen.value = false;
  if (item.category_id) {
    form.category_id = item.category_id;
  }
  if (!form.judul || form.judul.startsWith('Diskusi: ')) {
    form.judul = `Diskusi: ${item.judul}`;
  }
};

const clearKnowledge = () => {
  form.knowledge_id = '';
  searchKnowledge.value = '';
  isDropdownOpen.value = false;
};

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isDropdownOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const submit = () => {
  form.post('/forum');
};
</script>

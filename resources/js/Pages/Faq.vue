<template>
  <PublicLayout>
    <!-- Header -->
    <section class="relative py-14 bg-[#1e3a8a] text-white overflow-hidden">

      <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3 tracking-tight">Pertanyaan yang Sering Diajukan (FAQ)</h1>
        <p class="text-base text-blue-100 max-w-2xl mx-auto">Panduan operasional, tata kelola, dan jawaban atas pertanyaan umum seputar MojoPedia</p>
      </div>
    </section>

    <section class="py-12 container mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Search Bar -->
      <div class="max-w-2xl mx-auto mb-12">
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-[#e2e8f0] dark:border-slate-800 p-1.5 flex items-center focus-within:ring-2 focus-within:ring-[#2563eb] transition-all">
          <div class="pl-3.5 pr-2 text-[#94a3b8]">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          </div>
          <input v-model="searchFaq" type="text" placeholder="Ketik kata kunci pertanyaan..." class="w-full bg-transparent border-none text-[#0f172a] dark:text-slate-100 px-2 py-2 text-sm focus:outline-none focus:ring-0 placeholder-[#94a3b8]">
        </div>
      </div>

      <div class="max-w-3xl mx-auto">
        <div v-if="faqs && Object.keys(faqs).length > 0">
          <div v-for="(items, kategori) in faqs" :key="kategori" class="mb-10">
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#e2e8f0] dark:border-slate-800">
              <div class="w-1.5 h-5 bg-[#2563eb] rounded-full"></div>
              <h2 class="text-lg font-bold text-[#0f172a] dark:text-white">{{ kategori }}</h2>
            </div>

            <div class="space-y-3">
              <div 
                v-for="faq in items" 
                :key="faq.id" 
                v-show="isFaqVisible(faq)"
                class="bg-white dark:bg-slate-900 rounded-lg border border-[#e2e8f0] dark:border-slate-800 overflow-hidden transition-all card-hover"
              >
                <button @click="toggleFaq(faq.id)" class="w-full px-5 py-4 text-left flex justify-between items-center focus:outline-none hover:bg-[#eff6ff]/50 dark:hover:bg-slate-800 transition-colors">
                  <span class="font-semibold text-sm sm:text-base text-[#0f172a] dark:text-slate-100 pr-4" :class="openFaqs[faq.id] ? 'text-[#2563eb] dark:text-blue-400' : ''">{{ faq.pertanyaan }}</span>
                  <svg class="w-5 h-5 text-[#94a3b8] transform transition-transform duration-200 shrink-0" :class="openFaqs[faq.id] ? 'rotate-180 text-[#2563eb]' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div v-if="openFaqs[faq.id]" class="px-5 pb-5 text-[#475569] dark:text-slate-300 text-sm leading-relaxed border-t border-[#f1f5f9] dark:border-slate-800 pt-3 whitespace-pre-line">
                  {{ faq.jawaban }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12 text-[#94a3b8] text-sm">
          Belum ada data pertanyaan untuk kategori ini.
        </div>
      </div>
    </section>

    <!-- Support Section -->
    <section class="py-12 bg-[#f1f5f9]/60 dark:bg-slate-900/60 border-t border-[#e2e8f0] dark:border-slate-800">
      <div class="container mx-auto px-4 text-center max-w-xl">
        <h3 class="text-xl font-bold text-[#0f172a] dark:text-white mb-2">Butuh Bantuan Lebih Lanjut?</h3>
        <p class="text-[#475569] dark:text-slate-400 text-xs sm:text-sm mb-6">Tim helpdesk MojoPedia siap membantu kendala teknis atau pengunggahan berkas.</p>
        <a href="mailto:diskominfo@mojokertokab.go.id" class="inline-flex items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
          Hubungi Helpdesk MojoPedia
        </a>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import PublicLayout from '../Layouts/PublicLayout.vue';

const props = defineProps({
  faqs: Object
});

const searchFaq = ref('');
const openFaqs = ref({});

const toggleFaq = (id) => {
  openFaqs.value[id] = !openFaqs.value[id];
};

const isFaqVisible = (faq) => {
  if (!searchFaq.value) return true;
  const q = searchFaq.value.toLowerCase();
  return (faq.pertanyaan && faq.pertanyaan.toLowerCase().includes(q)) ||
         (faq.jawaban && faq.jawaban.toLowerCase().includes(q));
};
</script>

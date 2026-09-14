<template>
  <div class="bg-white dark:bg-slate-900 rounded-lg border border-[#e2e8f0] dark:border-slate-800 card-hover flex flex-col h-full overflow-hidden group">
    <div class="p-4 flex-grow flex flex-col justify-between">
      <div>
        <!-- Header Badges: Media Badge (Left) & Department / Category Badge (Right) -->
        <div class="flex items-center justify-between gap-2 mb-3">
          <span :class="badgeClass" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full tracking-wide">
            <!-- Media Icon -->
            <svg v-if="item.tipe === 'Video'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <svg v-else-if="item.tipe === 'Audio'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
            <svg v-else-if="item.tipe === 'Gambar'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            {{ item.tipe || 'Teks' }}
          </span>

          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#f1f5f9] text-[#475569] dark:bg-slate-800 dark:text-slate-300 truncate max-w-[50%]">
            {{ item.category?.nama_kategori || 'Umum' }}
          </span>
        </div>

        <!-- Title & Content -->
        <div class="space-y-1.5">
          <h3 class="text-base font-semibold text-[#0f172a] dark:text-white line-clamp-2 leading-snug group-hover:text-[#2563eb] dark:group-hover:text-blue-400 transition-colors">
            <a :href="`/knowledge/${item.id}`">
              {{ item.judul }}
            </a>
          </h3>
          <p class="text-[13px] text-[#475569] dark:text-slate-400 line-clamp-2 leading-relaxed">
            {{ truncatedDescription }}
          </p>
        </div>
      </div>

      <!-- Metadata Row: Date & Views -->
      <div class="mt-4 pt-3 border-t border-[#e2e8f0] dark:border-slate-800 flex items-center justify-between text-xs text-[#94a3b8] dark:text-slate-500">
        <div class="flex items-center gap-1.5 text-[12px] font-medium">
          <svg class="w-3.5 h-3.5 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span>{{ timeAgo }}</span>
        </div>

        <div class="flex items-center gap-1 text-[12px] font-medium">
          <svg class="w-3.5 h-3.5 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          <span>{{ item.views_count || 0 }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  item: {
    type: Object,
    required: true
  }
});

const badgeClass = computed(() => {
  switch (props.item.tipe) {
    case 'Teks': 
      return 'bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950/80 dark:text-blue-300';
    case 'Video': 
      return 'bg-[#fef2f2] text-[#dc2626] dark:bg-red-950/80 dark:text-red-300';
    case 'Audio': 
      return 'bg-[#f0fdf4] text-[#16a34a] dark:bg-emerald-950/80 dark:text-emerald-300';
    case 'Gambar': 
      return 'bg-[#fefce8] text-[#ca8a04] dark:bg-yellow-950/80 dark:text-yellow-300';
    default: 
      return 'bg-[#eff6ff] text-[#2563eb] dark:bg-blue-950/80 dark:text-blue-300';
  }
});

const truncatedDescription = computed(() => {
  if (!props.item.deskripsi) return '';
  const cleanText = props.item.deskripsi.replace(/<[^>]*>/g, '');
  return cleanText.length > 90 ? cleanText.substring(0, 90) + '...' : cleanText;
});

const timeAgo = computed(() => {
  if (!props.item.created_at) return '';
  const date = new Date(props.item.created_at);
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
});
</script>

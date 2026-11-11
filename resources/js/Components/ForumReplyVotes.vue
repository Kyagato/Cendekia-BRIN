<template>
  <div class="inline-flex items-center gap-1" role="group" aria-label="Suara balasan">
    <button v-for="option in options" :key="option.value" type="button"
      :aria-label="option.label" :title="option.label" :aria-pressed="vote === option.value"
      :disabled="pending" @click="submit(option.value)"
      class="inline-flex items-center gap-1 rounded-md px-1.5 py-1 text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:opacity-50 disabled:cursor-wait cursor-pointer"
      :class="vote === option.value ? option.active : 'text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-600 dark:hover:text-slate-300'">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="option.value === 1 ? 'M12 19V5m-6 6 6-6 6 6' : 'M12 5v14m-6-6 6 6 6-6'" />
      </svg>
      <span>{{ option.value === 1 ? upvotes : downvotes }}</span>
    </button>
    <span v-if="error" role="alert" class="text-xs text-red-600 dark:text-red-400">{{ error }}</span>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({ reply: { type: Object, required: true } });
const vote = ref(0);
const upvotes = ref(0);
const downvotes = ref(0);
const pending = ref(false);
const error = ref('');
const options = [
  { value: 1, label: 'Upvote', active: 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40' },
  { value: -1, label: 'Downvote', active: 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40' },
];
watch(() => [props.reply.id, props.reply.user_vote, props.reply.upvotes, props.reply.downvotes], () => {
  vote.value = Number(props.reply.user_vote || 0);
  upvotes.value = Number(props.reply.upvotes || 0);
  downvotes.value = Number(props.reply.downvotes || 0);
}, { immediate: true });

async function submit(value) {
  if (pending.value) return;
  pending.value = true;
  error.value = '';
  try {
    const { data } = await axios.put(`/forum/reply/${props.reply.id}/vote`, { value: vote.value === value ? 0 : value });
    vote.value = data.user_vote;
    upvotes.value = data.upvotes;
    downvotes.value = data.downvotes;
  } catch (e) {
    error.value = e.response?.status === 401 || e.response?.status === 419
      ? 'Sesi berakhir. Silakan muat ulang dan login.' : 'Suara belum tersimpan. Coba lagi.';
  } finally {
    pending.value = false;
  }
}
</script>

<template>
  <div v-if="user" class="inline-block">
    <!-- Trigger Slot: Default to link with user's name -->
    <slot :user="user">
      <a
        :href="`/users/${user.id}`"
        class="font-semibold text-sm text-[#0f172a] dark:text-slate-100 hover:text-[#2563eb] dark:hover:text-blue-400 hover:underline transition"
      >
        {{ user.name || 'Anonymous' }}
      </a>
    </slot>
  </div>
  <span v-else class="font-semibold text-sm text-[#0f172a] dark:text-slate-100">
    Anonymous
  </span>
</template>

<script setup>
defineProps({
  user: {
    type: Object,
    default: null,
  },
});
</script>

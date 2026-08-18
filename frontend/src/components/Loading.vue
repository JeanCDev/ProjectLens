<script setup lang="ts">
defineProps<{
  loading?: boolean
  label?: string
}>()
</script>

<template>
  <div class="flex flex-col items-center justify-center py-14">
    <svg class="h-8 w-8 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
    <p v-if="label" class="mt-3 text-xs text-surface-500">{{ label }}</p>
  </div>
</template>
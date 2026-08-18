<script setup lang="ts">
import type { Component } from 'vue'

defineProps<{
  label: string
  value: string | number
  icon?: Component
  hint?: string
}>()
</script>

<template>
  <div class="rounded-lg border border-white/5 bg-white/[0.02] p-4">
    <div class="flex items-center justify-between">
      <p class="text-xs font-medium uppercase tracking-wider text-surface-500">{{ label }}</p>
      <component :is="icon" v-if="icon" class="h-4 w-4 text-surface-600" />
    </div>
    <p class="mt-2 text-lg font-semibold text-surface-800">{{ value }}</p>
    <p v-if="hint" class="mt-0.5 text-xs text-surface-500">{{ hint }}</p>
  </div>
</template>
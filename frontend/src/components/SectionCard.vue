<script setup lang="ts">
defineProps<{
  title: string
  subtitle?: string
  actions?: boolean
}>()
</script>

<template>
  <section class="card-surface overflow-hidden">
    <header class="flex items-center justify-between border-b border-white/5 px-5 py-4">
      <div>
        <h3 class="text-sm font-semibold text-surface-800">{{ title }}</h3>
        <p v-if="subtitle" class="mt-0.5 text-xs text-surface-500">{{ subtitle }}</p>
      </div>
      <div v-if="$slots.actions" class="flex items-center gap-2">
        <slot name="actions" />
      </div>
    </header>
    <div class="p-5">
      <slot />
    </div>
  </section>
</template>
<script setup lang="ts">
import Breadcrumb, { type BreadcrumbItem } from './Breadcrumb.vue'

defineProps<{
  title: string
  subtitle?: string
  breadcrumb?: BreadcrumbItem[]
}>()
</script>

<template>
  <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    <div>
      <Breadcrumb v-if="breadcrumb?.length" :items="breadcrumb" class="mb-3" />
      <h1 class="text-xl font-semibold tracking-tight text-surface-900">{{ title }}</h1>
      <p v-if="subtitle" class="mt-1 text-sm text-surface-500">{{ subtitle }}</p>
    </div>
    <div v-if="$slots.actions" class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>
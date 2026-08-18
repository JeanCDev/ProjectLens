<script setup lang="ts">
import { ChevronRightIcon } from '@heroicons/vue/20/solid'

export interface BreadcrumbItem {
  label: string
  to?: string
}

defineProps<{ items: BreadcrumbItem[] }>()
</script>

<template>
  <nav class="flex items-center gap-1.5 text-sm">
    <template v-for="(item, index) in items" :key="item.label">
      <component
        :is="item.to ? 'RouterLink' : 'span'"
        :to="item.to"
        class="transition-colors"
        :class="item.to ? 'cursor-pointer text-surface-500 hover:text-surface-300' : 'font-medium text-surface-300'"
      >
        {{ item.label }}
      </component>
      <ChevronRightIcon v-if="index < items.length - 1" class="h-3.5 w-3.5 text-surface-600" />
    </template>
  </nav>
</template>
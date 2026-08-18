<script setup lang="ts">
import type { Component } from 'vue'

defineProps<{
  title: string
  value: string | number
  icon: Component
  accent?: 'indigo' | 'emerald' | 'amber' | 'rose' | 'sky'
  trend?: { direction: 'up' | 'down'; text: string; positive?: boolean }
}>()

const accents: Record<string, string> = {
  indigo: 'text-indigo-300 bg-indigo-500/10',
  emerald: 'text-emerald-300 bg-emerald-500/10',
  amber: 'text-amber-300 bg-amber-500/10',
  rose: 'text-rose-300 bg-rose-500/10',
  sky: 'text-sky-300 bg-sky-500/10',
}
</script>

<template>
  <div class="card-surface group relative overflow-hidden p-5 transition-colors duration-200 hover:border-white/10">
    <div class="flex items-start justify-between">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="accents[accent ?? 'indigo']">
        <component :is="icon" class="h-5 w-5" />
      </div>
      <span
        v-if="trend"
        class="inline-flex items-center gap-1 text-xs font-medium"
        :class="trend.positive !== false ? 'text-emerald-300' : 'text-rose-300'"
      >
        <svg
          class="h-3 w-3"
          :class="trend.direction === 'down' && 'rotate-180'"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
        </svg>
        {{ trend.text }}
      </span>
    </div>
    <div class="mt-4">
      <p class="text-sm text-surface-500">{{ title }}</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight text-surface-900">{{ value }}</p>
    </div>
  </div>
</template>
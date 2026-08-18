<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  status: string
  label?: string
}>()

const config = computed<Record<string, { dot: string; text: string; bg: string; border: string }>>(() => ({
  active: {
    dot: 'bg-emerald-400',
    text: 'text-emerald-300',
    bg: 'bg-emerald-500/10',
    border: 'border-emerald-500/20',
  },
  degraded: {
    dot: 'bg-amber-400',
    text: 'text-amber-300',
    bg: 'bg-amber-500/10',
    border: 'border-amber-500/20',
  },
  down: {
    dot: 'bg-rose-400',
    text: 'text-rose-300',
    bg: 'bg-rose-500/10',
    border: 'border-rose-500/20',
  },
  released: {
    dot: 'bg-emerald-400',
    text: 'text-emerald-300',
    bg: 'bg-emerald-500/10',
    border: 'border-emerald-500/20',
  },
  in_progress: {
    dot: 'bg-indigo-400',
    text: 'text-indigo-300',
    bg: 'bg-indigo-500/10',
    border: 'border-indigo-500/20',
  },
  planned: {
    dot: 'bg-sky-400',
    text: 'text-sky-300',
    bg: 'bg-sky-500/10',
    border: 'border-sky-500/20',
  },
  rolled_back: {
    dot: 'bg-rose-400',
    text: 'text-rose-300',
    bg: 'bg-rose-500/10',
    border: 'border-rose-500/20',
  },
  maintenance: {
    dot: 'bg-amber-400',
    text: 'text-amber-300',
    bg: 'bg-amber-500/10',
    border: 'border-amber-500/20',
  },
  planning: {
    dot: 'bg-sky-400',
    text: 'text-sky-300',
    bg: 'bg-sky-500/10',
    border: 'border-sky-500/20',
  },
  archived: {
    dot: 'bg-surface-400',
    text: 'text-surface-500',
    bg: 'bg-surface-500/10',
    border: 'border-surface-500/20',
  },
  inactive: {
    dot: 'bg-surface-500',
    text: 'text-surface-500',
    bg: 'bg-surface-500/10',
    border: 'border-surface-500/20',
  },
}))

const entry = computed(
  () =>
    config.value[props.status] ?? {
      dot: 'bg-surface-400',
      text: 'text-surface-300',
      bg: 'bg-surface-500/10',
      border: 'border-surface-500/20',
    },
)

const display = computed(() => props.label ?? props.status.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase()))
</script>

<template>
  <span
    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize"
    :class="[entry.text, entry.bg, entry.border]"
  >
    <span class="h-1.5 w-1.5 rounded-full" :class="entry.dot" />
    {{ display }}
  </span>
</template>
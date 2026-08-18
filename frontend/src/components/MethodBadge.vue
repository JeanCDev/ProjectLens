<script setup lang="ts">
import { computed } from 'vue'
import type { HttpMethod } from '@/services/types'

const props = defineProps<{ method: HttpMethod }>()

const color = computed(() => {
  switch (props.method) {
    case 'GET':
      return 'text-emerald-300 bg-emerald-500/10 border-emerald-500/20'
    case 'POST':
      return 'text-sky-300 bg-sky-500/10 border-sky-500/20'
    case 'PUT':
      return 'text-amber-300 bg-amber-500/10 border-amber-500/20'
    case 'PATCH':
      return 'text-violet-300 bg-violet-500/10 border-violet-500/20'
    case 'DELETE':
      return 'text-rose-300 bg-rose-500/10 border-rose-500/20'
  }
})
</script>

<template>
  <span
    class="inline-flex min-w-14 items-center justify-center rounded-md border px-2 py-0.5 font-mono text-[11px] font-semibold"
    :class="color"
  >
    {{ method }}
  </span>
</template>
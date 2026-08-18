<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core'
import { ref, watch } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  modelValue: string
  placeholder?: string
  debounceMs?: number
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  search: [value: string]
}>()

const local = ref(props.modelValue)

watch(
  () => props.modelValue,
  (value) => {
    if (value !== local.value) local.value = value
  },
)

const emitSearch = useDebounceFn((value: string) => {
  emit('update:modelValue', value)
  emit('search', value)
}, props.debounceMs ?? 300)

function clear() {
  local.value = ''
  emitSearch('')
}
</script>

<template>
  <div class="relative">
    <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
    <input
      v-model="local"
      type="search"
      :placeholder="placeholder ?? 'Buscar...'"
      class="input-field pl-9 pr-8"
      @input="emitSearch(local)"
    />
    <button
      v-if="local"
      type="button"
      class="absolute right-2.5 top-1/2 -translate-y-1/2 text-surface-500 transition hover:text-surface-300"
      @click="clear"
    >
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>
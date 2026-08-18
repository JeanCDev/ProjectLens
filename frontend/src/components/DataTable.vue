<script setup lang="ts" generic="T extends Record<string, unknown>">
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { computed, ref, watch } from 'vue'
import EmptyState from './EmptyState.vue'
import Loading from './Loading.vue'

export interface Column<T> {
  key: keyof T & string
  label: string
  sortable?: boolean
  width?: string
}

const props = withDefaults(
  defineProps<{
    columns: Column<T>[]
    rows: T[]
    loading?: boolean
    pageSize?: number
    rowKey?: keyof T & string
  }>(),
  { loading: false, pageSize: 10, rowKey: 'id' as string & keyof T },
)

const emit = defineEmits<{
  'row-click': [row: T]
}>()

const page = ref(1)
const sortKey = ref<string | null>(null)
const sortDir = ref<'asc' | 'desc'>('asc')

watch(
  () => props.rows.length,
  () => {
    const max = Math.ceil(props.rows.length / props.pageSize)
    if (page.value > max && max > 0) page.value = max
  },
)

const sorted = computed(() => {
  if (!sortKey.value) return props.rows
  const key = sortKey.value
  const dir = sortDir.value === 'asc' ? 1 : -1
  return [...props.rows].sort((a, b) => {
    const av = a[key]
    const bv = b[key]
    if (typeof av === 'string' && typeof bv === 'string') return av.localeCompare(bv) * dir
    if (typeof av === 'number' && typeof bv === 'number') return (av - bv) * dir
    return String(av).localeCompare(String(bv)) * dir
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(sorted.value.length / props.pageSize)))
const paged = computed(() => {
  const start = (page.value - 1) * props.pageSize
  return sorted.value.slice(start, start + props.pageSize)
})

function toggleSort(key: string) {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortDir.value = 'asc'
  }
  page.value = 1
}
</script>

<template>
  <div>
    <div v-if="loading" class="py-4">
      <Loading />
    </div>
    <template v-else>
      <div v-if="rows.length === 0" class="py-4">
        <EmptyState title="Nenhum registro encontrado" description="Ajuste os filtros ou adicione novos registros." />
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="border-b border-white/5">
              <th
                v-for="column in columns"
                :key="column.key"
                :style="column.width ? { width: column.width } : undefined"
                class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-surface-500"
              >
                <button
                  v-if="column.sortable"
                  type="button"
                  class="inline-flex items-center gap-1 transition hover:text-surface-300"
                  @click="toggleSort(column.key)"
                >
                  {{ column.label }}
                  <svg
                    class="h-3 w-3"
                    :class="sortKey === column.key ? 'text-indigo-400' : 'text-surface-600'"
                    :style="sortKey === column.key && sortDir === 'desc' ? { transform: 'rotate(180deg)' } : undefined"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75 12 3m0 0 3.75 3.75M12 3v18" />
                  </svg>
                </button>
                <span v-else>{{ column.label }}</span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="row in paged"
              :key="String((row as Record<string, unknown>)[props.rowKey as string])"
              class="cursor-pointer border-b border-white/5 transition-colors last:border-0 hover:bg-white/[0.02]"
              @click="emit('row-click', row)"
            >
              <td v-for="column in columns" :key="column.key" class="px-4 py-3.5 text-sm">
                <slot :name="column.key" :row="row" :value="row[column.key]">
                  <span class="text-surface-400">{{ row[column.key] }}</span>
                </slot>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center justify-between border-t border-white/5 px-4 py-3">
        <p class="text-xs text-surface-500">
          {{ sorted.length }} registro{{ sorted.length === 1 ? '' : 's' }}
        </p>
        <div class="flex items-center gap-1">
          <button
            type="button"
            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-white/10 text-surface-400 transition hover:bg-white/5 disabled:opacity-40"
            :disabled="page === 1"
            @click="page--"
          >
            <ChevronLeftIcon class="h-4 w-4" />
          </button>
          <span class="px-2 text-xs text-surface-500">{{ page }} / {{ totalPages }}</span>
          <button
            type="button"
            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-white/10 text-surface-400 transition hover:bg-white/5 disabled:opacity-40"
            :disabled="page === totalPages"
            @click="page++"
          >
            <ChevronRightIcon class="h-4 w-4" />
          </button>
        </div>
      </div>
    </template>
  </div>
</template>
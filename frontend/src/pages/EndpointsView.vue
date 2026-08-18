<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { EndpointService } from '@/services/EndpointService'
import type { ApiEndpoint, HttpMethod } from '@/services/types'
import { timeAgo } from '@/utils/format'
import PageHeader from '@/components/PageHeader.vue'
import DataTable, { type Column } from '@/components/DataTable.vue'
import SearchInput from '@/components/SearchInput.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import MethodBadge from '@/components/MethodBadge.vue'
import Loading from '@/components/Loading.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

const router = useRouter()

const endpoints = ref<ApiEndpoint[]>([])
const loading = ref(true)
const search = ref('')
const methodFilter = ref('')
const statusFilter = ref('')

const columns: Column<ApiEndpoint>[] = [
  { key: 'method', label: 'Método' },
  { key: 'path', label: 'URL', sortable: true },
  { key: 'response_time_ms', label: 'Resposta', sortable: true },
  { key: 'status', label: 'Status' },
  { key: 'last_checked_at', label: 'Última verificação', sortable: true },
]

const methods: HttpMethod[] = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']
const statuses = ['active', 'degraded', 'down']

const filtered = computed(() => {
  let items = endpoints.value
  if (methodFilter.value) items = items.filter((e) => e.method === methodFilter.value)
  if (statusFilter.value) items = items.filter((e) => e.status === statusFilter.value)
  return items
})

function goToEndpoint(endpoint: ApiEndpoint) {
  router.push(`/endpoints/${endpoint.id}`)
}

onMounted(async () => {
  endpoints.value = await EndpointService.list()
  loading.value = false
})
</script>

<template>
  <div>
    <PageHeader
      title="Endpoints"
      subtitle="Monitore a saúde e o desempenho dos endpoints da sua API."
    >
      <template #actions>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3.5 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
        >
          <PlusIcon class="h-4 w-4" />
          Novo Endpoint
        </button>
      </template>
    </PageHeader>

    <div class="card-surface">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 p-4">
        <SearchInput v-model="search" placeholder="Buscar por URL ou descrição..." class="w-full sm:w-80" />
        <div class="flex flex-wrap items-center gap-2">
          <select v-model="methodFilter" class="input-field w-auto">
            <option value="">Método</option>
            <option v-for="method in methods" :key="method" :value="method">{{ method }}</option>
          </select>
          <select v-model="statusFilter" class="input-field w-auto">
            <option value="">Status</option>
            <option v-for="status in statuses" :key="status" :value="status">
              {{ status.charAt(0).toUpperCase() + status.slice(1) }}
            </option>
          </select>
        </div>
      </div>

      <Loading v-if="loading" label="Carregando endpoints..." />

      <DataTable
        v-else
        :columns="columns"
        :rows="filtered"
        :page-size="10"
        row-key="id"
        @row-click="goToEndpoint"
      >
        <template #method="{ row }">
          <MethodBadge :method="row.method" />
        </template>

        <template #path="{ row }">
          <div>
            <p class="font-mono text-[13px] font-medium text-surface-300">{{ row.path }}</p>
            <p class="mt-0.5 max-w-md truncate text-xs text-surface-600">{{ row.description }}</p>
          </div>
        </template>

        <template #response_time_ms="{ row }">
          <span
            class="font-mono text-xs"
            :class="{
              'text-emerald-300': row.response_time_ms < 200,
              'text-amber-300': row.response_time_ms >= 200 && row.response_time_ms < 500,
              'text-rose-300': row.response_time_ms >= 500,
            }"
          >
            {{ row.response_time_ms }}ms
          </span>
        </template>

        <template #status="{ row }">
          <StatusBadge :status="row.status" />
        </template>

        <template #last_checked_at="{ row }">
          <span class="text-xs text-surface-500">{{ timeAgo(row.last_checked_at) }}</span>
        </template>
      </DataTable>
    </div>
  </div>
</template>
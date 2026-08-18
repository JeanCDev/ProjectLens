<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ProjectService } from '@/services/ProjectService'
import type { Project } from '@/services/types'
import { formatDate } from '@/utils/format'
import PageHeader from '@/components/PageHeader.vue'
import DataTable, { type Column } from '@/components/DataTable.vue'
import SearchInput from '@/components/SearchInput.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Loading from '@/components/Loading.vue'
import { PlusIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline'

const router = useRouter()

const projects = ref<Project[]>([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')

const columns: Column<Project>[] = [
  { key: 'name', label: 'Nome', sortable: true },
  { key: 'framework', label: 'Framework', sortable: true },
  { key: 'language', label: 'Linguagem', sortable: true },
  { key: 'status', label: 'Status' },
  { key: 'owner', label: 'Owner', sortable: true },
  { key: 'last_deploy', label: 'Último Deploy', sortable: true },
]

const statuses = ['planning', 'active', 'maintenance', 'archived']

const filtered = computed(() => {
  let items = projects.value
  if (statusFilter.value) items = items.filter((p) => p.status === statusFilter.value)
  return items
})

function onSearch(query: string) {
  search.value = query
}

function goToProject(project: Project) {
  router.push(`/projects/${project.id}`)
}

onMounted(async () => {
  const { items } = await ProjectService.list()
  projects.value = items
  loading.value = false
})
</script>

<template>
  <div>
    <PageHeader
      title="Projects"
      subtitle="Gerencie projetos de software e acompanhe sua saúde."
    >
      <template #actions>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3.5 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
        >
          <PlusIcon class="h-4 w-4" />
          Novo Projeto
        </button>
      </template>
    </PageHeader>

    <div class="card-surface">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/5 p-4">
        <SearchInput v-model="search" placeholder="Buscar por nome, framework, linguagem..." class="w-full sm:w-80" @search="onSearch" />
        <div class="flex items-center gap-2">
          <span class="text-xs text-surface-500">Status:</span>
          <select
            v-model="statusFilter"
            class="input-field w-auto"
          >
            <option value="">Todos</option>
            <option v-for="status in statuses" :key="status" :value="status">
              {{ status.charAt(0).toUpperCase() + status.slice(1) }}
            </option>
          </select>
        </div>
      </div>

      <Loading v-if="loading" label="Carregando projetos..." />

      <DataTable
        v-else
        :columns="columns"
        :rows="filtered"
        :page-size="8"
        row-key="id"
        @row-click="goToProject"
      >
        <template #name="{ row }">
          <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/5 bg-white/[0.02] text-xs font-semibold text-surface-400">
              {{ (row as Project).name.charAt(0) }}
            </div>
            <div>
              <p class="font-medium text-surface-300">{{ row.name }}</p>
              <p class="text-xs text-surface-600">{{ row.description.slice(0, 48) }}…</p>
            </div>
          </div>
        </template>

        <template #framework="{ row }">
          <span class="text-surface-400">{{ row.framework }}</span>
        </template>

        <template #language="{ row }">
          <span class="inline-flex items-center gap-1.5 text-surface-400">
            <span class="h-2 w-2 rounded-full bg-indigo-400/70" />
            {{ row.language }}
          </span>
        </template>

        <template #status="{ row }">
          <StatusBadge :status="row.status" />
        </template>

        <template #owner="{ row }">
          <span class="text-surface-400">{{ row.owner }}</span>
        </template>

        <template #last_deploy="{ row }">
          <span class="font-mono text-xs text-surface-500">{{ formatDate(row.last_deploy) }}</span>
        </template>
      </DataTable>
    </div>
  </div>
</template>
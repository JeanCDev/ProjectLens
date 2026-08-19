<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { ProjectService } from '@/services/ProjectService'
import type { Project } from '@/services/types'
import PageHeader from '@/components/PageHeader.vue'
import DataTable, { type Column } from '@/components/DataTable.vue'
import SearchInput from '@/components/SearchInput.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Loading from '@/components/Loading.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

const router = useRouter()

const projects = ref<Project[]>([])
const total = ref(0)
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const languageFilter = ref('')

const columns: Column<Project>[] = [
  { key: 'name', label: 'Nome', sortable: true },
  { key: 'framework', label: 'Framework', sortable: true },
  { key: 'primary_language', label: 'Linguagem', sortable: true },
  { key: 'status', label: 'Status' },
]

const statuses = ['planning', 'active', 'on_hold', 'completed', 'archived']

const languageOptions = computed(() => {
  const langs = new Set<string>()
  for (const project of projects.value) {
    if (project.primary_language) langs.add(project.primary_language)
  }
  return [...langs].sort()
})

async function load() {
  loading.value = true
  try {
    const result = await ProjectService.list({
      search: search.value,
      status: statusFilter.value,
      language: languageFilter.value,
    })
    projects.value = result.items
    total.value = result.total
  } finally {
    loading.value = false
  }
}

function onSearch(query: string) {
  search.value = query
  load()
}

watch(statusFilter, load)
watch(languageFilter, load)

function goToProject(project: Project) {
  router.push(`/projects/${project.id}`)
}

onMounted(load)
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
        <SearchInput v-model="search" placeholder="Buscar por nome, descrição, linguagem..." class="w-full sm:w-80" @search="onSearch" />
        <div class="flex flex-wrap items-center gap-4">
          <div v-if="languageOptions.length > 0" class="flex items-center gap-2">
            <span class="text-xs text-surface-500">Linguagem:</span>
            <select
              v-model="languageFilter"
              class="input-field w-auto"
            >
              <option value="">Todas</option>
              <option v-for="language in languageOptions" :key="language" :value="language">
                {{ language }}
              </option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs text-surface-500">Status:</span>
            <select
              v-model="statusFilter"
              class="input-field w-auto"
            >
              <option value="">Todos</option>
              <option v-for="status in statuses" :key="status" :value="status">
                {{ status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ') }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between border-b border-white/5 px-4 py-2">
        <p class="text-xs text-surface-500">
          {{ total }} projeto{{ total === 1 ? '' : 's' }} no total
        </p>
        <span class="text-xs text-surface-700">{{ projects.length }} exibido{{ projects.length === 1 ? '' : 's' }}</span>
      </div>

      <Loading v-if="loading" label="Carregando projetos..." />

      <DataTable
        v-else
        :columns="columns"
        :rows="projects"
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
          <span class="text-surface-400">{{ row.framework ?? '—' }}</span>
        </template>

        <template #primary_language="{ row }">
          <span class="inline-flex items-center gap-1.5 text-surface-400">
            <span class="h-2 w-2 rounded-full bg-indigo-400/70" />
            {{ row.primary_language }}
          </span>
        </template>

        <template #status="{ row }">
          <StatusBadge :status="row.status" />
        </template>
      </DataTable>
    </div>
  </div>
</template>
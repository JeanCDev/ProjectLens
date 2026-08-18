<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { ProjectService } from '@/services/ProjectService'
import { EnvironmentService } from '@/services/EnvironmentService'
import { ReleaseService } from '@/services/ReleaseService'
import type { Environment, Project, Release } from '@/services/types'
import { formatDate, timeAgo } from '@/utils/format'
import PageHeader from '@/components/PageHeader.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import SectionCard from '@/components/SectionCard.vue'
import MetricCard from '@/components/MetricCard.vue'
import Loading from '@/components/Loading.vue'
import {
  CodeBracketIcon,
  GlobeAltIcon,
  LinkIcon,
  RocketLaunchIcon,
  ServerStackIcon,
  UsersIcon,
  ArrowPathRoundedSquareIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const projectId = Number(route.params.id)

const project = ref<Project | null>(null)
const environments = ref<Environment[]>([])
const releases = ref<Release[]>([])
const loading = ref(true)
const activeTab = ref('overview')

const tabs = [
  { id: 'overview', label: 'Overview' },
  { id: 'endpoints', label: 'Endpoints' },
  { id: 'releases', label: 'Releases' },
  { id: 'environments', label: 'Environments' },
  { id: 'members', label: 'Members' },
  { id: 'metrics', label: 'Metrics' },
]

const productionEnv = computed(() => environments.value.find((e) => e.type === 'production'))

onMounted(async () => {
  const [projectResult, environmentsResult, releasesResult] = await Promise.all([
    ProjectService.get(projectId),
    EnvironmentService.list(projectId),
    ReleaseService.list(projectId),
  ])
  project.value = projectResult
  environments.value = environmentsResult
  releases.value = releasesResult
  loading.value = false
})
</script>

<template>
  <div>
    <PageHeader
      v-if="project"
      :title="project.name"
      :subtitle="project.description"
      :breadcrumb="[{ label: 'Projects', to: '/projects' }, { label: project.name }]"
    >
      <template #actions>
        <StatusBadge :status="project.status" />
      </template>
    </PageHeader>

    <Loading v-if="loading" label="Carregando projeto..." />

    <template v-else-if="project">
      <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <MetricCard label="Endpoints" :value="project.endpoints_count" :icon="ArrowPathRoundedSquareIcon" />
        <MetricCard label="Releases" :value="project.releases_count" :icon="RocketLaunchIcon" />
        <MetricCard label="Ambientes" :value="project.environments_count" :icon="ServerStackIcon" />
        <MetricCard label="Membros" :value="project.members_count" :icon="UsersIcon" />
      </div>

      <div class="mb-6 flex gap-1 border-b border-white/5">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          type="button"
          class="border-b-2 px-4 py-2.5 text-sm font-medium transition-colors"
          :class="
            activeTab === tab.id
              ? 'border-indigo-400 text-surface-200'
              : 'border-transparent text-surface-500 hover:text-surface-300'
          "
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- OVERVIEW -->
      <div v-if="activeTab === 'overview'" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <SectionCard title="Sobre o projeto" class="lg:col-span-2">
          <p class="text-sm leading-relaxed text-surface-400">{{ project.description }}</p>
          <div class="mt-6 grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Framework</p>
              <p class="mt-1 flex items-center gap-2 text-sm font-medium text-surface-300">
                <CodeBracketIcon class="h-4 w-4 text-indigo-400" />
                {{ project.framework }}
              </p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Linguagem</p>
              <p class="mt-1 flex items-center gap-2 text-sm font-medium text-surface-300">
                <CodeBracketIcon class="h-4 w-4 text-indigo-400" />
                {{ project.language }}
              </p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Repositório</p>
              <p class="mt-1 flex items-center gap-2 text-sm font-medium text-surface-300">
                <LinkIcon class="h-4 w-4 text-indigo-400" />
                <span class="font-mono text-xs">{{ project.repository }}</span>
              </p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Criado em</p>
              <p class="mt-1 text-sm font-medium text-surface-300">{{ formatDate(project.created_at) }}</p>
            </div>
          </div>
        </SectionCard>

        <div class="space-y-6">
          <SectionCard title="Ambientes">
            <ul class="space-y-2">
              <li
                v-for="env in environments"
                :key="env.id"
                class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] px-3 py-2.5"
              >
                <div class="flex items-center gap-2.5">
                  <span class="h-2 w-2 rounded-full" :class="env.status === 'active' ? 'bg-emerald-400' : 'bg-surface-600'" />
                  <span class="text-sm text-surface-300">{{ env.name }}</span>
                </div>
                <span class="font-mono text-xs text-surface-500">{{ env.version }}</span>
              </li>
            </ul>
          </SectionCard>

          <SectionCard title="Deploy mais recente">
            <div v-if="releases.length > 0">
              <p class="font-mono text-sm font-semibold text-surface-200">{{ releases[0].version }}</p>
              <p class="mt-1 text-xs text-surface-500">
                {{ releases[0].author }} · {{ timeAgo(releases[0].released_at) }}
              </p>
              <div class="mt-3">
                <StatusBadge :status="releases[0].status" />
              </div>
            </div>
          </SectionCard>
        </div>
      </div>

      <!-- ENDPOINTS -->
      <div v-if="activeTab === 'endpoints'" class="card-surface">
        <div class="flex items-center justify-between border-b border-white/5 p-4">
          <h3 class="text-sm font-semibold text-surface-300">Endpoints da API</h3>
          <span class="text-xs text-surface-500">{{ project.endpoints_count }} no total</span>
        </div>
        <p class="p-8 text-center text-sm text-surface-500">
          Acesse a aba Endpoints no menu lateral para explorar os endpoints deste projeto.
        </p>
      </div>

      <!-- RELEASES -->
      <div v-if="activeTab === 'releases'" class="card-surface">
        <div class="border-b border-white/5 p-4">
          <h3 class="text-sm font-semibold text-surface-300">Histórico de releases</h3>
        </div>
        <ul class="divide-y divide-white/5">
          <li v-for="release in releases" :key="release.id" class="flex items-center gap-4 px-5 py-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/5 bg-white/[0.02]">
              <RocketLaunchIcon class="h-5 w-5 text-indigo-400" />
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2">
                <p class="font-mono text-sm font-semibold text-surface-200">{{ release.version }}</p>
                <StatusBadge :status="release.status" />
              </div>
              <p class="mt-0.5 truncate text-xs text-surface-500">
                {{ release.author }} · {{ formatDate(release.released_at) }}
              </p>
            </div>
            <span class="text-xs text-surface-600">{{ timeAgo(release.released_at) }}</span>
          </li>
        </ul>
      </div>

      <!-- ENVIRONMENTS -->
      <div v-if="activeTab === 'environments'" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        <div
          v-for="env in environments"
          :key="env.id"
          class="card-surface p-5"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <GlobeAltIcon class="h-5 w-5 text-indigo-400" />
              <p class="text-sm font-semibold text-surface-200">{{ env.name }}</p>
            </div>
            <StatusBadge :status="env.status" />
          </div>
          <div class="mt-4 space-y-2.5">
            <div class="flex justify-between">
              <span class="text-xs text-surface-600">URL</span>
              <span class="font-mono text-xs text-surface-400">{{ env.url }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-xs text-surface-600">Database</span>
              <span class="font-mono text-xs text-surface-400">{{ env.database }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-xs text-surface-600">Version</span>
              <span class="font-mono text-xs text-surface-400">{{ env.version }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- MEMBERS -->
      <div v-if="activeTab === 'members'" class="card-surface">
        <div class="border-b border-white/5 p-4">
          <h3 class="text-sm font-semibold text-surface-300">Membros da equipe</h3>
        </div>
        <p class="p-8 text-center text-sm text-surface-500">
          A listagem de membros estará disponível em breve nesta seção.
        </p>
      </div>

      <!-- METRICS -->
      <div v-if="activeTab === 'metrics'" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <MetricCard label="Uptime (30d)" value="99.9%" hint="Últimos 30 dias" />
        <MetricCard label="Latência média" value="187ms" hint="P95 em 412ms" />
        <MetricCard label="Taxa de erro" value="0.4%" hint="Meta: < 1%" />
      </div>
    </template>
  </div>
</template>
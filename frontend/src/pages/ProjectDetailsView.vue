<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ProjectService } from '@/services/ProjectService'
import { EnvironmentService } from '@/services/EnvironmentService'
import { ReleaseService } from '@/services/ReleaseService'
import { EndpointService } from '@/services/EndpointService'
import { TeamMemberService } from '@/services/TeamMemberService'
import type { ApiEndpoint, Environment, Project, Release, TeamMember } from '@/services/types'
import { formatDate, timeAgo } from '@/utils/format'
import PageHeader from '@/components/PageHeader.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import MethodBadge from '@/components/MethodBadge.vue'
import SectionCard from '@/components/SectionCard.vue'
import MetricCard from '@/components/MetricCard.vue'
import Loading from '@/components/Loading.vue'
import EmptyState from '@/components/EmptyState.vue'
import {
  CodeBracketIcon,
  GlobeAltIcon,
  LinkIcon,
  RocketLaunchIcon,
  ServerStackIcon,
  UsersIcon,
  ArrowPathRoundedSquareIcon,
  ArrowTopRightOnSquareIcon,
  BoltIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const projectId = Number(route.params.id)

const project = ref<Project | null>(null)
const environments = ref<Environment[]>([])
const releases = ref<Release[]>([])
const endpoints = ref<ApiEndpoint[]>([])
const teamMembers = ref<TeamMember[]>([])
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

const healthyEndpoints = computed(() => endpoints.value.filter((e) => e.status === 'healthy').length)
const downEndpoints = computed(() => endpoints.value.filter((e) => e.status === 'down').length)
const averageResponse = computed(() => {
  const values = endpoints.value
    .map((e) => e.response_time_ms)
    .filter((value): value is number => typeof value === 'number')
  if (values.length === 0) return 0
  return Math.round(values.reduce((sum, value) => sum + value, 0) / values.length)
})

function goToEndpoint(endpoint: ApiEndpoint) {
  router.push(`/projects/${projectId}/endpoints/${endpoint.id}`)
}

onMounted(async () => {
  const [projectResult, environmentsResult, releasesResult, endpointsResult, membersResult] = await Promise.allSettled([
    ProjectService.get(projectId),
    EnvironmentService.list(projectId),
    ReleaseService.list(projectId),
    EndpointService.list(projectId),
    TeamMemberService.list(projectId),
  ])
  if (projectResult.status === 'fulfilled') project.value = projectResult.value
  if (environmentsResult.status === 'fulfilled') environments.value = environmentsResult.value
  if (releasesResult.status === 'fulfilled') releases.value = releasesResult.value
  if (endpointsResult.status === 'fulfilled') endpoints.value = endpointsResult.value
  if (membersResult.status === 'fulfilled') teamMembers.value = membersResult.value
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
        <StatusBadge :status="project.status" :label="project.status_label" />
      </template>
    </PageHeader>

    <Loading v-if="loading" label="Carregando projeto..." />

    <template v-else-if="project">
      <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <MetricCard label="Endpoints" :value="endpoints.length" :icon="ArrowPathRoundedSquareIcon" />
        <MetricCard label="Releases" :value="releases.length" :icon="RocketLaunchIcon" />
        <MetricCard label="Ambientes" :value="environments.length" :icon="ServerStackIcon" />
        <MetricCard label="Membros" :value="teamMembers.length" :icon="UsersIcon" />
      </div>

      <div class="mb-6 flex gap-1 overflow-x-auto border-b border-white/5">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          type="button"
          class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors"
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
          <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Framework</p>
              <p class="mt-1 flex items-center gap-2 text-sm font-medium text-surface-300">
                <CodeBracketIcon class="h-4 w-4 text-indigo-400" />
                {{ project.framework ?? '—' }}
              </p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Linguagem</p>
              <p class="mt-1 flex items-center gap-2 text-sm font-medium text-surface-300">
                <CodeBracketIcon class="h-4 w-4 text-indigo-400" />
                {{ project.primary_language }}
              </p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Repositório</p>
              <a
                v-if="project.git_repository"
                :href="project.git_repository"
                target="_blank"
                rel="noopener"
                class="mt-1 inline-flex items-center gap-2 text-sm font-medium text-sky-300 transition hover:text-sky-200"
              >
                <LinkIcon class="h-4 w-4 text-indigo-400" />
                <span class="font-mono text-xs">{{ project.git_repository }}</span>
              </a>
              <p v-else class="mt-1 text-sm font-medium text-surface-500">—</p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Data de início</p>
              <p class="mt-1 text-sm font-medium text-surface-300">
                {{ project.start_date ? formatDate(project.start_date) : '—' }}
              </p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wider text-surface-600">Previsão de término</p>
              <p class="mt-1 text-sm font-medium text-surface-300">
                {{ project.estimated_end_date ? formatDate(project.estimated_end_date) : '—' }}
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
              <li v-if="environments.length === 0" class="px-3 py-2 text-sm text-surface-600">
                Nenhum ambiente cadastrado.
              </li>
            </ul>
          </SectionCard>

          <SectionCard title="Deploy mais recente">
            <div v-if="releases.length > 0">
              <p class="font-mono text-sm font-semibold text-surface-200">{{ releases[0].version }}</p>
              <p v-if="releases[0].released_at" class="mt-1 text-xs text-surface-500">
                publicado em {{ formatDate(releases[0].released_at) }} · {{ timeAgo(releases[0].released_at) }}
              </p>
              <div class="mt-3">
                <StatusBadge :status="releases[0].status" :label="releases[0].status_label" />
              </div>
            </div>
            <p v-else class="text-sm text-surface-600">Nenhuma release publicada.</p>
          </SectionCard>
        </div>
      </div>

      <!-- ENDPOINTS -->
      <div v-if="activeTab === 'endpoints'" class="card-surface">
        <div class="flex items-center justify-between border-b border-white/5 p-4">
          <h3 class="text-sm font-semibold text-surface-300">Endpoints da API</h3>
          <span class="text-xs text-surface-500">{{ endpoints.length }} no total</span>
        </div>
        <div v-if="endpoints.length > 0" class="divide-y divide-white/5">
          <button
            v-for="endpoint in endpoints"
            :key="endpoint.id"
            type="button"
            class="flex w-full items-center gap-4 px-5 py-4 text-left transition hover:bg-white/[0.02]"
            @click="goToEndpoint(endpoint)"
          >
            <MethodBadge :method="endpoint.method" />
            <div class="min-w-0 flex-1">
              <p class="font-mono text-[13px] font-medium text-surface-300">{{ endpoint.url }}</p>
              <p v-if="endpoint.name" class="mt-0.5 truncate text-xs text-surface-600">{{ endpoint.name }}</p>
            </div>
            <StatusBadge :status="endpoint.status" :label="endpoint.status_label" />
            <ArrowTopRightOnSquareIcon class="h-4 w-4 shrink-0 text-surface-600" />
          </button>
        </div>
        <EmptyState v-else title="Nenhum endpoint" description="Este projeto ainda não possui endpoints cadastrados." />
      </div>

      <!-- RELEASES -->
      <div v-if="activeTab === 'releases'" class="card-surface">
        <div class="border-b border-white/5 p-4">
          <h3 class="text-sm font-semibold text-surface-300">Histórico de releases</h3>
        </div>
        <ul v-if="releases.length > 0" class="divide-y divide-white/5">
          <li v-for="release in releases" :key="release.id" class="flex items-center gap-4 px-5 py-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/5 bg-white/[0.02]">
              <RocketLaunchIcon class="h-5 w-5 text-indigo-400" />
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <p class="font-mono text-sm font-semibold text-surface-200">{{ release.version }}</p>
                <StatusBadge :status="release.status" :label="release.status_label" />
              </div>
              <p v-if="release.released_at" class="mt-0.5 truncate text-xs text-surface-500">
                publicado em {{ formatDate(release.released_at) }}
              </p>
            </div>
            <span v-if="release.released_at" class="text-xs text-surface-600">{{ timeAgo(release.released_at) }}</span>
          </li>
        </ul>
        <EmptyState v-else title="Nenhuma release" description="Este projeto ainda não possui releases cadastradas." />
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
            <StatusBadge :status="env.status" :label="env.status_label" />
          </div>
          <div class="mt-4 space-y-2.5">
            <div class="flex justify-between">
              <span class="text-xs text-surface-600">URL</span>
              <span class="font-mono text-xs text-surface-400">{{ env.url ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-xs text-surface-600">Database</span>
              <span class="font-mono text-xs text-surface-400">{{ env.database ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-xs text-surface-600">Version</span>
              <span class="font-mono text-xs text-surface-400">{{ env.version ?? '—' }}</span>
            </div>
          </div>
        </div>
        <div v-if="environments.length === 0" class="col-span-full">
          <EmptyState title="Nenhum ambiente" description="Este projeto ainda não possui ambientes configurados." />
        </div>
      </div>

      <!-- MEMBERS -->
      <div v-if="activeTab === 'members'" class="card-surface">
        <div class="border-b border-white/5 p-4">
          <h3 class="text-sm font-semibold text-surface-300">Membros da equipe</h3>
        </div>
        <ul v-if="teamMembers.length > 0" class="divide-y divide-white/5">
          <li v-for="member in teamMembers" :key="member.id" class="flex items-center gap-4 px-5 py-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-xs font-semibold text-white">
              {{ (member.user?.name ?? '?').charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-medium text-surface-300">{{ member.user?.name ?? 'Membro' }}</p>
              <p class="truncate text-xs text-surface-500">{{ member.user?.email ?? '—' }}</p>
            </div>
            <StatusBadge :status="member.role" :label="member.role_label" />
          </li>
        </ul>
        <EmptyState v-else title="Nenhum membro" description="Este projeto ainda não possui membros na equipe." />
      </div>

      <!-- METRICS -->
      <div v-if="activeTab === 'metrics'" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <MetricCard label="Endpoints saudáveis" :value="`${healthyEndpoints}/${endpoints.length}`" hint="De todos os endpoints" :icon="ArrowPathRoundedSquareIcon" />
        <MetricCard label="Latência média" :value="`${averageResponse}ms`" hint="Calculada dos endpoints" :icon="BoltIcon" />
        <MetricCard label="Endpoints fora do ar" :value="downEndpoints" hint="Precisam de atenção" :icon="ServerStackIcon" />
        <MetricCard label="Total de membros" :value="teamMembers.length" hint="Membros da equipe" :icon="UsersIcon" />
      </div>
    </template>
  </div>
</template>
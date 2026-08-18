<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useDashboardStore } from '@/stores/dashboard'
import { useProjectStore } from '@/stores/projects'
import { useRouter } from 'vue-router'
import { DashboardService } from '@/services/DashboardService'
import type { ActivityItem } from '@/services/types'
import PageHeader from '@/components/PageHeader.vue'
import StatCard from '@/components/StatCard.vue'
import SectionCard from '@/components/SectionCard.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Loading from '@/components/Loading.vue'
import AreaChart from '@/components/charts/AreaChart.vue'
import StackedBarChart from '@/components/charts/StackedBarChart.vue'
import { timeAgo } from '@/utils/format'
import {
  FolderIcon,
  BoltIcon,
  RocketLaunchIcon,
  UsersIcon,
  ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline'

const dashboard = useDashboardStore()
const projectsStore = useProjectStore()
const router = useRouter()

const { metrics, loading } = storeToRefs(dashboard)

const activities = ref<ActivityItem[]>([])

onMounted(async () => {
  const [, activitiesResult] = await Promise.all([
    dashboard.load(),
    DashboardService.activities(),
    projectsStore.fetchProjects(),
  ])
  activities.value = activitiesResult
})
</script>

<template>
  <div>
    <PageHeader
      title="Dashboard"
      subtitle="Visão geral da operação e saúde das APIs em tempo real."
    />

    <div v-if="loading && !metrics" class="py-16">
      <Loading label="Carregando métricas..." />
    </div>

    <template v-else-if="metrics">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard
          title="Total Projects"
          :value="metrics.totalProjects"
          :icon="FolderIcon"
          accent="indigo"
          :trend="{ direction: 'up', text: '+2 este mês' }"
        />
        <StatCard
          title="APIs Online"
          :value="metrics.apisOnline"
          :icon="BoltIcon"
          accent="emerald"
          :trend="{ direction: 'up', text: `${metrics.uptime}% uptime` }"
        />
        <StatCard
          title="Active Releases"
          :value="metrics.activeReleases"
          :icon="RocketLaunchIcon"
          accent="sky"
          :trend="{ direction: 'up', text: '+4 este mês' }"
        />
        <StatCard
          title="Team Members"
          :value="metrics.teamMembers"
          :icon="UsersIcon"
          accent="amber"
          :trend="{ direction: 'up', text: '+1 este mês' }"
        />
      </div>

      <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <SectionCard
          title="Deploys por mês"
          subtitle="Total de deploys realizados nos últimos 12 meses"
          class="xl:col-span-2"
        >
          <AreaChart :series="metrics.deploySeries" :categories="metrics.categories" />
        </SectionCard>

        <SectionCard title="Alertas" subtitle="O que precisa de atenção">
          <ul class="space-y-3">
            <li
              v-for="alert in dashboard.alerts"
              :key="alert.id"
              class="flex items-start gap-3 rounded-lg border border-white/5 bg-white/[0.02] p-3"
            >
              <span
                class="mt-0.5 h-2 w-2 shrink-0 rounded-full"
                :class="{
                  'bg-rose-400': alert.severity === 'critical',
                  'bg-amber-400': alert.severity === 'warning',
                  'bg-sky-400': alert.severity === 'info',
                }"
              />
              <div class="min-w-0">
                <p class="text-sm font-medium text-surface-400">{{ alert.title }}</p>
                <p class="mt-0.5 line-clamp-2 text-xs text-surface-500">{{ alert.detail }}</p>
                <p class="mt-1 text-[11px] text-surface-600">{{ timeAgo(alert.timestamp) }}</p>
              </div>
            </li>
          </ul>
        </SectionCard>
      </div>

      <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <SectionCard
          title="Saúde das APIs"
          subtitle="Distribuição de sucesso e erros por mês"
          class="xl:col-span-2"
        >
          <StackedBarChart :series="metrics.apiSeries" :categories="metrics.categories" />
        </SectionCard>

        <SectionCard title="Atividades recentes" subtitle="Últimas ações da equipe">
          <ul class="space-y-4">
            <li v-for="activity in activities" :key="activity.id" class="flex items-start gap-3">
              <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400" />
              <div class="min-w-0">
                <p class="text-sm text-surface-400">
                  <span class="font-medium text-surface-300">{{ activity.actor }}</span>
                  {{ activity.action }}
                  <span class="font-medium text-surface-300">{{ activity.target }}</span>
                </p>
                <p class="mt-0.5 text-[11px] text-surface-600">{{ timeAgo(activity.timestamp) }}</p>
              </div>
            </li>
          </ul>
        </SectionCard>
      </div>

      <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <SectionCard
          title="Projetos recentes"
          subtitle="Projetos mais recentes da plataforma"
          class="xl:col-span-2"
        >
          <div class="space-y-1">
            <button
              v-for="project in projectsStore.projects.slice(0, 5)"
              :key="project.id"
              type="button"
              class="flex w-full items-center justify-between gap-4 rounded-lg px-3 py-2.5 transition hover:bg-white/[0.03]"
              @click="router.push(`/projects/${project.id}`)"
            >
              <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-white/5 bg-white/[0.02] text-xs font-semibold text-surface-400">
                  {{ project.name.charAt(0) }}
                </div>
                <div class="min-w-0 text-left">
                  <p class="truncate text-sm font-medium text-surface-300">{{ project.name }}</p>
                  <p class="truncate text-xs text-surface-500">
                    {{ project.framework }} · {{ project.language }}
                  </p>
                </div>
              </div>
              <div class="flex shrink-0 items-center gap-3">
                <StatusBadge :status="project.status" />
                <ArrowTopRightOnSquareIcon class="h-4 w-4 text-surface-600" />
              </div>
            </button>
          </div>
        </SectionCard>

        <SectionCard title="Últimas releases" subtitle="Deploys mais recentes">
          <ul class="space-y-3">
            <li
              v-for="release in [
                { version: 'v3.4.0', project: 'Portal de Pagamentos', status: 'released', when: '2 dias atrás' },
                { version: 'v1.4.0', project: 'Chat IA Suporte', status: 'released', when: '1 dia atrás' },
                { version: 'v2.1.0', project: 'App Delivery', status: 'released', when: '3 dias atrás' },
              ]"
              :key="release.version + release.project"
              class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] p-3"
            >
              <div>
                <p class="text-sm font-mono font-medium text-surface-300">{{ release.version }}</p>
                <p class="text-xs text-surface-500">{{ release.project }}</p>
              </div>
              <div class="text-right">
                <StatusBadge :status="release.status" />
                <p class="mt-1 text-[11px] text-surface-600">{{ release.when }}</p>
              </div>
            </li>
          </ul>
        </SectionCard>
      </div>
    </template>
  </div>
</template>
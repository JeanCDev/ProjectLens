import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { defineStore } from 'pinia'
import { ref } from 'vue'

vi.mock('@/stores/projects', () => ({
  useProjectStore: () => ({
    projects: [{ id: 1, name: 'P1', primary_language: 'PHP', status: 'active' }],
    fetchProjects: vi.fn(),
  }),
}))

vi.mock('@/services/DashboardService', () => ({
  DashboardService: {
    activities: vi.fn().mockResolvedValue([
      { id: 1, actor: 'A', action: 'fez', target: 'X', timestamp: new Date().toISOString() },
    ]),
    alerts: vi.fn().mockResolvedValue([]),
  },
}))

vi.mock('vue3-apexcharts', () => ({
  default: {
    name: 'VueApexCharts',
    props: ['type', 'height', 'options', 'series'],
    template: '<div class="apex-mock"></div>',
  },
}))

// Store real do dashboard para storeToRefs funcionar
const useDashboardStore = defineStore('dashboard-test', () => {
  const metrics = ref({
    totalProjects: 10, apisOnline: 8, activeReleases: 4, teamMembers: 12, uptime: 99.9,
    deploySeries: [{ name: 'x', data: [1, 2] }], apiSeries: [{ name: 'y', data: [1, 2] }],
    categories: ['jan', 'fev'],
  })
  const alerts = ref([{ id: 1, severity: 'critical', title: 'A', detail: 'B', timestamp: new Date().toISOString() }])
  const loading = ref(false)
  const load = vi.fn()
  return { metrics, alerts, loading, load }
})

vi.mock('@/stores/dashboard', () => ({
  useDashboardStore: () => useDashboardStore(),
}))

describe('DashboardView', () => {
  beforeEach(() => setActivePinia(createPinia()))
  it('renderiza metricas, alertas e atividades', async () => {
    const { default: DashboardView } = await import('@/pages/DashboardView.vue')
    const w = mount(DashboardView)
    await new Promise((r) => setTimeout(r, 60))
    expect(w.text()).toContain('Total Projects')
    expect(w.text()).toContain('A')
  })
})

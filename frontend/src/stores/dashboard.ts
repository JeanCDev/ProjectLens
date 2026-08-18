import { defineStore } from 'pinia'
import { ref } from 'vue'
import { DashboardService } from '@/services/DashboardService'
import type { AlertItem, DashboardMetrics } from '@/services/types'

export const useDashboardStore = defineStore('dashboard', () => {
  const metrics = ref<DashboardMetrics | null>(null)
  const alerts = ref<AlertItem[]>([])
  const loading = ref(false)

  async function load() {
    loading.value = true
    try {
      metrics.value = await DashboardService.metrics()
      alerts.value = await DashboardService.alerts()
    } finally {
      loading.value = false
    }
  }

  return { metrics, alerts, loading, load }
})
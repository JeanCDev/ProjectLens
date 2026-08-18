import { delay, randomIn } from './helpers'
import { mockActivities, mockAlerts, mockMetrics, mockTeamMembers } from './mock'
import type { ActivityItem, AlertItem, DashboardMetrics, TeamMember } from './types'

export const DashboardService = {
  async metrics(): Promise<DashboardMetrics> {
    await delay(randomIn(350, 550))
    return {
      ...mockMetrics,
      deploySeries: mockMetrics.deploySeries.map((s) => ({ ...s, data: [...s.data] })),
      apiSeries: mockMetrics.apiSeries.map((s) => ({ ...s, data: [...s.data] })),
      categories: [...mockMetrics.categories],
    }
  },

  async activities(): Promise<ActivityItem[]> {
    await delay(randomIn(250, 400))
    return [...mockActivities]
  },

  async alerts(): Promise<AlertItem[]> {
    await delay(randomIn(250, 400))
    return [...mockAlerts]
  },
}

export const TeamMemberService = {
  async list(projectId?: number): Promise<TeamMember[]> {
    await delay(randomIn(300, 500))
    const items = projectId ? mockTeamMembers.filter((m) => m.project_id === projectId) : [...mockTeamMembers]
    return items.map((member) => ({ ...member }))
  },
}
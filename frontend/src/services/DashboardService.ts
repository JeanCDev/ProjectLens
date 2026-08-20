import type { ActivityItem, AlertItem, DashboardMetrics } from './types'

const mockMetrics: DashboardMetrics = {
  totalProjects: 6,
  apisOnline: 11,
  activeReleases: 6,
  teamMembers: 6,
  uptime: 99.96,
  deploySeries: [{ name: 'Deploys', data: [3, 5, 2, 8, 6, 11, 7, 14, 9, 12, 8, 16] }],
  apiSeries: [
    { name: 'Sucesso', data: [98, 99, 97, 99, 100, 99, 98, 99, 99, 100, 99, 99] },
    { name: 'Erros', data: [2, 1, 3, 1, 0, 1, 2, 1, 1, 0, 1, 1] },
  ],
  categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
}

const mockActivities: ActivityItem[] = [
  { id: 1, actor: 'Ana Souza', action: 'deployou', target: 'Portal de Pagamentos v3.4.0', timestamp: '2026-08-16T14:20:00Z' },
  { id: 2, actor: 'Eduarda Pires', action: 'deployou', target: 'Chat IA Suporte v1.4.0', timestamp: '2026-08-17T07:50:00Z' },
  { id: 3, actor: 'Bruno Lima', action: 'marcou o endpoint como degradado', target: 'POST /api/v2/wallet/transfer', timestamp: '2026-08-18T08:50:00Z' },
  { id: 4, actor: 'Carla Mendes', action: 'adicionou o membro', target: 'Diego Rocha no CRM Interno', timestamp: '2026-08-18T07:30:00Z' },
  { id: 5, actor: 'Fábio Alves', action: 'arquivou o projeto', target: 'Analytics SaaS', timestamp: '2026-08-17T20:15:00Z' },
  { id: 6, actor: 'Diego Rocha', action: 'criou a release planejada', target: 'Biometria Facial v0.9.0', timestamp: '2026-08-17T16:40:00Z' },
]

const mockAlerts: AlertItem[] = [
  { id: 1, severity: 'critical', title: 'Endpoint fora do ar', detail: 'DELETE /api/v1/customers/{id} está retornando 500 há 20 minutos.', timestamp: '2026-08-18T08:40:00Z' },
  { id: 2, severity: 'warning', title: 'Latência acima do limite', detail: 'POST /api/v2/wallet/transfer ultrapassou 900ms (limite 500ms).', timestamp: '2026-08-18T08:50:00Z' },
  { id: 3, severity: 'info', title: 'Certificado SSL próximo da validade', detail: 'O certificado de api.delivery.acme.com expira em 12 dias.', timestamp: '2026-08-18T06:00:00Z' },
]

export const DashboardService = {
  async metrics(): Promise<DashboardMetrics> {
    return {
      ...mockMetrics,
      deploySeries: mockMetrics.deploySeries.map((s) => ({ ...s, data: [...s.data] })),
      apiSeries: mockMetrics.apiSeries.map((s) => ({ ...s, data: [...s.data] })),
      categories: [...mockMetrics.categories],
    }
  },

  async activities(): Promise<ActivityItem[]> {
    return [...mockActivities]
  },

  async alerts(): Promise<AlertItem[]> {
    return [...mockAlerts]
  },
}
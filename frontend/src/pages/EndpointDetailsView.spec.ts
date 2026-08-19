import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'

const getEp = vi.fn()
const reqDetails = vi.fn()

vi.mock('vue-router', () => ({
  useRoute: () => ({ params: { projectId: '1', id: '2' } }),
  useRouter: () => ({ push: vi.fn() }),
}))
vi.mock('@/services/EndpointService', () => ({
  EndpointService: {
    get: (...a: unknown[]) => getEp(...a),
    requestDetails: (...a: unknown[]) => reqDetails(...a),
  },
}))

const endpoint = {
  id: 2, project_id: 1, environment_id: 1, method: 'GET', url: '/api/x',
  name: 'List', description: '', response_time_ms: 12, status: 'active', status_label: 'Ativo',
  last_checked_at: '2026-02-01T00:00:00Z',
}

describe('EndpointDetailsView', () => {
  beforeEach(() => {
    getEp.mockReset(); reqDetails.mockReset()
    getEp.mockResolvedValue(endpoint)
    reqDetails.mockReturnValue({
      method: 'GET', url: '/api/x', request: { headers: { Authorization: 'Bearer x' }, body: null },
      response: { status: 200, statusText: 'OK', headers: {}, body: {} },
      duration_ms: 12, checked_at: '2026-02-01T00:00:00Z',
    })
  })

  async function setup() {
    const { default: EndpointDetailsView } = await import('@/pages/EndpointDetailsView.vue')
    const w = mount(EndpointDetailsView)
    await new Promise((r) => setTimeout(r, 20))
    return w
  }

  it('carrega e mostra dados do endpoint', async () => {
    const w = await setup()
    expect(w.text()).toContain('/api/x')
    expect(w.text()).toContain('12ms')
  })

  it('botao verificar agora recarrega', async () => {
    const w = await setup()
    const btn = w.findAll('button').find((b) => b.text().includes('Verificar agora'))!
    await btn.trigger('click')
    await new Promise((r) => setTimeout(r, 20))
    expect(getEp).toHaveBeenCalledTimes(2)
  })

  it('trata endpoint sem ultima verificacao', async () => {
    getEp.mockResolvedValueOnce({ ...endpoint, last_checked_at: null })
    const w = await setup()
    expect(w.text()).toContain('—')
  })
})

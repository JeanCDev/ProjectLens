import { delay, randomIn } from './helpers'
import { mockEndpoints } from './mock'
import type { ApiEndpoint } from './types'

export const EndpointService = {
  async list(projectId?: number): Promise<ApiEndpoint[]> {
    await delay()
    const items = projectId ? mockEndpoints.filter((e) => e.project_id === projectId) : [...mockEndpoints]
    return items.map((endpoint) => ({ ...endpoint }))
  },

  async get(id: number): Promise<ApiEndpoint> {
    await delay(randomIn(250, 450))
    const endpoint = mockEndpoints.find((e) => e.id === id)
    if (!endpoint) throw new Error('Endpoint não encontrado')
    return { ...endpoint }
  },

  async requestDetails(id: number) {
    await delay(randomIn(600, 900))
    const endpoint = mockEndpoints.find((e) => e.id === id)
    if (!endpoint) throw new Error('Endpoint não encontrado')
    const status = endpoint.status === 'active' ? 200 : endpoint.status === 'degraded' ? 429 : 500
    return {
      method: endpoint.method,
      url: `https://api.acme.com${endpoint.path}`,
      request: {
        headers: {
          Authorization: 'Bearer sk_live_••••••••',
          'Content-Type': 'application/json',
          'Idempotency-Key': '9f8e7d6c-5b4a-3c2d-1e0f-0a1b2c3d4e5f',
        },
        body: endpoint.method === 'GET' ? null : { example: 'payload', nested: { key: 'value' } },
      },
      response: {
        status,
        statusText: status === 200 ? 'OK' : status === 429 ? 'Too Many Requests' : 'Internal Server Error',
        headers: {
          'Content-Type': 'application/json',
          'X-Request-Id': 'req_1234abcd',
          'X-RateLimit-Remaining': '997',
        },
        body: {
          data: { id: 'trx_abc123', status: status === 200 ? 'approved' : 'failed' },
          meta: { took_ms: endpoint.response_time_ms },
        },
      },
      duration_ms: endpoint.response_time_ms,
      checked_at: endpoint.last_checked_at,
    }
  },
}
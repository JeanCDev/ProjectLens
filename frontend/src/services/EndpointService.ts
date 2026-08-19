import http from './http'
import type { ApiEndpoint, PaginatedData } from './types'

export interface EndpointRequestDetails {
  method: string
  url: string
  request: {
    headers: Record<string, string>
    body: Record<string, unknown> | null
  }
  response: {
    status: number
    statusText: string
    headers: Record<string, string>
    body: Record<string, unknown>
  }
  duration_ms: number
  checked_at: string | null
}

export const EndpointService = {
  async list(projectId: number): Promise<ApiEndpoint[]> {
    const { data } = await http.get<PaginatedData<ApiEndpoint>>(`/projects/${projectId}/endpoints`)
    return data.data
  },

  async get(projectId: number, id: number): Promise<ApiEndpoint> {
    const { data } = await http.get<{ data: ApiEndpoint }>(`/projects/${projectId}/endpoints/${id}`)
    return data.data
  },

  requestDetails(endpoint: ApiEndpoint): EndpointRequestDetails {
    const status = endpoint.status === 'healthy' ? 200 : endpoint.status === 'degraded' ? 429 : 500
    return {
      method: endpoint.method,
      url: endpoint.url,
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
          meta: { took_ms: endpoint.response_time_ms ?? 0 },
        },
      },
      duration_ms: endpoint.response_time_ms ?? 0,
      checked_at: endpoint.last_checked_at ?? null,
    }
  },
}
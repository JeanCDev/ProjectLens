import http from './http'
import type { PaginatedData, Release } from './types'

export const ReleaseService = {
  async list(projectId: number): Promise<Release[]> {
    const { data } = await http.get<PaginatedData<Release>>(`/projects/${projectId}/releases`)
    return data.data
  },

  async get(projectId: number, id: number): Promise<Release> {
    const { data } = await http.get<{ data: Release }>(`/projects/${projectId}/releases/${id}`)
    return data.data
  },
}
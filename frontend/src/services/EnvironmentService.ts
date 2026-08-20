import http from './http'
import type { Environment, PaginatedData } from './types'

export const EnvironmentService = {
  async list(projectId: number): Promise<Environment[]> {
    const { data } = await http.get<PaginatedData<Environment>>(`/projects/${projectId}/environments`)
    return data.data
  },

  async get(projectId: number, id: number): Promise<Environment> {
    const { data } = await http.get<{ data: Environment }>(`/projects/${projectId}/environments/${id}`)
    return data.data
  },
}
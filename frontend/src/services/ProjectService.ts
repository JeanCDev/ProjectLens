import http from './http'
import type { PaginatedData, Project } from './types'

export interface ProjectFilters {
  search?: string
  status?: string
  language?: string
  framework?: string
}

export const ProjectService = {
  async list(filters: ProjectFilters = {}): Promise<{ items: Project[]; total: number }> {
    const params = {
      search: filters.search || undefined,
      status: filters.status || undefined,
      language: filters.language || undefined,
    }
    const { data } = await http.get<PaginatedData<Project>>('/projects', { params })
    return { items: data.data, total: data.meta?.total ?? data.data.length }
  },

  async get(id: number): Promise<Project> {
    const { data } = await http.get<{ data: Project }>(`/projects/${id}`)
    return data.data
  },
}
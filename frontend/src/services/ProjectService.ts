import { delay, randomIn } from './helpers'
import { mockProjects } from './mock'
import type { Project } from './types'

export interface ProjectFilters {
  search?: string
  status?: string
  framework?: string
}

export const ProjectService = {
  async list(filters: ProjectFilters = {}): Promise<{ items: Project[]; total: number }> {
    await delay(randomIn(300, 500))
    let items = [...mockProjects]

    if (filters.search) {
      const q = filters.search.toLowerCase()
      items = items.filter(
        (p) =>
          p.name.toLowerCase().includes(q) ||
          p.language.toLowerCase().includes(q) ||
          p.framework.toLowerCase().includes(q) ||
          p.owner.toLowerCase().includes(q),
      )
    }
    if (filters.status) items = items.filter((p) => p.status === filters.status)
    if (filters.framework) items = items.filter((p) => p.framework === filters.framework)

    return { items, total: items.length }
  },

  async get(id: number): Promise<Project> {
    await delay(randomIn(250, 400))
    const project = mockProjects.find((p) => p.id === id)
    if (!project) throw new Error('Projeto não encontrado')
    return { ...project }
  },
}
import { delay, randomIn } from './helpers'
import { mockEnvironments } from './mock'
import type { Environment } from './types'

export const EnvironmentService = {
  async list(projectId?: number): Promise<Environment[]> {
    await delay(randomIn(300, 500))
    const items = projectId ? mockEnvironments.filter((e) => e.project_id === projectId) : [...mockEnvironments]
    return items.map((env) => ({ ...env }))
  },

  async get(id: number): Promise<Environment> {
    await delay(randomIn(250, 400))
    const env = mockEnvironments.find((e) => e.id === id)
    if (!env) throw new Error('Ambiente não encontrado')
    return { ...env }
  },
}
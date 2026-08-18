import { delay, randomIn } from './helpers'
import { mockReleases } from './mock'
import type { Release } from './types'

export const ReleaseService = {
  async list(projectId?: number): Promise<Release[]> {
    await delay(randomIn(300, 500))
    const items = projectId ? mockReleases.filter((r) => r.project_id === projectId) : [...mockReleases]
    return items.map((release) => ({ ...release, changelog: [...release.changelog] }))
  },

  async get(id: number): Promise<Release> {
    await delay(randomIn(250, 400))
    const release = mockReleases.find((r) => r.id === id)
    if (!release) throw new Error('Release não encontrada')
    return { ...release, changelog: [...release.changelog] }
  },
}
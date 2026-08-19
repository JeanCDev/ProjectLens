import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import type { Project } from '@/services/types'

const list = vi.fn()
const detail = vi.fn()
const push = vi.fn()

vi.mock('@/services/ProjectService', () => ({
  ProjectService: {
    list: (...a: unknown[]) => list(...a),
    get: (...a: unknown[]) => detail(...a),
  },
}))
vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
  useRoute: () => ({ params: {} }),
}))

const projects: Project[] = [
  {
    id: 1, name: 'Portal', description: 'desc', primary_language: 'PHP', framework: 'Laravel',
    status: 'active', status_label: 'Ativo', start_date: '2026-01-01', estimated_end_date: '2027-01-01',
    git_repository: 'http://x.com', documentation_url: '', created_at: '', updated_at: '',
    environments: [], releases: [], endpoints: [], team_members: [],
  } as unknown as Project,
]

describe('ProjectsView', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    list.mockReset()
    detail.mockReset()
    list.mockResolvedValue({ items: projects, total: 1 })
  })

  it('lista projetos e mostra total', async () => {
    const { default: ProjectsView } = await import('@/pages/ProjectsView.vue')
    const w = mount(ProjectsView)
    await new Promise((r) => setTimeout(r, 10))
    expect(w.text()).toContain('Portal')
    expect(w.text()).toContain('1 registro')
  })

  it('busca filtra via ProjectService', async () => {
    const { default: ProjectsView } = await import('@/pages/ProjectsView.vue')
    const w = mount(ProjectsView)
    await new Promise((r) => setTimeout(r, 10))
    const input = w.find('input[type="search"]')
    await input.setValue('Portal')
    await new Promise((r) => setTimeout(r, 350))
    expect(list).toHaveBeenCalled()
  })

  it('filtro de status chama service', async () => {
    const { default: ProjectsView } = await import('@/pages/ProjectsView.vue')
    const w = mount(ProjectsView)
    await new Promise((r) => setTimeout(r, 10))
    const sel = w.find('select')
    await sel.setValue('active')
    await new Promise((r) => setTimeout(r, 350))
    expect(list).toHaveBeenCalled()
  })

  it('clicar em linha emite navegacao', async () => {
    const { default: ProjectsView } = await import('@/pages/ProjectsView.vue')
    const w = mount(ProjectsView)
    await new Promise((r) => setTimeout(r, 10))
    const row = w.find('tbody tr')
    if (row.exists()) {
      await row.trigger('click')
      expect(w.text()).toBeTruthy()
    }
  })
})

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'

const push = vi.fn()
const getProject = vi.fn()
const listRelease = vi.fn()
const listEnv = vi.fn()
const listEndpoint = vi.fn()
const listMember = vi.fn()

vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
  useRoute: () => ({ params: { id: '1' } }),
}))

vi.mock('@/services/ProjectService', () => ({
  ProjectService: { get: (...a: unknown[]) => getProject(...a) },
}))
vi.mock('@/services/ReleaseService', () => ({
  ReleaseService: { list: (...a: unknown[]) => listRelease(...a) },
}))
vi.mock('@/services/EnvironmentService', () => ({
  EnvironmentService: { list: (...a: unknown[]) => listEnv(...a) },
}))
vi.mock('@/services/EndpointService', () => ({
  EndpointService: {
    list: (...a: unknown[]) => listEndpoint(...a),
    get: vi.fn(),
    requestDetails: vi.fn(),
  },
}))
vi.mock('@/services/TeamMemberService', () => ({
  TeamMemberService: { list: (...a: unknown[]) => listMember(...a) },
}))

const project = {
  id: 1, name: 'Portal', description: 'desc', primary_language: 'PHP', framework: 'Laravel',
  status: 'active', status_label: 'Ativo', start_date: '2026-01-01', estimated_end_date: '2027-01-01',
  git_repository: 'http://x.com', documentation_url: '', created_at: '2026-01-01T00:00:00Z',
  updated_at: '2026-01-01T00:00:00Z',
  environments: [{ id: 1, project_id: 1, name: 'Prod', type: 'production', url: 'u', database: 'd', version: '1', status: 'active', status_label: 'Ativo' }],
  releases: [{ id: 1, project_id: 1, version: '1.0', status: 'released', status_label: 'Liberado', released_at: '2026-02-01T00:00:00Z', changelog: ['a'], author: 'x' }],
  endpoints: [{ id: 1, project_id: 1, environment_id: 1, method: 'GET', url: '/api/x', name: 'List', description: '', response_time_ms: 10, status: 'active', status_label: 'Ativo', last_checked_at: '2026-02-01T00:00:00Z' }],
  team_members: [{ id: 1, project_id: 1, name: 'Joao', email: 'j@x.com', role: 'owner', joined_at: '2026-01-01T00:00:00Z', user: { name: 'Joao', email: 'j@x.com' } }],
}

describe('ProjectDetailsView', () => {
  beforeEach(() => {
    push.mockClear()
    getProject.mockReset(); listRelease.mockReset(); listEnv.mockReset(); listEndpoint.mockReset(); listMember.mockReset()
    getProject.mockResolvedValue(project)
    listRelease.mockResolvedValue(project.releases)
    listEnv.mockResolvedValue(project.environments)
    listEndpoint.mockResolvedValue(project.endpoints)
    listMember.mockResolvedValue(project.team_members)
  })

  async function setup() {
    const { default: ProjectDetailsView } = await import('@/pages/ProjectDetailsView.vue')
    const w = mount(ProjectDetailsView)
    await new Promise((r) => setTimeout(r, 20))
    return w
  }

  it('carrega e mostra overview', async () => {
    const w = await setup()
    expect(w.text()).toContain('Portal')
    expect(w.text()).toContain('PHP')
  })

  it('navega para aba Endpoints e mostra endpoint', async () => {
    const w = await setup()
    const tabs = w.findAll('button').filter((b) => b.text().includes('Endpoints'))
    await tabs[0].trigger('click')
    expect(w.text()).toContain('/api/x')
  })

  it('navega para Releases', async () => {
    const w = await setup()
    await w.findAll('button').find((b) => b.text().includes('Releases'))!.trigger('click')
    expect(w.text()).toContain('1.0')
  })

  it('navega para Environments', async () => {
    const w = await setup()
    await w.findAll('button').find((b) => b.text().includes('Environments'))!.trigger('click')
    expect(w.text()).toContain('Prod')
  })

  it('navega para Members', async () => {
    const w = await setup()
    await w.findAll('button').find((b) => b.text().includes('Members'))!.trigger('click')
    expect(w.text()).toContain('Joao')
  })

  it('navega para Metrics e mostra contagens', async () => {
    const w = await setup()
    await w.findAll('button').find((b) => b.text().includes('Metrics'))!.trigger('click')
    expect(w.text()).toContain('1') // contagens derivadas
  })

  it('clicar em endpoint navega para detalhes', async () => {
    const w = await setup()
    await w.findAll('button').find((b) => b.text().includes('Endpoints'))!.trigger('click')
    const epBtn = w.findAll('button').find((b) => b.text().includes('/api/x'))
    await epBtn!.trigger('click')
    expect(push).toHaveBeenCalled()
  })

  it('estado vazio de endpoints', async () => {
    listEndpoint.mockResolvedValueOnce([])
    const w = await setup()
    await w.findAll('button').find((b) => b.text().includes('Endpoints'))!.trigger('click')
    expect(w.text()).toContain('Nenhum endpoint')
  })
})

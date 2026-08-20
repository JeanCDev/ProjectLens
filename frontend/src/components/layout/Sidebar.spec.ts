import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import Sidebar from './Sidebar.vue'
import Topbar from './Topbar.vue'

const routerPush = vi.fn()
vi.mock('vue-router', async () => {
  const actual = await vi.importActual('vue-router')
  return {
    ...actual,
    useRoute: () => ({ path: '/', meta: { title: 'Dashboard' } }),
    useRouter: () => ({ push: routerPush }),
  }
})

vi.mock('@/stores/ui', () => ({
  useUiStore: () => ({
    sidebarCollapsed: false,
    sidebarMobileOpen: false,
    toggleSidebar: vi.fn(),
    closeMobileSidebar: vi.fn(),
  }),
}))

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({
    isAuthenticated: true,
    user: { name: 'Jean Gomes', email: 'jean@x.com' },
    logout: vi.fn(),
  }),
}))

describe('Sidebar', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    routerPush.mockClear()
  })
  it('renderiza navegacao e usuario', () => {
    const w = mount(Sidebar)
    expect(w.text()).toContain('Dashboard')
    expect(w.text()).toContain('Projects')
    expect(w.text()).toContain('Jean Gomes')
  })
  it('mostra inicial do usuario', () => {
    const w = mount(Sidebar)
    expect(w.find('.rounded-full').text()).toBe('J')
  })
})

describe('Topbar', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    routerPush.mockClear()
  })
  it('mostra titulo da pagina e botao Sair quando autenticado', () => {
    const w = mount(Topbar)
    expect(w.text()).toContain('Dashboard')
    expect(w.text()).toContain('Sair')
  })
  it('logout chama router.push /login', async () => {
    const w = mount(Topbar)
    const sair = w.findAll('button').find((b) => b.text().includes('Sair'))!
    await sair.trigger('click')
    expect(routerPush).toHaveBeenCalledWith('/login')
  })
})

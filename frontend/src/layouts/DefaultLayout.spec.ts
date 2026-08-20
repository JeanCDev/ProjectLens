import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import DefaultLayout from '@/layouts/DefaultLayout.vue'

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

vi.mock('primevue/toast', () => ({ default: { name: 'Toast', template: '<div></div>' } }))
vi.mock('primevue/confirmdialog', () => ({ default: { name: 'ConfirmDialog', template: '<div></div>' } }))

describe('DefaultLayout', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    routerPush.mockClear()
  })
  it('renderiza Sidebar, Topbar e main', () => {
    const w = mount(DefaultLayout)
    expect(w.findComponent({ name: 'Sidebar' }).exists()).toBe(true)
    expect(w.findComponent({ name: 'Topbar' }).exists()).toBe(true)
    expect(w.find('main').exists()).toBe(true)
  })
  it('registra listener de resize no mount', () => {
    const addSpy = vi.spyOn(window, 'addEventListener')
    mount(DefaultLayout)
    expect(addSpy).toHaveBeenCalledWith('resize', expect.any(Function))
    addSpy.mockRestore()
  })
})

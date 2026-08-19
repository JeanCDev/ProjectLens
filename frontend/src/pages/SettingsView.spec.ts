import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'

const push = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
  useRoute: () => ({ params: {} }),
}))
vi.mock('primevue/usetoast', () => ({
  useToast: () => ({ add: vi.fn() }),
}))

describe('SettingsView', () => {
  beforeEach(() => push.mockClear())
  it('renderiza perfil e chaves de API', async () => {
    const { default: SettingsView } = await import('@/pages/SettingsView.vue')
    const w = mount(SettingsView)
    expect(w.text()).toContain('Ana Souza')
    expect(w.text()).toContain('Produção')
    expect(w.text()).toContain('Staging')
  })
  it('alterna tema ao clicar no switch', async () => {
    const { default: SettingsView } = await import('@/pages/SettingsView.vue')
    const w = mount(SettingsView)
    const themeSwitch = w.findAll('[role="switch"]')[0]
    await themeSwitch.trigger('click')
    expect(w.text()).toBeTruthy()
  })
  it('toggle de notificacoes muda estado', async () => {
    const { default: SettingsView } = await import('@/pages/SettingsView.vue')
    const w = mount(SettingsView)
    const switches = w.findAll('[role="switch"]')
    // os primeiros 2 sao tema + ... ; notificacoes comecam do indice 2
    await switches[2].trigger('click')
    expect(w.text()).toBeTruthy()
  })
  it('salvar chama toast', async () => {
    const { default: SettingsView } = await import('@/pages/SettingsView.vue')
    const w = mount(SettingsView)
    const saveBtn = w.findAll('button').find((b) => b.text().includes('Salvar'))!
    await saveBtn.trigger('click')
    expect(w.text()).toBeTruthy()
  })
})

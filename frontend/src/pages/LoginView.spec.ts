import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'

const push = vi.fn()
const login = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
  useRoute: () => ({ params: {} }),
}))
vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ login, isAuthenticated: false, user: null }),
}))

describe('LoginView', () => {
  beforeEach(() => {
    push.mockClear()
    login.mockReset()
  })

  it('valida campos obrigatorios', async () => {
    const { default: LoginView } = await import('@/pages/LoginView.vue')
    const w = mount(LoginView)
    await w.find('form').trigger('submit')
    expect(w.text()).toContain('Informe seu e-mail')
    expect(w.text()).toContain('Informe sua senha')
    expect(login).not.toHaveBeenCalled()
  })

  it('valida e-mail invalido', async () => {
    const { default: LoginView } = await import('@/pages/LoginView.vue')
    const w = mount(LoginView)
    await w.find('#login-email').setValue('naoeemail')
    await w.find('#login-password').setValue('senha123')
    await w.find('form').trigger('submit')
    expect(w.text()).toContain('e-mail válido')
  })

  it('login bem sucedido redireciona', async () => {
    login.mockResolvedValueOnce({ token: 'x', user: { id: 1, name: 'J', email: 'a@b.c' } })
    const { default: LoginView } = await import('@/pages/LoginView.vue')
    const w = mount(LoginView)
    await w.find('#login-email').setValue('a@b.com')
    await w.find('#login-password').setValue('senha123')
    await w.find('form').trigger('submit')
    await new Promise((r) => setTimeout(r, 10))
    expect(login).toHaveBeenCalled()
    expect(push).toHaveBeenCalledWith('/')
  })

  it('erro 422 mostra mensagens de validacao', async () => {
    login.mockRejectedValueOnce({
      response: { status: 422, data: { errors: { email: ['Credenciais inválidas.'] } } },
    })
    const { default: LoginView } = await import('@/pages/LoginView.vue')
    const w = mount(LoginView)
    await w.find('#login-email').setValue('a@b.com')
    await w.find('#login-password').setValue('senha123')
    await w.find('form').trigger('submit')
    await new Promise((r) => setTimeout(r, 10))
    expect(w.text()).toContain('Credenciais inválidas')
  })

  it('outros erros mostram geral', async () => {
    login.mockRejectedValueOnce({ response: { status: 500 } })
    const { default: LoginView } = await import('@/pages/LoginView.vue')
    const w = mount(LoginView)
    await w.find('#login-email').setValue('a@b.com')
    await w.find('#login-password').setValue('senha123')
    await w.find('form').trigger('submit')
    await new Promise((r) => setTimeout(r, 10))
    expect(w.text()).toContain('Não foi possível entrar')
  })
})

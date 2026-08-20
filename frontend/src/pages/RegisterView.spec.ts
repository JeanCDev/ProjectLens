import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'

const push = vi.fn()
const register = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
  useRoute: () => ({ params: {} }),
}))
vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ register, isAuthenticated: false, user: null }),
}))

async function fill(w: any, name: string, email: string, pw: string, pw2: string) {
  const inputs = w.findAll('input')
  // ordem: name, email, password, password_confirmation
  await inputs[0].setValue(name)
  await inputs[1].setValue(email)
  await inputs[2].setValue(pw)
  await inputs[3].setValue(pw2)
}

describe('RegisterView', () => {
  beforeEach(() => {
    push.mockClear()
    register.mockReset()
  })

  it('valida nome obrigatorio', async () => {
    const { default: RegisterView } = await import('@/pages/RegisterView.vue')
    const w = mount(RegisterView)
    await fill(w, '', 'a@b.com', 'senha123', 'senha123')
    await w.find('form').trigger('submit')
    expect(w.text()).toContain('Informe seu nome')
  })

  it('valida e-mail invalido', async () => {
    const { default: RegisterView } = await import('@/pages/RegisterView.vue')
    const w = mount(RegisterView)
    await fill(w, 'Jean', 'x', 'senha123', 'senha123')
    await w.find('form').trigger('submit')
    expect(w.text()).toContain('e-mail válido')
  })

  it('valida senha curta', async () => {
    const { default: RegisterView } = await import('@/pages/RegisterView.vue')
    const w = mount(RegisterView)
    await fill(w, 'Jean', 'a@b.com', '123', '123')
    await w.find('form').trigger('submit')
    expect(w.text()).toContain('pelo menos 8 caracteres')
  })

  it('valida confirmacao de senha', async () => {
    const { default: RegisterView } = await import('@/pages/RegisterView.vue')
    const w = mount(RegisterView)
    await fill(w, 'Jean', 'a@b.com', 'senha123', 'diferente')
    await w.find('form').trigger('submit')
    expect(w.text()).toContain('não conferem')
  })

  it('registro bem sucedido redireciona', async () => {
    register.mockResolvedValueOnce({ token: 'x', user: { id: 1, name: 'J', email: 'a@b.c' } })
    const { default: RegisterView } = await import('@/pages/RegisterView.vue')
    const w = mount(RegisterView)
    await fill(w, 'Jean', 'a@b.com', 'senha123', 'senha123')
    await w.find('form').trigger('submit')
    await new Promise((r) => setTimeout(r, 10))
    expect(register).toHaveBeenCalled()
    expect(push).toHaveBeenCalledWith('/')
  })

  it('erro 422 mapeia campos', async () => {
    register.mockRejectedValueOnce({
      response: { status: 422, data: { errors: { email: ['Já cadastrado.'] } } },
    })
    const { default: RegisterView } = await import('@/pages/RegisterView.vue')
    const w = mount(RegisterView)
    await fill(w, 'Jean', 'a@b.com', 'senha123', 'senha123')
    await w.find('form').trigger('submit')
    await new Promise((r) => setTimeout(r, 10))
    expect(w.text()).toContain('Já cadastrado')
  })

  it('outros erros mostram geral', async () => {
    register.mockRejectedValueOnce({ response: { status: 500 } })
    const { default: RegisterView } = await import('@/pages/RegisterView.vue')
    const w = mount(RegisterView)
    await fill(w, 'Jean', 'a@b.com', 'senha123', 'senha123')
    await w.find('form').trigger('submit')
    await new Promise((r) => setTimeout(r, 10))
    expect(w.text()).toContain('Não foi possível concluir')
  })
})

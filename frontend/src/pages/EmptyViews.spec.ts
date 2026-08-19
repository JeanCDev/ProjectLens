import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'

const push = vi.fn()
vi.mock('vue-router', () => ({
  useRouter: () => ({ push }),
  useRoute: () => ({ params: {} }),
}))

async function mountView(name: string) {
  const mod = await import(`@/pages/${name}.vue`)
  return mount((mod as any).default)
}

describe('EndpointsView', () => {
  it('mostra EmptyState e botao ver projetos', async () => {
    const w = await mountView('EndpointsView')
    expect(w.text()).toContain('Endpoints por projeto')
    const btn = w.find('button')
    await btn.trigger('click')
    expect(push).toHaveBeenCalledWith('/projects')
  })
})

describe('ReleasesView', () => {
  it('mostra EmptyState e botao ver projetos', async () => {
    const w = await mountView('ReleasesView')
    expect(w.text()).toContain('Releases por projeto')
    const btn = w.find('button')
    await btn.trigger('click')
    expect(push).toHaveBeenCalledWith('/projects')
  })
})

describe('EnvironmentsView', () => {
  it('mostra EmptyState e botao ver projetos', async () => {
    const w = await mountView('EnvironmentsView')
    expect(w.text()).toContain('Ambientes por projeto')
    const btn = w.find('button')
    await btn.trigger('click')
    expect(push).toHaveBeenCalledWith('/projects')
  })
})

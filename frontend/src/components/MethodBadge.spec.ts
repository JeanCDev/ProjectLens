import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import MethodBadge from './MethodBadge.vue'

describe('MethodBadge', () => {
  const methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'] as const
  for (const m of methods) {
    it(`renderiza metodo '${m}'`, () => {
      const w = mount(MethodBadge, { props: { method: m } })
      expect(w.text()).toContain(m)
    })
  }
  it('fallback para metodo desconhecido', () => {
    const w = mount(MethodBadge, { props: { method: 'FOO' as never } })
    expect(w.text()).toBeTruthy()
  })
})

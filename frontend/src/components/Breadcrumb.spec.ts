import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import Breadcrumb from './Breadcrumb.vue'

describe('Breadcrumb', () => {
  it('renderiza itens e separadores', () => {
    const w = mount(Breadcrumb, {
      props: { items: [{ label: 'A', to: '/a' }, { label: 'B' }, { label: 'C' }] },
    })
    expect(w.text()).toContain('A')
    expect(w.text()).toContain('B')
    expect(w.text()).toContain('C')
  })
  it('ultimo item nao é link', () => {
    const w = mount(Breadcrumb, { props: { items: [{ label: 'A' }] } })
    expect(w.findAll('a').length).toBe(0)
  })
})

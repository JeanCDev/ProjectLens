import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import Loading from './Loading.vue'

describe('Loading', () => {
  it('renderiza sem label', () => {
    const w = mount(Loading)
    expect(w.find('svg').exists()).toBe(true)
  })
  it('renderiza com label', () => {
    const w = mount(Loading, { props: { label: 'Carregando...' } })
    expect(w.text()).toContain('Carregando...')
  })
})

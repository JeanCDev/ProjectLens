import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import EmptyState from './EmptyState.vue'

describe('EmptyState', () => {
  it('renderiza titulo e descricao', () => {
    const w = mount(EmptyState, { props: { title: 'Vazio', description: 'nada aqui' } })
    expect(w.text()).toContain('Vazio')
    expect(w.text()).toContain('nada aqui')
  })
  it('renderiza slot default (acao)', () => {
    const w = mount(EmptyState, {
      props: { title: 'Vazio' },
      slots: { default: '<button>acao</button>' },
    })
    expect(w.find('button').text()).toBe('acao')
  })
  it('funciona sem descricao', () => {
    const w = mount(EmptyState, { props: { title: 'X' } })
    expect(w.text()).toBeTruthy()
  })
})

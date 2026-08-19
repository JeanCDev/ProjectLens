import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import SectionCard from './SectionCard.vue'

describe('SectionCard', () => {
  it('renderiza titulo e slot default', () => {
    const w = mount(SectionCard, { props: { title: 'Sobre' }, slots: { default: '<p>conteudo</p>' } })
    expect(w.text()).toContain('Sobre')
    expect(w.text()).toContain('conteudo')
  })
  it('renderiza subtitle quando passado', () => {
    const w = mount(SectionCard, { props: { title: 'T', subtitle: 'sub' } })
    expect(w.text()).toContain('sub')
  })
  it('renderiza slot actions', () => {
    const w = mount(SectionCard, {
      props: { title: 'T', actions: true },
      slots: { actions: '<button>acao</button>' },
    })
    expect(w.find('button').exists()).toBe(true)
  })
})

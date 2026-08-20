import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import StatCard from './StatCard.vue'
import { UsersIcon } from '@heroicons/vue/24/outline'

describe('StatCard', () => {
  it('renderiza titulo, valor e icone', () => {
    const w = mount(StatCard, {
      props: { title: 'Membros', value: 5, icon: UsersIcon },
    })
    expect(w.text()).toContain('Membros')
    expect(w.text()).toContain('5')
    expect(w.findComponent(UsersIcon).exists()).toBe(true)
  })
  it('renderiza accent e trend', () => {
    const w = mount(StatCard, {
      props: {
        title: 'T',
        value: 1,
        icon: UsersIcon,
        accent: 'emerald',
        trend: { direction: 'up', text: '+10%', positive: true },
      },
    })
    expect(w.text()).toContain('+10%')
  })
  it('trend negative', () => {
    const w = mount(StatCard, {
      props: {
        title: 'T',
        value: 1,
        icon: UsersIcon,
        trend: { direction: 'down', text: '-5%', positive: false },
      },
    })
    expect(w.text()).toContain('-5%')
  })
})

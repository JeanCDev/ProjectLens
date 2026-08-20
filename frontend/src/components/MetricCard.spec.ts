import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import MetricCard from './MetricCard.vue'
import { RocketLaunchIcon } from '@heroicons/vue/24/outline'

describe('MetricCard', () => {
  it('renderiza label, valor e ícone', () => {
    const w = mount(MetricCard, { props: { label: 'Releases', value: 12, icon: RocketLaunchIcon } })
    expect(w.text()).toContain('Releases')
    expect(w.text()).toContain('12')
    expect(w.findComponent(RocketLaunchIcon).exists()).toBe(true)
  })
  it('renderiza valor string e sem ícone', () => {
    const w = mount(MetricCard, { props: { label: 'X', value: 'R$ 10' } })
    expect(w.text()).toContain('R$ 10')
    expect(w.find('svg').exists()).toBe(false)
  })
  it('renderiza hint quando passado', () => {
    const w = mount(MetricCard, { props: { label: 'X', value: 1, hint: 'abc' } })
    expect(w.text()).toContain('abc')
  })
})

import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import AreaChart from './AreaChart.vue'

vi.mock('vue3-apexcharts', () => ({
  default: {
    name: 'VueApexCharts',
    props: ['type', 'height', 'options', 'series'],
    template: '<div class="apex-mock"></div>',
  },
}))

describe('AreaChart', () => {
  it('renderiza com series e categorias', () => {
    const w = mount(AreaChart, {
      props: { series: [{ name: 'x', data: [1, 2, 3] }], categories: ['a', 'b', 'c'] },
    })
    expect(w.find('.apex-mock').exists()).toBe(true)
  })
  it('aceita height custom', () => {
    const w = mount(AreaChart, {
      props: { series: [], categories: [], height: 100 },
    })
    expect(w.find('.apex-mock').exists()).toBe(true)
  })
})

import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import StackedBarChart from './StackedBarChart.vue'

vi.mock('vue3-apexcharts', () => ({
  default: {
    name: 'VueApexCharts',
    props: ['type', 'height', 'options', 'series'],
    template: '<div class="apex-mock"></div>',
  },
}))

describe('StackedBarChart', () => {
  it('renderiza com series e categorias', () => {
    const w = mount(StackedBarChart, {
      props: { series: [{ name: 'x', data: [1, 2] }], categories: ['a', 'b'] },
    })
    expect(w.find('.apex-mock').exists()).toBe(true)
  })
  it('aceita height custom', () => {
    const w = mount(StackedBarChart, { props: { series: [], categories: [], height: 200 } })
    expect(w.find('.apex-mock').exists()).toBe(true)
  })
})

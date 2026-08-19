import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import PageHeader from './PageHeader.vue'
import Breadcrumb from './Breadcrumb.vue'
import type { BreadcrumbItem } from './Breadcrumb.vue'

const items: BreadcrumbItem[] = [{ label: 'Projects', to: '/projects' }, { label: 'Detalhe' }]

describe('PageHeader', () => {
  it('renderiza titulo', () => {
    const w = mount(PageHeader, { props: { title: 'Dashboard' } })
    expect(w.text()).toContain('Dashboard')
  })
  it('renderiza subtitle', () => {
    const w = mount(PageHeader, { props: { title: 'T', subtitle: 'sub' } })
    expect(w.text()).toContain('sub')
  })
  it('renderiza breadcrumb', () => {
    const w = mount(PageHeader, { props: { title: 'T', breadcrumb: items } })
    expect(w.findComponent(Breadcrumb).exists()).toBe(true)
  })
  it('renderiza slot actions', () => {
    const w = mount(PageHeader, {
      props: { title: 'T' },
      slots: { actions: '<button>novo</button>' },
    })
    expect(w.find('button').text()).toBe('novo')
  })
})

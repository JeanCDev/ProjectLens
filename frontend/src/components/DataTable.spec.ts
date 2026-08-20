import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import type { Component } from 'vue'
import DataTable from './DataTable.vue'
import type { Column } from './DataTable.vue'

interface Row {
  id: number
  name: string
  age: number
  [key: string]: unknown
}

const columns: Column<Row>[] = [
  { key: 'name', label: 'Nome', sortable: true },
  { key: 'age', label: 'Idade', sortable: true },
]

const rows: Row[] = [
  { id: 1, name: 'Ana', age: 30 },
  { id: 2, name: 'Beto', age: 20 },
  { id: 3, name: 'Caio', age: 40 },
]

const Mounted = DataTable as Component<{
  columns: Column<Record<string, unknown>>[]
  rows: Record<string, unknown>[]
  loading?: boolean
  pageSize?: number
}>

describe('DataTable', () => {
  it('renderiza colunas e linhas', () => {
    const w = mount(Mounted, { props: { columns, rows } })
    expect(w.text()).toContain('Ana')
    expect(w.text()).toContain('Beto')
    expect(w.text()).toContain('Nome')
  })
  it('mostra EmptyState quando vazio', () => {
    const w = mount(Mounted, { props: { columns, rows: [] } })
    expect(w.text()).toContain('Nenhum registro')
  })
  it('mostra Loading quando loading', () => {
    const w = mount(Mounted, { props: { columns, rows, loading: true } })
    expect(w.find('svg').exists()).toBe(true)
  })
  it('emite row-click', async () => {
    const w = mount(Mounted, { props: { columns, rows } })
    await w.findAll('tbody tr')[0].trigger('click')
    expect(w.emitted('row-click')?.[0]).toEqual([rows[0]])
  })
  it('ordena ascendente/descendente ao clicar no cabecalho', async () => {
    const w = mount(Mounted, { props: { columns, rows } })
    const headers = w.findAll('thead th button')
    await headers[0].trigger('click') // asc by name
    expect((w.findAll('tbody tr')[0].text())).toContain('Ana')
    await headers[0].trigger('click') // desc by name
    expect((w.findAll('tbody tr')[0].text())).toContain('Caio')
  })
  it('pagina corretamente', async () => {
    const many = Array.from({ length: 25 }, (_, i) => ({ id: i, name: `N${i}`, age: i }))
    const w = mount(Mounted, { props: { columns, rows: many, pageSize: 10 } })
    expect(w.findAll('tbody tr').length).toBe(10)
    // botão de próxima página é o último button (ChevronRight da paginação)
    const buttons = w.findAll('button')
    const nextBtn = buttons[buttons.length - 1]
    await nextBtn.trigger('click')
    expect(w.text()).toContain('2 /')
  })
  it('usa slot de coluna', () => {
    const w = mount(Mounted, {
      props: { columns, rows },
      slots: { name: '<span class="custom">X</span>' },
    })
    expect(w.find('.custom').exists()).toBe(true)
  })
})

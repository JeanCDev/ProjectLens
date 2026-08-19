import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import StatusBadge from './StatusBadge.vue'

describe('StatusBadge', () => {
  const cases = [
    'planning', 'active', 'maintenance', 'archived', 'completed', 'on_hold',
    'planned', 'in_progress', 'released', 'rolled_back', 'stable', 'draft',
    'pre_release', 'deprecated', 'healthy', 'degraded', 'down', 'unknown',
    'active_env', 'inactive',
  ]
  for (const s of cases) {
    it(`renderiza status '${s}'`, () => {
      const w = mount(StatusBadge, { props: { status: s } })
      expect(w.text()).toBeTruthy()
      expect(w.classes().length).toBeGreaterThan(0)
    })
  }
  it('aceita label customizado', () => {
    const w = mount(StatusBadge, { props: { status: 'active', label: 'Ativo' } })
    expect(w.text()).toContain('Ativo')
  })
  it('fallback para status desconhecido', () => {
    const w = mount(StatusBadge, { props: { status: 'xyz' as never } })
    expect(w.text()).toBeTruthy()
  })
})

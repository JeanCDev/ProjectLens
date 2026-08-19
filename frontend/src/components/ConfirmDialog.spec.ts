import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import ConfirmDialog from './ConfirmDialog.vue'

const accept = vi.fn()
const reject = vi.fn()

vi.mock('primevue/useconfirm', () => ({
  useConfirm: () => ({
    require: (opts: { accept: () => void }) => {
      // captura o callback para disparar manualmente
      ;(globalThis as any).__accept = opts.accept
    },
  }),
}))

describe('ConfirmDialog', () => {
  beforeEach(() => {
    accept.mockClear()
    reject.mockClear()
  })
  it('renderiza slot e abre confirm no clique', async () => {
    const w = mount(ConfirmDialog, { slots: { default: 'Excluir' } })
    expect(w.text()).toContain('Excluir')
    await w.find('button').trigger('click')
    expect((globalThis as any).__accept).toBeTypeOf('function')
  })
  it('aceitar emite confirm', async () => {
    const w = mount(ConfirmDialog)
    await w.find('button').trigger('click')
    const cb = (globalThis as any).__accept as () => void
    cb()
    expect(w.emitted('confirm')).toBeTruthy()
  })
  it('renderiza botão e abre confirm por padrao', async () => {
    const w = mount(ConfirmDialog)
    expect(w.find('button').exists()).toBe(true)
    await w.find('button').trigger('click')
    expect((globalThis as any).__accept).toBeTypeOf('function')
  })
})

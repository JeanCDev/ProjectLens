import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import SearchInput from './SearchInput.vue'

describe('SearchInput', () => {
  it('emite update:modelValue e search com debounce', async () => {
    vi.useFakeTimers()
    const w = mount(SearchInput, { props: { modelValue: '' } })
    const input = w.find('input')
    await input.setValue('abc')
    vi.advanceTimersByTime(300)
    expect(w.emitted('update:modelValue')?.at(-1)).toEqual(['abc'])
    expect(w.emitted('search')?.at(-1)).toEqual(['abc'])
    vi.useRealTimers()
  })
  it('usa placeholder padrao', () => {
    const w = mount(SearchInput, { props: { modelValue: '' } })
    expect((w.find('input').element as HTMLInputElement).placeholder).toBe('Buscar...')
  })
  it('usa placeholder custom', () => {
    const w = mount(SearchInput, { props: { modelValue: '', placeholder: 'Procurar' } })
    expect((w.find('input').element as HTMLInputElement).placeholder).toBe('Procurar')
  })
  it('botao limpar zera o valor', async () => {
    vi.useFakeTimers()
    const w = mount(SearchInput, { props: { modelValue: 'x' } })
    await w.find('input').setValue('texto')
    vi.advanceTimersByTime(300)
    const btn = w.find('button')
    expect(btn.exists()).toBe(true)
    await btn.trigger('click')
    vi.advanceTimersByTime(300)
    expect(w.emitted('update:modelValue')?.at(-1)).toEqual([''])
    vi.useRealTimers()
  })
  it('sincroniza modelValue externo', async () => {
    const w = mount(SearchInput, { props: { modelValue: 'init' } })
    expect((w.find('input').element as HTMLInputElement).value).toBe('init')
    await w.setProps({ modelValue: 'novo' })
    expect((w.find('input').element as HTMLInputElement).value).toBe('novo')
  })
})

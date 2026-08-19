import { describe, it, expect } from 'vitest'
import { formatDate, formatDateTime, timeAgo } from './format'

describe('format utils', () => {
  it('formatDate formata com padrao pt-BR', () => {
    expect(formatDate('2026-01-15T10:00:00Z')).toMatch(/\d{2} de jan\. de 2026/)
  })

  it('formatDate aceita opcoes custom', () => {
    expect(formatDate('2026-01-15T10:00:00Z', { year: 'numeric' })).toContain('2026')
  })

  it('formatDateTime inclui hora', () => {
    const out = formatDateTime('2026-01-15T10:30:00Z')
    expect(out).toMatch(/\d{2}:\d{2}/)
  })

  it('timeAgo: agora mesmo', () => {
    const now = new Date().toISOString()
    expect(timeAgo(now)).toBe('agora mesmo')
  })

  it('timeAgo: minutos', () => {
    const d = new Date(Date.now() - 5 * 60 * 1000).toISOString()
    expect(timeAgo(d)).toBe('5 min atrás')
  })

  it('timeAgo: horas', () => {
    const d = new Date(Date.now() - 3 * 60 * 60 * 1000).toISOString()
    expect(timeAgo(d)).toBe('3h atrás')
  })

  it('timeAgo: dias (singular/plural)', () => {
    const d1 = new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString()
    expect(timeAgo(d1)).toBe('1 dia atrás')
    const d2 = new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString()
    expect(timeAgo(d2)).toBe('2 dias atrás')
  })
})

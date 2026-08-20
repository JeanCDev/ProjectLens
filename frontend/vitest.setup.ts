import { vi } from 'vitest'
import { config } from '@vue/test-utils'

// Stub global de componentes pesados/externos para isolar a cobertura de UI
config.global.stubs = {
  transition: false,
  'router-link': {
    template: '<a><slot /></a>',
  },
  'router-view': {
    template: '<div><slot /></div>',
  },
}

// jsdom não implementa matchMedia usado por primevue/tailwind dark — stub
if (!window.matchMedia) {
  window.matchMedia = vi.fn().mockImplementation((query: string) => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  }))
}

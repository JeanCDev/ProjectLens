import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  test: {
    environment: 'jsdom',
    globals: true,
    include: ['src/**/*.spec.ts'],
    setupFiles: ['./vitest.setup.ts'],
    coverage: {
      provider: 'v8',
      include: ['src/**/*.vue', 'src/**/format.ts', 'src/**/*.ts'],
      exclude: [
        'src/**/*.spec.ts',
        'src/main.ts',
        'src/App.vue',
        'src/router/**',
        'src/stores/**',
        'src/services/**',
        'src/env.d.ts',
      ],
      reporter: ['text', 'json-summary'],
    },
  },
})

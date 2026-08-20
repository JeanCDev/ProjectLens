import { test, expect } from '@playwright/test'
import { loginViaApi } from './helpers'

test.describe('Telas globais (EmptyState)', () => {
  test.beforeEach(async ({ page }) => {
    await loginViaApi(page)
  })

  test('Endpoints global pede para escolher projeto', async ({ page }) => {
    await page.goto('/endpoints')
    await expect(page.getByText('Endpoints por projeto')).toBeVisible()
    await expect(page.getByRole('button', { name: /Ver projetos/i })).toBeVisible()
  })

  test('Releases global pede para escolher projeto', async ({ page }) => {
    await page.goto('/releases')
    await expect(page.getByText('Releases por projeto')).toBeVisible()
  })

  test('Environments global pede para escolher projeto', async ({ page }) => {
    await page.goto('/environments')
    await expect(page.getByText('Ambientes por projeto')).toBeVisible()
  })
})

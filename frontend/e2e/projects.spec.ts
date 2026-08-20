import { test, expect } from '@playwright/test'
import { loginViaApi } from './helpers'

test.describe('Projects', () => {
  test.beforeEach(async ({ page }) => {
    await loginViaApi(page)
    await page.goto('/projects')
  })

  test('lista projetos do backend', async ({ page }) => {
    await expect(page.locator('table tbody tr').first()).toBeVisible()
    const count = await page.locator('table tbody tr').count()
    expect(count).toBeGreaterThan(0)
  })

  test('busca filtra a listagem', async ({ page }) => {
    const first = await page.locator('table tbody tr').first().innerText()
    const term = first.split('\n')[0].trim().slice(0, 4)
    await page.fill('input[type="search"]', term)
    await page.waitForTimeout(500)
    await expect(page.locator('table tbody tr').first()).toContainText(term, { ignoreCase: true })
  })

  test('clicar em projeto abre detalhes', async ({ page }) => {
    await page.locator('table tbody tr').first().click()
    await expect(page).toHaveURL(/\/projects\/\d+/)
  })
})

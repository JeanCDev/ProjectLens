import { test, expect } from '@playwright/test'
import { loginViaApi } from './helpers'

test.describe('Project Details', () => {
  test.beforeEach(async ({ page }) => {
    await loginViaApi(page)
    await page.goto('/projects')
    await page.locator('table tbody tr').first().click()
    await expect(page).toHaveURL(/\/projects\/\d+/)
  })

  test('aba Overview mostra dados do projeto', async ({ page }) => {
    await expect(page.getByText('Sobre o projeto')).toBeVisible()
  })

  test('aba Endpoints lista endpoints', async ({ page }) => {
    await page.getByRole('button', { name: /Endpoints/i }).click()
    // tanto a lista quanto o empty-state são aceitáveis
    await expect(
      page.locator('text=/Nenhum endpoint|Endpoints da API/'),
    ).toBeVisible()
  })

  test('aba Releases lista versoes', async ({ page }) => {
    await page.getByRole('button', { name: /Releases/i }).click()
    await expect(page.getByText('Histórico de releases')).toBeVisible()
  })

  test('aba Environments lista ambientes', async ({ page }) => {
    await page.getByRole('button', { name: /Environments/i }).click()
    await expect(page.locator('text=/Ambientes|Nenhum ambiente/i')).toBeVisible()
  })

  test('aba Members lista membros', async ({ page }) => {
    await page.getByRole('button', { name: /Members/i }).click()
    await expect(page.getByText(/Membros da equipe|Nenhum membro/i)).toBeVisible()
  })

  test('aba Metrics mostra contagens', async ({ page }) => {
    await page.getByRole('button', { name: /Metrics/i }).click()
    await expect(page.getByText('Endpoints saudáveis')).toBeVisible()
  })
})

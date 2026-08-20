import { test, expect } from '@playwright/test'
import { loginViaApi, createUserViaApi } from './helpers'

test.describe('Auth', () => {
  test('redireciona para /login quando nao autenticado', async ({ page }) => {
    await page.goto('/projects')
    await expect(page).toHaveURL(/\/login/)
  })

  test('login com credenciais validas redireciona para dashboard', async ({ page }) => {
    const { email, password } = await createUserViaApi()
    await page.goto('/login')
    await page.fill('#login-email', email)
    await page.fill('#login-password', password)
    await page.click('button[type="submit"]')
    await expect(page).toHaveURL('/')
  })

  test('login invalido mostra erro', async ({ page }) => {
    await page.goto('/login')
    await page.fill('#login-email', 'naoexiste@test.com')
    await page.fill('#login-password', 'senhaerrada')
    await page.click('button[type="submit"]')
    await expect(page.locator('text=Credenciais')).toBeVisible()
  })

  test('cadastro cria conta e redireciona', async ({ page }) => {
    const email = `cad_${Date.now()}@test.com`
    await page.goto('/register')
    await page.fill('#register-name', 'Novo User')
    await page.fill('#register-email', email)
    await page.fill('#register-password', 'password123')
    await page.locator('input[type="password"]').nth(1).fill('password123')
    await page.click('button[type="submit"]')
    await expect(page).toHaveURL('/')
  })

  test('logout retorna para /login', async ({ page }) => {
    await loginViaApi(page)
    await page.goto('/')
    await page.getByRole('button', { name: /Sair/i }).click()
    await expect(page).toHaveURL(/\/login/)
  })
})

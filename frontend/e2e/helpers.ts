import { test, expect, type Page } from '@playwright/test'

const API = 'http://127.0.0.1:8000/api'

async function createUserViaApi(): Promise<{ email: string; password: string }> {
  const email = `e2e_${Date.now()}@test.com`
  const password = 'password123'
  await fetch(`${API}/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ name: 'E2E User', email, password, password_confirmation: password }),
  })
  return { email, password }
}

async function loginViaApi(page: Page) {
  const email = `e2e_${Date.now()}@test.com`
  const password = 'password123'
  const resp = await fetch(`${API}/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ name: 'E2E User', email, password, password_confirmation: password }),
  })
  const data = await resp.json()
  await page.addInitScript(
    ({ token, user }) => {
      localStorage.setItem('pl_token', token)
      localStorage.setItem('pl_user', JSON.stringify(user))
    },
    { token: data.token, user: data.user },
  )
  return { email, password }
}

export { loginViaApi, createUserViaApi }

import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { AuthService, type LoginPayload, type RegisterPayload } from '@/services/AuthService'
import type { AuthUser } from '@/services/types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<AuthUser | null>(AuthService.getUser())
  const token = ref<string | null>(AuthService.getToken())
  const loading = ref(false)

  const isAuthenticated = computed(() => Boolean(token.value))

  async function login(payload: LoginPayload) {
    loading.value = true
    try {
      const auth = await AuthService.login(payload)
      user.value = auth.user
      token.value = auth.token
    } finally {
      loading.value = false
    }
  }

  async function register(payload: RegisterPayload) {
    loading.value = true
    try {
      const auth = await AuthService.register(payload)
      user.value = auth.user
      token.value = auth.token
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    try {
      await AuthService.logout()
    } finally {
      user.value = null
      token.value = null
      loading.value = false
    }
  }

  return { user, token, loading, isAuthenticated, login, register, logout }
})
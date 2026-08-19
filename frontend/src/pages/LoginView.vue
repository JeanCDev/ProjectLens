<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { EnvelopeIcon, LockClosedIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({ email: '', password: '' })
const errors = reactive<{ email?: string; password?: string; general?: string }>({})
const submitting = ref(false)

function validate(): boolean {
  errors.email = undefined
  errors.password = undefined
  errors.general = undefined
  let valid = true
  if (!form.email) {
    errors.email = 'Informe seu e-mail.'
    valid = false
  } else if (!/^\S+@\S+\.\S+$/.test(form.email)) {
    errors.email = 'Informe um e-mail válido.'
    valid = false
  }
  if (!form.password) {
    errors.password = 'Informe sua senha.'
    valid = false
  }
  return valid
}

async function onSubmit() {
  if (!validate()) return
  submitting.value = true
  try {
    await auth.login({ email: form.email, password: form.password })
    router.push('/')
  } catch (error: unknown) {
    const axiosError = error as { response?: { status?: number; data?: { errors?: Record<string, string[]> } } }
    if (axiosError.response?.status === 422) {
      const fieldErrors = axiosError.response.data?.errors ?? {}
      errors.email = fieldErrors.email?.[0]
      errors.password = fieldErrors.password?.[0]
      errors.general = (fieldErrors.email?.[0] ?? fieldErrors.password?.[0]) && !errors.email && !errors.password ? '' : undefined
      if (fieldErrors.email?.[0]) errors.email = fieldErrors.email[0]
      if (fieldErrors.password?.[0]) errors.password = fieldErrors.password[0]
    } else {
      errors.general = 'Não foi possível entrar. Verifique sua conexão e tente novamente.'
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-surface-0 px-4">
    <div class="w-full max-w-md animate-fade-in">
      <div class="mb-8 flex flex-col items-center">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/15">
          <svg class="h-8 w-8 text-indigo-400" viewBox="0 0 32 32" fill="none">
            <circle cx="16" cy="16" r="8.5" stroke="currentColor" stroke-width="2.5" />
            <circle cx="16" cy="16" r="3.5" fill="currentColor" />
          </svg>
        </div>
        <h1 class="mt-4 text-xl font-semibold tracking-tight text-surface-900">ProjectLens</h1>
        <p class="mt-1 text-sm text-surface-500">Acesse sua conta para gerenciar seus projetos.</p>
      </div>

      <div class="card-surface p-6">
        <form novalidate @submit.prevent="onSubmit">
          <div class="space-y-4">
            <div>
              <label class="mb-1 block text-xs font-medium text-surface-500" for="login-email">E-mail</label>
              <div class="relative">
                <EnvelopeIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
                <input
                  id="login-email"
                  v-model="form.email"
                  type="email"
                  autocomplete="email"
                  placeholder="voce@empresa.com"
                  class="input-field pl-9"
                />
              </div>
              <p v-if="errors.email" class="mt-1 text-xs text-rose-400">{{ errors.email }}</p>
            </div>

            <div>
              <label class="mb-1 block text-xs font-medium text-surface-500" for="login-password">Senha</label>
              <div class="relative">
                <LockClosedIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
                <input
                  id="login-password"
                  v-model="form.password"
                  type="password"
                  autocomplete="current-password"
                  placeholder="••••••••"
                  class="input-field pl-9"
                />
              </div>
              <p v-if="errors.password" class="mt-1 text-xs text-rose-400">{{ errors.password }}</p>
            </div>

            <p v-if="errors.general" class="rounded-lg border border-rose-500/20 bg-rose-500/10 px-3 py-2 text-xs text-rose-300">
              {{ errors.general }}
            </p>

            <button
              type="submit"
              :disabled="submitting"
              class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-400 disabled:opacity-60"
            >
              {{ submitting ? 'Entrando...' : 'Entrar' }}
              <ArrowRightIcon class="h-4 w-4" />
            </button>
          </div>
        </form>

        <p class="mt-6 text-center text-sm text-surface-500">
          Ainda não tem conta?
          <RouterLink to="/register" class="font-medium text-indigo-400 transition hover:text-indigo-300">
            Cadastre-se
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
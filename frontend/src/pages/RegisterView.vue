<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { EnvelopeIcon, LockClosedIcon, UserIcon, ArrowRightIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const errors = reactive<{ name?: string; email?: string; password?: string; password_confirmation?: string; general?: string }>({})
const submitting = ref(false)

function validate(): boolean {
  errors.name = undefined
  errors.email = undefined
  errors.password = undefined
  errors.password_confirmation = undefined
  errors.general = undefined
  let valid = true
  if (!form.name.trim()) {
    errors.name = 'Informe seu nome.'
    valid = false
  }
  if (!form.email) {
    errors.email = 'Informe seu e-mail.'
    valid = false
  } else if (!/^\S+@\S+\.\S+$/.test(form.email)) {
    errors.email = 'Informe um e-mail válido.'
    valid = false
  }
  if (!form.password) {
    errors.password = 'Informe uma senha.'
    valid = false
  } else if (form.password.length < 8) {
    errors.password = 'A senha deve ter pelo menos 8 caracteres.'
    valid = false
  }
  if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'As senhas não conferem.'
    valid = false
  }
  return valid
}

async function onSubmit() {
  if (!validate()) return
  submitting.value = true
  try {
    await auth.register({
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
    })
    router.push('/')
  } catch (error: unknown) {
    const axiosError = error as { response?: { status?: number; data?: { errors?: Record<string, string[]> } } }
    if (axiosError.response?.status === 422) {
      const fieldErrors = axiosError.response.data?.errors ?? {}
      for (const key of ['name', 'email', 'password', 'password_confirmation'] as const) {
        const message = fieldErrors[key]?.[0]
        if (message) errors[key] = message
      }
      if (!Object.values(errors).some((value) => value !== undefined)) {
        errors.general = 'Não foi possível concluir o cadastro.'
      }
    } else {
      errors.general = 'Não foi possível concluir o cadastro. Verifique sua conexão e tente novamente.'
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
        <h1 class="mt-4 text-xl font-semibold tracking-tight text-surface-900">Criar conta</h1>
        <p class="mt-1 text-sm text-surface-500">Cadastre-se para começar a usar o ProjectLens.</p>
      </div>

      <div class="card-surface p-6">
        <form novalidate @submit.prevent="onSubmit">
          <div class="space-y-4">
            <div>
              <label class="mb-1 block text-xs font-medium text-surface-500" for="register-name">Nome</label>
              <div class="relative">
                <UserIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
                <input
                  id="register-name"
                  v-model="form.name"
                  type="text"
                  autocomplete="name"
                  placeholder="Seu nome completo"
                  class="input-field pl-9"
                />
              </div>
              <p v-if="errors.name" class="mt-1 text-xs text-rose-400">{{ errors.name }}</p>
            </div>

            <div>
              <label class="mb-1 block text-xs font-medium text-surface-500" for="register-email">E-mail</label>
              <div class="relative">
                <EnvelopeIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
                <input
                  id="register-email"
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
              <label class="mb-1 block text-xs font-medium text-surface-500" for="register-password">Senha</label>
              <div class="relative">
                <LockClosedIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
                <input
                  id="register-password"
                  v-model="form.password"
                  type="password"
                  autocomplete="new-password"
                  placeholder="Mínimo 8 caracteres"
                  class="input-field pl-9"
                />
              </div>
              <p v-if="errors.password" class="mt-1 text-xs text-rose-400">{{ errors.password }}</p>
            </div>

            <div>
              <label class="mb-1 block text-xs font-medium text-surface-500" for="register-password-confirm">Confirmar senha</label>
              <div class="relative">
                <LockClosedIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
                <input
                  id="register-password-confirm"
                  v-model="form.password_confirmation"
                  type="password"
                  autocomplete="new-password"
                  placeholder="Repita a senha"
                  class="input-field pl-9"
                />
              </div>
              <p v-if="errors.password_confirmation" class="mt-1 text-xs text-rose-400">{{ errors.password_confirmation }}</p>
            </div>

            <p v-if="errors.general" class="rounded-lg border border-rose-500/20 bg-rose-500/10 px-3 py-2 text-xs text-rose-300">
              {{ errors.general }}
            </p>

            <button
              type="submit"
              :disabled="submitting"
              class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-400 disabled:opacity-60"
            >
              {{ submitting ? 'Criando conta...' : 'Criar conta' }}
              <ArrowRightIcon class="h-4 w-4" />
            </button>
          </div>
        </form>

        <p class="mt-6 text-center text-sm text-surface-500">
          Já possui uma conta?
          <RouterLink to="/login" class="font-medium text-indigo-400 transition hover:text-indigo-300">
            Entrar
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
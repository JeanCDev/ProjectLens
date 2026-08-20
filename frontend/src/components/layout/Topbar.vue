<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUiStore } from '@/stores/ui'
import { useAuthStore } from '@/stores/auth'
import { Bars3Icon, BellIcon, MagnifyingGlassIcon, ArrowRightEndOnRectangleIcon, ArrowLeftEndOnRectangleIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const ui = useUiStore()
const auth = useAuthStore()

const pageTitle = computed(() => {
  const title = route.meta.title
  return typeof title === 'string' ? title : 'Dashboard'
})

const initials = computed(() => {
  const name = auth.user?.name ?? ''
  const parts = name.trim().split(/\s+/)
  return ((parts[0]?.[0] ?? '') + (parts[1]?.[0] ?? '')).toUpperCase() || 'U'
})

async function onLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-white/5 bg-surface-0/80 px-4 backdrop-blur-md lg:px-6">
    <button
      type="button"
      class="flex h-9 w-9 items-center justify-center rounded-lg text-surface-500 transition hover:bg-white/5 hover:text-surface-300"
      @click="ui.toggleSidebar"
    >
      <Bars3Icon class="h-5 w-5" />
    </button>

    <h2 class="hidden text-sm font-semibold text-surface-300 sm:block">{{ pageTitle }}</h2>

    <div class="ml-auto flex items-center gap-2">
      <div class="relative hidden md:block">
        <MagnifyingGlassIcon class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-500" />
        <input
          type="search"
          placeholder="Busca global..."
          class="w-56 rounded-lg border border-white/10 bg-white/[0.02] py-2 pl-9 pr-3 text-sm text-surface-400 placeholder-surface-600 outline-none transition focus:border-indigo-500/50 focus:text-surface-300"
        />
      </div>

      <button
        type="button"
        class="relative flex h-9 w-9 items-center justify-center rounded-lg text-surface-500 transition hover:bg-white/5 hover:text-surface-300"
        title="Notificações"
      >
        <BellIcon class="h-5 w-5" />
        <span class="absolute right-2 top-2 h-1.5 w-1.5 rounded-full bg-rose-400 ring-2 ring-surface-0" />
      </button>

      <template v-if="auth.isAuthenticated">
        <button
          type="button"
          class="ml-1 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-xs font-semibold text-white transition hover:opacity-90"
          :title="auth.user?.name"
        >
          {{ initials }}
        </button>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.02] px-3 py-1.5 text-xs font-medium text-surface-300 transition hover:bg-white/5 hover:text-surface-100"
          title="Sair"
          @click="onLogout"
        >
          <ArrowRightEndOnRectangleIcon class="h-4 w-4" />
          <span class="hidden sm:inline">Sair</span>
        </button>
      </template>
      <RouterLink
        v-else
        to="/login"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-indigo-400"
      >
        <ArrowLeftEndOnRectangleIcon class="h-4 w-4" />
        Entrar
      </RouterLink>
    </div>
  </header>
</template>
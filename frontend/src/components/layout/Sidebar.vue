<script setup lang="ts">
import { computed, type Component } from 'vue'
import { useRoute } from 'vue-router'
import { useUiStore } from '@/stores/ui'
import {
  Squares2X2Icon,
  FolderIcon,
  ArrowPathRoundedSquareIcon,
  RocketLaunchIcon,
  ServerStackIcon,
  Cog6ToothIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const ui = useUiStore()

interface NavItem {
  label: string
  to: string
  icon: Component
}

const navItems: NavItem[] = [
  { label: 'Dashboard', to: '/', icon: Squares2X2Icon },
  { label: 'Projects', to: '/projects', icon: FolderIcon },
  { label: 'Endpoints', to: '/endpoints', icon: ArrowPathRoundedSquareIcon },
  { label: 'Releases', to: '/releases', icon: RocketLaunchIcon },
  { label: 'Environments', to: '/environments', icon: ServerStackIcon },
  { label: 'Settings', to: '/settings', icon: Cog6ToothIcon },
]

const isActive = computed(() => (item: NavItem) => {
  if (item.to === '/') return route.path === '/'
  return route.path.startsWith(item.to)
})
</script>

<template>
  <aside
    class="fixed inset-y-0 left-0 z-40 flex w-60 flex-col border-r border-white/5 bg-surface-50 transition-[width,transform] duration-300 lg:sticky lg:top-0 lg:h-screen"
    :class="[
      ui.sidebarCollapsed && 'lg:w-[72px]',
      ui.sidebarMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
    ]"
  >
    <div class="flex h-16 items-center gap-3 border-b border-white/5 px-4" :class="ui.sidebarCollapsed && 'lg:justify-center lg:px-2'">
      <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-500/15">
        <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 32 32" fill="none">
          <circle cx="16" cy="16" r="8.5" stroke="currentColor" stroke-width="2.5" />
          <circle cx="16" cy="16" r="3.5" fill="currentColor" />
        </svg>
      </div>
      <div v-if="!ui.sidebarCollapsed" class="min-w-0">
        <p class="truncate text-sm font-semibold text-surface-900">ProjectLens</p>
        <p class="text-[11px] text-surface-500">Tech Operations</p>
      </div>
      <button
        type="button"
        class="ml-auto flex h-8 w-8 items-center justify-center rounded-lg text-surface-500 transition hover:bg-white/5 hover:text-surface-300 lg:hidden"
        @click="ui.toggleSidebar"
      >
        <XMarkIcon class="h-5 w-5" />
      </button>
    </div>

    <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
      <p v-if="!ui.sidebarCollapsed" class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-widest text-surface-600">
        Navegação
      </p>
      <RouterLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        :title="item.label"
        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all duration-150"
        :class="[
          isActive(item)
            ? 'bg-indigo-500/10 font-medium text-indigo-300'
            : 'text-surface-500 hover:bg-white/5 hover:text-surface-300',
          ui.sidebarCollapsed && 'lg:justify-center lg:px-2',
        ]"
      >
        <component :is="item.icon" class="h-5 w-5 shrink-0" />
        <span v-if="!ui.sidebarCollapsed">{{ item.label }}</span>
      </RouterLink>
    </nav>

    <div class="border-t border-white/5 p-3">
      <div class="flex items-center gap-3 rounded-lg px-2 py-2" :class="ui.sidebarCollapsed && 'lg:justify-center lg:px-0'">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-xs font-semibold text-white">
          AS
        </div>
        <div v-if="!ui.sidebarCollapsed" class="min-w-0">
          <p class="truncate text-xs font-medium text-surface-400">Ana Souza</p>
          <p class="truncate text-[11px] text-surface-600">Administradora</p>
        </div>
      </div>
    </div>
  </aside>
</template>
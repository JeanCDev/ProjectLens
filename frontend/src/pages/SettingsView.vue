<script setup lang="ts">
import { ref } from 'vue'
import PageHeader from '@/components/PageHeader.vue'
import SectionCard from '@/components/SectionCard.vue'
import { useToast } from 'primevue/usetoast'
import { CheckIcon, KeyIcon, BellIcon, UserIcon, SunIcon } from '@heroicons/vue/24/outline'

const toast = useToast()

const profile = ref({
  name: 'Ana Souza',
  email: 'ana@projectlens.com',
  role: 'Administradora',
})

const notifications = ref({
  deploys: true,
  incidents: true,
  weeklyReport: false,
  mentions: true,
})

const themeMode = ref<'dark' | 'system'>('dark')

const apiKeys = ref([
  { id: 1, name: 'Produção', key: 'pl_live_••••••••••••9f3a', lastUsed: '12 min atrás', createdAt: '12/05/2026' },
  { id: 2, name: 'Staging', key: 'pl_test_••••••••••••4c1b', lastUsed: '2 horas atrás', createdAt: '30/06/2026' },
])

function save() {
  toast.add({
    severity: 'success',
    summary: 'Salvo',
    detail: 'Suas preferências foram atualizadas.',
    life: 3000,
  })
}
</script>

<template>
  <div>
    <PageHeader
      title="Settings"
      subtitle="Configure seu perfil, preferências de tema e chaves de API."
    />

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
      <div class="space-y-6">
        <SectionCard title="Perfil" subtitle="Suas informações pessoais">
          <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-lg font-semibold text-white">
              {{ profile.name.charAt(0) }}{{ profile.name.split(' ')[1]?.charAt(0) ?? '' }}
            </div>
            <div>
              <p class="text-sm font-semibold text-surface-300">{{ profile.name }}</p>
              <p class="text-xs text-surface-500">{{ profile.email }}</p>
              <p class="mt-0.5 text-xs text-surface-600">{{ profile.role }}</p>
            </div>
          </div>
          <div class="mt-5 space-y-3">
            <div>
              <label class="mb-1 block text-xs text-surface-500">Nome</label>
              <input v-model="profile.name" class="input-field" />
            </div>
            <div>
              <label class="mb-1 block text-xs text-surface-500">E-mail</label>
              <input v-model="profile.email" type="email" class="input-field" />
            </div>
          </div>
          <button
            type="button"
            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
            @click="save"
          >
            <CheckIcon class="h-4 w-4" />
            Salvar alterações
          </button>
        </SectionCard>

        <SectionCard title="Tema" subtitle="Preferência de aparência">
          <div class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] p-3">
            <div class="flex items-center gap-2.5">
              <SunIcon class="h-5 w-5 text-amber-300" />
              <span class="text-sm text-surface-400">Tema escuro</span>
            </div>
            <button
              type="button"
              role="switch"
              :aria-checked="themeMode === 'dark'"
              class="relative h-6 w-11 rounded-full transition"
              :class="themeMode === 'dark' ? 'bg-indigo-500' : 'bg-white/10'"
              @click="themeMode = themeMode === 'dark' ? 'system' : 'dark'"
            >
              <span
                class="absolute top-0.5 h-5 w-5 rounded-full bg-white transition-all"
                :class="themeMode === 'dark' ? 'left-[22px]' : 'left-0.5'"
              />
            </button>
          </div>
        </SectionCard>
      </div>

      <div class="space-y-6">
        <SectionCard title="Notificações" subtitle="Quais eventos devem gerar alertas">
          <div class="space-y-4">
            <div
              v-for="(enabled, key) in notifications"
              :key="key"
              class="flex items-center justify-between rounded-lg border border-white/5 bg-white/[0.02] p-3"
            >
              <div class="flex items-center gap-2.5">
                <BellIcon class="h-4 w-4 text-surface-500" />
                <div>
                  <p class="text-sm text-surface-300">
                    {{
                      {
                        deploys: 'Novos deploys',
                        incidents: 'Incidentes e falhas',
                        weeklyReport: 'Relatório semanal',
                        mentions: 'Menções e comentários',
                      }[key as string]
                    }}
                  </p>
                </div>
              </div>
              <button
                type="button"
                role="switch"
                :aria-checked="enabled"
                class="relative h-6 w-11 rounded-full transition"
                :class="enabled ? 'bg-indigo-500' : 'bg-white/10'"
                @click="notifications[key as keyof typeof notifications] = !enabled"
              >
                <span
                  class="absolute top-0.5 h-5 w-5 rounded-full bg-white transition-all"
                  :class="enabled ? 'left-[22px]' : 'left-0.5'"
                />
              </button>
            </div>
          </div>
        </SectionCard>
      </div>

      <div class="space-y-6">
        <SectionCard title="API Keys" subtitle="Tokens para integrações externas">
          <div class="space-y-3">
            <div v-for="key in apiKeys" :key="key.id" class="rounded-lg border border-white/5 bg-white/[0.02] p-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <KeyIcon class="h-4 w-4 text-amber-300" />
                  <p class="text-sm font-medium text-surface-300">{{ key.name }}</p>
                </div>
                <span class="rounded-md bg-white/5 px-2 py-0.5 text-[11px] text-surface-500">ativa</span>
              </div>
              <p class="mt-3 font-mono text-xs text-surface-400">{{ key.key }}</p>
              <div class="mt-3 flex items-center justify-between border-t border-white/5 pt-3">
                <span class="text-[11px] text-surface-600">Criada em {{ key.createdAt }}</span>
                <span class="text-[11px] text-surface-600">Último uso: {{ key.lastUsed }}</span>
              </div>
            </div>
          </div>
          <button
            type="button"
            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-white/10 bg-white/[0.02] px-4 py-2 text-sm font-medium text-surface-300 transition hover:bg-white/5"
          >
            <UserIcon class="h-4 w-4" />
            Gerar nova chave
          </button>
        </SectionCard>
      </div>
    </div>
  </div>
</template>
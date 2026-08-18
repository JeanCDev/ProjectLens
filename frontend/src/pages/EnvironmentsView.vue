<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { EnvironmentService } from '@/services/EnvironmentService'
import type { Environment } from '@/services/types'
import PageHeader from '@/components/PageHeader.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Loading from '@/components/Loading.vue'
import { PlusIcon, GlobeAltIcon, CircleStackIcon, ArrowTopRightOnSquareIcon } from '@heroicons/vue/24/outline'

const environments = ref<Environment[]>([])
const loading = ref(true)

const grouped = computed(() => {
  const types: Record<string, Environment[]> = {}
  for (const env of environments.value) {
    const key = env.type.charAt(0).toUpperCase() + env.type.slice(1)
    if (!types[key]) types[key] = []
    types[key].push(env)
  }
  return types
})

const typeAccent: Record<string, string> = {
  Production: 'bg-emerald-500/10 text-emerald-300',
  Staging: 'bg-amber-500/10 text-amber-300',
  Development: 'bg-sky-500/10 text-sky-300',
  Testing: 'bg-violet-500/10 text-violet-300',
}

onMounted(async () => {
  environments.value = await EnvironmentService.list()
  loading.value = false
})
</script>

<template>
  <div>
    <PageHeader
      title="Environments"
      subtitle="Ambientes configurados para cada projeto."
    >
      <template #actions>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3.5 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
        >
          <PlusIcon class="h-4 w-4" />
          Novo Ambiente
        </button>
      </template>
    </PageHeader>

    <Loading v-if="loading" label="Carregando ambientes..." />

    <template v-else>
      <div v-for="(envs, type) in grouped" :key="type" class="mb-8 last:mb-0">
        <div class="mb-4 flex items-center gap-2">
          <span class="rounded-md px-2 py-0.5 text-xs font-semibold" :class="typeAccent[type] ?? typeAccent.Development">
            {{ type }}
          </span>
          <span class="text-xs text-surface-600">{{ envs.length }} ambiente{{ envs.length === 1 ? '' : 's' }}</span>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
          <div v-for="env in envs" :key="env.id" class="card-surface p-5 transition hover:border-white/10">
            <div class="flex items-start justify-between">
              <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10">
                <GlobeAltIcon class="h-5 w-5 text-indigo-400" />
              </div>
              <StatusBadge :status="env.status" />
            </div>

            <h3 class="mt-4 text-sm font-semibold text-surface-200">{{ env.name }}</h3>
            <p class="mt-0.5 font-mono text-xs text-surface-600">{{ env.version }}</p>

            <div class="mt-4 space-y-2.5 border-t border-white/5 pt-4">
              <div class="flex items-center justify-between gap-3">
                <span class="text-xs text-surface-600">URL</span>
                <a
                  :href="env.url"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex max-w-[60%] items-center gap-1 truncate font-mono text-xs text-sky-300 transition hover:text-sky-200"
                >
                  {{ env.url }}
                  <ArrowTopRightOnSquareIcon class="h-3 w-3 shrink-0" />
                </a>
              </div>
              <div class="flex items-center justify-between gap-3">
                <span class="text-xs text-surface-600">Database</span>
                <span class="inline-flex items-center gap-1.5 font-mono text-xs text-surface-400">
                  <CircleStackIcon class="h-3.5 w-3.5 text-surface-500" />
                  {{ env.database }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
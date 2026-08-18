<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ReleaseService } from '@/services/ReleaseService'
import type { Release } from '@/services/types'
import { formatDate, timeAgo } from '@/utils/format'
import PageHeader from '@/components/PageHeader.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Loading from '@/components/Loading.vue'
import { RocketLaunchIcon, PlusIcon } from '@heroicons/vue/24/outline'

const releases = ref<Release[]>([])
const loading = ref(true)

onMounted(async () => {
  releases.value = await ReleaseService.list()
  loading.value = false
})
</script>

<template>
  <div>
    <PageHeader
      title="Releases"
      subtitle="Histórico de versões publicadas em todos os projetos."
    >
      <template #actions>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3.5 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
        >
          <PlusIcon class="h-4 w-4" />
          Nova Release
        </button>
      </template>
    </PageHeader>

    <Loading v-if="loading" label="Carregando releases..." />

    <div v-else class="mx-auto max-w-3xl">
      <div class="relative">
        <div class="absolute bottom-0 left-[19px] top-2 w-px bg-white/10" />
        <ol class="space-y-6">
          <li v-for="release in releases" :key="release.id" class="relative flex gap-4">
            <div class="relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/10 bg-surface-50">
              <RocketLaunchIcon class="h-5 w-5 text-indigo-400" />
            </div>
            <div class="card-surface flex-1 p-4">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                  <p class="font-mono text-sm font-semibold text-surface-200">{{ release.version }}</p>
                  <StatusBadge :status="release.status" />
                </div>
                <p class="text-xs text-surface-500">{{ timeAgo(release.released_at) }}</p>
              </div>
              <p class="mt-1 text-xs text-surface-600">
                {{ release.author }} · publicado em {{ formatDate(release.released_at) }}
              </p>
              <div class="mt-3">
                <p class="mb-1.5 text-[11px] font-medium uppercase tracking-wider text-surface-600">Changelog</p>
                <ul class="space-y-1">
                  <li
                    v-for="(change, index) in release.changelog"
                    :key="index"
                    class="flex items-start gap-2 text-sm text-surface-400"
                  >
                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-400/60" />
                    {{ change }}
                  </li>
                </ul>
              </div>
            </div>
          </li>
        </ol>
      </div>
    </div>
  </div>
</template>
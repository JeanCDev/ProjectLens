<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { EndpointService } from '@/services/EndpointService'
import { timeAgo } from '@/utils/format'
import PageHeader from '@/components/PageHeader.vue'
import SectionCard from '@/components/SectionCard.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import MethodBadge from '@/components/MethodBadge.vue'
import Loading from '@/components/Loading.vue'
import { ArrowPathIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const endpointId = Number(route.params.id)

const endpoint = ref<Awaited<ReturnType<typeof EndpointService.get>> | null>(null)
const details = ref<Awaited<ReturnType<typeof EndpointService.requestDetails>> | null>(null)
const loading = ref(true)
const checking = ref(false)

async function load() {
  loading.value = true
  try {
    endpoint.value = await EndpointService.get(endpointId)
    details.value = await EndpointService.requestDetails(endpointId)
  } finally {
    loading.value = false
  }
}

async function recheck() {
  checking.value = true
  await EndpointService.requestDetails(endpointId)
  await load()
  checking.value = false
}

onMounted(load)
</script>

<template>
  <div>
    <PageHeader
      v-if="endpoint"
      :title="endpoint.path"
      :subtitle="endpoint.description"
      :breadcrumb="[{ label: 'Endpoints', to: '/endpoints' }, { label: endpoint.path }]"
    >
      <template #actions>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.02] px-3.5 py-2 text-sm font-medium text-surface-300 transition hover:bg-white/5"
          :disabled="checking"
          @click="recheck"
        >
          <ArrowPathIcon class="h-4 w-4" :class="checking && 'animate-spin'" />
          Verificar agora
        </button>
      </template>
    </PageHeader>

    <Loading v-if="loading" label="Carregando endpoint..." />

    <template v-else-if="endpoint && details">
      <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card-surface p-4">
          <p class="text-xs uppercase tracking-wider text-surface-600">Método</p>
          <div class="mt-2">
            <MethodBadge :method="endpoint.method" />
          </div>
        </div>
        <div class="card-surface p-4">
          <p class="text-xs uppercase tracking-wider text-surface-600">Status</p>
          <div class="mt-2">
            <StatusBadge :status="endpoint.status" />
          </div>
        </div>
        <div class="card-surface p-4">
          <p class="text-xs uppercase tracking-wider text-surface-600">Tempo de resposta</p>
          <p class="mt-2 font-mono text-lg font-semibold text-surface-200">{{ details.duration_ms }}ms</p>
        </div>
        <div class="card-surface p-4">
          <p class="text-xs uppercase tracking-wider text-surface-600">Última verificação</p>
          <p class="mt-2 text-sm text-surface-300">{{ timeAgo(details.checked_at) }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <SectionCard title="Request">
          <div class="mb-4">
            <p class="mb-1.5 text-xs font-medium uppercase tracking-wider text-surface-600">Headers</p>
            <div class="rounded-lg border border-white/5 bg-surface-50 p-3 font-mono text-xs">
              <p v-for="(value, key) in details.request.headers" :key="key" class="py-0.5">
                <span class="text-sky-300">{{ key }}:</span>
                <span class="text-surface-400"> {{ value }}</span>
              </p>
            </div>
          </div>
          <div>
            <p class="mb-1.5 text-xs font-medium uppercase tracking-wider text-surface-600">Body</p>
            <pre
              v-if="details.request.body"
              class="overflow-x-auto rounded-lg border border-white/5 bg-surface-50 p-3 font-mono text-xs text-surface-400"
            >{{ JSON.stringify(details.request.body, null, 2) }}</pre>
            <p v-else class="text-xs text-surface-600">Sem corpo (GET)</p>
          </div>
        </SectionCard>

        <SectionCard title="Response">
          <div class="mb-4 flex items-center justify-between">
            <span
              class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 font-mono text-xs font-semibold"
              :class="
                details.response.status < 400
                  ? 'bg-emerald-500/10 text-emerald-300'
                  : details.response.status < 500
                    ? 'bg-amber-500/10 text-amber-300'
                    : 'bg-rose-500/10 text-rose-300'
              "
            >
              {{ details.response.status }} {{ details.response.statusText }}
            </span>
            <span class="font-mono text-xs text-surface-500">{{ details.duration_ms }}ms</span>
          </div>
          <div class="mb-4">
            <p class="mb-1.5 text-xs font-medium uppercase tracking-wider text-surface-600">Headers</p>
            <div class="rounded-lg border border-white/5 bg-surface-50 p-3 font-mono text-xs">
              <p v-for="(value, key) in details.response.headers" :key="key" class="py-0.5">
                <span class="text-sky-300">{{ key }}:</span>
                <span class="text-surface-400"> {{ value }}</span>
              </p>
            </div>
          </div>
          <div>
            <p class="mb-1.5 text-xs font-medium uppercase tracking-wider text-surface-600">Body</p>
            <pre
              class="overflow-x-auto rounded-lg border border-white/5 bg-surface-50 p-3 font-mono text-xs text-surface-400"
            >{{ JSON.stringify(details.response.body, null, 2) }}</pre>
          </div>
        </SectionCard>
      </div>
    </template>
  </div>
</template>
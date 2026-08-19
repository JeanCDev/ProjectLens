<script setup lang="ts">
import { useRouter } from 'vue-router'
import PageHeader from '@/components/PageHeader.vue'
import EmptyState from '@/components/EmptyState.vue'
import { ArrowRightIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
</script>

<template>
  <div>
    <PageHeader
      title="Environments"
      subtitle="Ambientes configurados para cada projeto."
    />

    <div class="card-surface">
      <EmptyState
        title="Ambientes por projeto"
        description="Selecione um projeto para ver os ambientes configurados, URLs e bancos de dados."
      >
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3.5 py-2 text-sm font-medium text-white transition hover:bg-indigo-400"
          @click="router.push('/projects')"
        >
          Ver projetos
          <ArrowRightIcon class="h-4 w-4" />
        </button>
      </EmptyState>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useConfirm } from 'primevue/useconfirm'

const props = withDefaults(
  defineProps<{
    title?: string
    message?: string
    confirmLabel?: string
    cancelLabel?: string
    severity?: 'danger' | 'info'
  }>(),
  {
    title: 'Confirmação',
    message: 'Tem certeza que deseja continuar?',
    confirmLabel: 'Confirmar',
    cancelLabel: 'Cancelar',
    severity: 'danger',
  },
)

const emit = defineEmits<{ confirm: [] }>()
const confirm = useConfirm()

function open() {
  confirm.require({
    message: props.message,
    header: props.title,
    icon: 'pi pi-exclamation-triangle',
    rejectLabel: props.cancelLabel,
    acceptLabel: props.confirmLabel,
    rejectProps: {
      label: props.cancelLabel,
      severity: 'secondary',
      outlined: true,
      class: '!text-xs',
    },
    acceptProps: {
      label: props.confirmLabel,
      severity: props.severity,
      class: '!text-xs',
    },
    accept: () => emit('confirm'),
  })
}

defineExpose({ open })
</script>

<template>
  <button
    type="button"
    class="inline-flex items-center gap-2 rounded-lg border border-rose-500/20 bg-rose-500/10 px-3.5 py-2 text-sm font-medium text-rose-300 transition hover:bg-rose-500/20"
    @click="open"
  >
    <slot />
  </button>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import type { ApexOptions } from 'apexcharts'

const props = defineProps<{
  series: ApexOptions['series']
  categories: string[]
  height?: number
}>()

const options = computed<ApexOptions>(() => ({
  chart: {
    type: 'area',
    height: props.height ?? 280,
    fontFamily: 'Inter, sans-serif',
    foreColor: '#64748b',
    toolbar: { show: false },
    zoom: { enabled: false },
    animations: { enabled: true, speed: 400 },
    background: 'transparent',
  },
  colors: ['#818cf8'],
  stroke: { curve: 'smooth', width: 2 },
  fill: {
    type: 'gradient',
    gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0 },
  },
  dataLabels: { enabled: false },
  grid: { borderColor: 'rgba(255,255,255,0.06)', strokeDashArray: 4 },
  xaxis: {
    categories: props.categories,
    labels: { style: { fontSize: '11px' } },
    axisBorder: { show: false },
    axisTicks: { show: false },
  },
  yaxis: {
    labels: { style: { fontSize: '11px' } },
  },
  tooltip: {
    theme: 'dark',
    style: { fontSize: '12px' },
  },
}))
</script>

<template>
  <VueApexCharts type="area" height="280" :options="options" :series="props.series" />
</template>
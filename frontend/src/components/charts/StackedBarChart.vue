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
    type: 'bar',
    height: props.height ?? 280,
    fontFamily: 'Inter, sans-serif',
    foreColor: '#64748b',
    toolbar: { show: false },
    stacked: true,
    animations: { enabled: true, speed: 400 },
    background: 'transparent',
  },
  colors: ['#34d399', '#f43f5e'],
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '45%',
      borderRadius: 4,
      borderRadiusApplication: 'end',
    },
  },
  dataLabels: { enabled: false },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    labels: { colors: '#64748b' },
    markers: { size: 5 },
    fontSize: '12px',
  },
  grid: { borderColor: 'rgba(255,255,255,0.06)', strokeDashArray: 4 },
  xaxis: {
    categories: props.categories,
    labels: { style: { fontSize: '11px' } },
    axisBorder: { show: false },
    axisTicks: { show: false },
  },
  yaxis: {
    max: 100,
    labels: { style: { fontSize: '11px' }, formatter: (v: number) => `${v}%` },
  },
  tooltip: {
    theme: 'dark',
    style: { fontSize: '12px' },
  },
}))
</script>

<template>
  <VueApexCharts type="bar" height="280" :options="options" :series="props.series" />
</template>
<template>
  <div class="w-full h-full">
    <div v-if="!props.data || props.data.length === 0" class="flex items-center justify-center h-full">
      <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
        </svg>
        <p class="mt-2 text-sm text-gray-500">No spending data yet</p>
        <p class="text-xs text-gray-400">Add expense transactions to see the chart</p>
      </div>
    </div>
    <div v-else ref="chartContainer" class="w-full h-full"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import ApexCharts from 'apexcharts'

const props = defineProps({
  data: {
    type: Array,
    required: true
  }
})

const chartContainer = ref(null)
let chart = null

const createChart = () => {
  if (!chartContainer.value || !props.data || !props.data.length) return

  const options = {
    series: props.data.map(item => item.amount),
    chart: {
      type: 'pie',
      height: 250,
    },
    labels: props.data.map(item => item.category),
    colors: props.data.map(item => item.color),
    legend: {
      position: 'bottom',
      fontSize: '12px',
    },
    dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toFixed(1) + '%'
      }
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return '$' + val.toLocaleString()
        }
      }
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  }

  chart = new ApexCharts(chartContainer.value, options)
  chart.render()
}

onMounted(() => {
  createChart()
})

watch(() => props.data, () => {
  if (chart) {
    chart.destroy()
  }
  createChart()
}, { deep: true })
</script>

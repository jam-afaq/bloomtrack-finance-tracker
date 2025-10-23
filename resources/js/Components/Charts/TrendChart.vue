<template>
  <div ref="chartContainer" class="w-full h-full"></div>
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
  if (!chartContainer.value || !props.data.length) return

  const options = {
    series: [
      {
        name: 'Income',
        data: props.data.map(item => item.income),
        color: '#10B981'
      },
      {
        name: 'Expenses',
        data: props.data.map(item => item.expenses),
        color: '#EF4444'
      },
      {
        name: 'Net',
        data: props.data.map(item => item.net),
        color: '#3B82F6'
      }
    ],
    chart: {
      type: 'line',
      height: 250,
      toolbar: {
        show: false
      }
    },
    xaxis: {
      categories: props.data.map(item => item.month),
      labels: {
        style: {
          fontSize: '12px'
        }
      }
    },
    yaxis: {
      labels: {
        formatter: function (val) {
          return '$' + val.toLocaleString()
        },
        style: {
          fontSize: '12px'
        }
      }
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return '$' + val.toLocaleString()
        }
      }
    },
    legend: {
      position: 'top',
      fontSize: '12px'
    },
    stroke: {
      width: 2
    },
    markers: {
      size: 4
    },
    grid: {
      borderColor: '#f1f5f9'
    }
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

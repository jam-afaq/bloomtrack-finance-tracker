<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            AI Insights
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Personalized financial insights powered by AI
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <button
            @click="generateInsights"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
          >
            <CpuChipIcon class="h-5 w-5 mr-2" />
            Generate New Insights
          </button>
        </div>
      </div>

      <!-- Insights List -->
      <div class="space-y-4">
        <div
          v-for="insight in (insights?.data || [])"
          :key="insight.id"
          class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200"
        >
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="flex items-start flex-1">
                <div class="flex-shrink-0">
                  <div
                    :class="[
                      insight.type === 'warning' ? 'bg-yellow-100' :
                      insight.type === 'success' ? 'bg-green-100' :
                      insight.type === 'info' ? 'bg-blue-100' :
                      'bg-purple-100',
                      'h-12 w-12 rounded-lg flex items-center justify-center'
                    ]"
                  >
                    <CpuChipIcon
                      :class="[
                        insight.type === 'warning' ? 'text-yellow-600' :
                        insight.type === 'success' ? 'text-green-600' :
                        insight.type === 'info' ? 'text-blue-600' :
                        'text-purple-600',
                        'h-6 w-6'
                      ]"
                    />
                  </div>
                </div>
                <div class="ml-4 flex-1">
                  <div class="flex items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ insight.title }}</h3>
                    <span
                      v-if="!insight.is_read"
                      class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      New
                    </span>
                  </div>
                  <p class="mt-2 text-sm text-gray-600">{{ insight.content }}</p>
                  <div class="mt-4 flex items-center text-xs text-gray-500">
                    <CalendarIcon class="h-4 w-4 mr-1" />
                    <span>{{ insight.created_at }}</span>
                  </div>
                </div>
              </div>
              <div class="ml-4 flex-shrink-0">
                <button
                  @click="deleteInsight(insight.id)"
                  class="text-gray-400 hover:text-red-600 transition-colors"
                >
                  <TrashIcon class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="!insights?.data || insights.data.length === 0"
          class="bg-white shadow rounded-lg p-12 text-center"
        >
          <CpuChipIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No insights yet</h3>
          <p class="mt-1 text-sm text-gray-500">Generate AI insights to get personalized financial recommendations.</p>
          <div class="mt-6">
            <button
              @click="generateInsights"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700"
            >
              <CpuChipIcon class="h-5 w-5 mr-2" />
              Generate Insights
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { CpuChipIcon, CalendarIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  insights: {
    type: Object,
    default: () => ({ data: [] })
  }
})

const generateInsights = () => {
  router.post(route('dashboard.generate-insights'))
}

const deleteInsight = (id) => {
  if (confirm('Are you sure you want to delete this insight?')) {
    router.delete(route('insights.destroy', id))
  }
}
</script>

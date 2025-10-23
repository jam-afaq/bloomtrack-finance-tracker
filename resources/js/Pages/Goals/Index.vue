<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Financial Goals
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Set and track your financial goals
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <Link
            :href="route('goals.create')"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Create Goal
          </Link>
        </div>
      </div>

      <!-- Goals Grid -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div
          v-for="goal in goals"
          :key="goal.id"
          class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <FlagIcon class="h-6 w-6 text-white" />
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-gray-900">{{ goal.title }}</h3>
                  <p class="text-sm text-gray-500">{{ goal.description }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <Link
                  :href="route('goals.edit', goal.id)"
                  class="text-blue-600 hover:text-blue-900 text-sm font-medium"
                >
                  Edit
                </Link>
                <button
                  @click="deleteGoal(goal.id)"
                  class="text-red-600 hover:text-red-900 text-sm font-medium"
                >
                  Delete
                </button>
              </div>
            </div>

            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Progress</span>
                <span class="text-sm font-medium text-gray-900">{{ goal.progress_percentage }}%</span>
              </div>
              
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="h-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 transition-all duration-300"
                  :style="{ width: Math.min(goal.progress_percentage, 100) + '%' }"
                ></div>
              </div>

              <div class="flex items-center justify-between text-sm">
                <div>
                  <p class="text-gray-600">Current Amount</p>
                  <p class="text-lg font-semibold text-gray-900">${{ formatCurrency(goal.current_amount) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-gray-600">Target Amount</p>
                  <p class="text-lg font-semibold text-gray-900">${{ formatCurrency(goal.target_amount) }}</p>
                </div>
              </div>

              <div class="pt-3 border-t border-gray-200 flex items-center justify-between text-sm">
                <div class="flex items-center text-gray-600">
                  <CalendarIcon class="h-4 w-4 mr-1" />
                  <span>Target: {{ goal.target_date }}</span>
                </div>
                <div
                  :class="[
                    goal.days_remaining > 30 ? 'text-green-600' :
                    goal.days_remaining > 7 ? 'text-yellow-600' :
                    'text-red-600',
                    'font-medium'
                  ]"
                >
                  {{ goal.days_remaining }} days left
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="goals.length === 0"
          class="col-span-full bg-white shadow rounded-lg p-12 text-center"
        >
          <FlagIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No goals</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by creating a financial goal.</p>
          <div class="mt-6">
            <Link
              :href="route('goals.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700"
            >
              <PlusIcon class="h-5 w-5 mr-2" />
              Create Goal
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { PlusIcon, FlagIcon, CalendarIcon } from '@heroicons/vue/24/outline'

defineProps({
  goals: {
    type: Array,
    default: () => []
  }
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

const deleteGoal = (id) => {
  if (confirm('Are you sure you want to delete this goal?')) {
    router.delete(route('goals.destroy', id))
  }
}
</script>

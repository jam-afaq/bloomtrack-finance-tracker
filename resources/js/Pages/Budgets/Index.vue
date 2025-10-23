<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Budgets
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Set spending limits and track your budget progress
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <Link
            :href="route('budgets.create')"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Create Budget
          </Link>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <ChartBarIcon class="h-6 w-6 text-blue-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Budget</dt>
                  <dd class="text-lg font-medium text-gray-900">
                    ${{ formatCurrency(totalBudget) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Spent</dt>
                  <dd class="text-lg font-medium text-gray-900">
                    ${{ formatCurrency(totalSpent) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <BanknotesIcon class="h-6 w-6 text-purple-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Remaining</dt>
                  <dd class="text-lg font-medium text-gray-900">
                    ${{ formatCurrency(totalBudget - totalSpent) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Budgets List -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Active Budgets</h3>
        </div>
        <div class="p-6">
          <div v-if="budgets.length === 0" class="text-center py-12">
            <ChartBarIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No budgets</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a budget.</p>
            <div class="mt-6">
              <Link
                :href="route('budgets.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700"
              >
                <PlusIcon class="h-5 w-5 mr-2" />
                Create Budget
              </Link>
            </div>
          </div>

          <div v-else class="space-y-6">
            <div
              v-for="budget in budgets"
              :key="budget.id"
              class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
            >
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                  <div
                    class="w-3 h-3 rounded-full mr-3"
                    :style="{ backgroundColor: budget.category_color }"
                  ></div>
                  <div>
                    <h4 class="text-lg font-medium text-gray-900">{{ budget.category_name }}</h4>
                    <p class="text-sm text-gray-500">{{ budget.start_date }} - {{ budget.end_date }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <Link
                    :href="route('budgets.edit', budget.id)"
                    class="text-blue-600 hover:text-blue-900 text-sm font-medium"
                  >
                    Edit
                  </Link>
                  <button
                    @click="deleteBudget(budget.id)"
                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                  >
                    Delete
                  </button>
                </div>
              </div>

              <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-600">
                    ${{ formatCurrency(budget.spent_amount) }} of ${{ formatCurrency(budget.amount) }}
                  </span>
                  <span
                    :class="[
                      budget.percentage_used > 100 ? 'text-red-600' :
                      budget.percentage_used > 80 ? 'text-yellow-600' :
                      'text-green-600',
                      'font-medium'
                    ]"
                  >
                    {{ budget.percentage_used }}%
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                  <div
                    class="h-3 rounded-full transition-all duration-300"
                    :class="[
                      budget.percentage_used > 100 ? 'bg-red-500' :
                      budget.percentage_used > 80 ? 'bg-yellow-500' :
                      'bg-green-500'
                    ]"
                    :style="{ width: Math.min(budget.percentage_used, 100) + '%' }"
                  ></div>
                </div>
                <p class="text-xs text-gray-500">
                  ${{ formatCurrency(budget.amount - budget.spent_amount) }} remaining
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { PlusIcon, ChartBarIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline'
import { BanknotesIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  budgets: {
    type: Array,
    default: () => []
  }
})

const totalBudget = computed(() => {
  return props.budgets.reduce((sum, budget) => sum + parseFloat(budget.amount || 0), 0)
})

const totalSpent = computed(() => {
  return props.budgets.reduce((sum, budget) => sum + parseFloat(budget.spent_amount || 0), 0)
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

const deleteBudget = (id) => {
  if (confirm('Are you sure you want to delete this budget?')) {
    router.delete(route('budgets.destroy', id))
  }
}
</script>

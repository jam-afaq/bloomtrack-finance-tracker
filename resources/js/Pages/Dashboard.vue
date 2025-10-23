<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Dashboard
            </h2>
          <p class="mt-1 text-sm text-gray-500">
            Welcome back! Here's your financial overview.
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <button
            @click="generateInsights"
            class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <CpuChipIcon class="h-4 w-4 mr-2" />
            Generate AI Insights
          </button>
        </div>
      </div>

      <!-- Overview Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Income</dt>
                  <dd class="text-lg font-medium text-gray-900">
                    ${{ formatCurrency(overview.total_income) }}
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
                <MinusCircleIcon class="h-6 w-6 text-red-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Expenses</dt>
                  <dd class="text-lg font-medium text-gray-900">
                    ${{ formatCurrency(overview.total_expenses) }}
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
                <ChartBarIcon class="h-6 w-6 text-blue-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Net Savings</dt>
                  <dd class="text-lg font-medium text-gray-900">
                    ${{ formatCurrency(overview.net_savings) }}
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
                <FlagIcon class="h-6 w-6 text-purple-600" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Active Budgets</dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ overview.active_budgets_count }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and Data -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Spending by Category Chart -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Spending by Category</h3>
          <div class="h-64">
            <SpendingChart :data="chart_data.spending_by_category" />
          </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Trend</h3>
          <div class="h-64">
            <TrendChart :data="chart_data.monthly_trend" />
          </div>
        </div>
      </div>

      <!-- Active Budgets and Goals -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Budgets -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Active Budgets</h3>
          </div>
          <div class="p-6">
            <div v-if="active_budgets.length === 0" class="text-center py-4">
              <ChartBarIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No active budgets</h3>
              <p class="mt-1 text-sm text-gray-500">Get started by creating a budget.</p>
              <div class="mt-6">
                <Link
                  :href="route('budgets.create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  Create Budget
                </Link>
              </div>
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="budget in active_budgets"
                :key="budget.id"
                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center">
                  <div
                    class="w-3 h-3 rounded-full mr-3"
                    :style="{ backgroundColor: budget.color }"
                  ></div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ budget.category_name }}</p>
                    <p class="text-xs text-gray-500">
                      ${{ formatCurrency(budget.spent_amount) }} of ${{ formatCurrency(budget.amount) }}
                    </p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">{{ budget.percentage_used }}%</p>
                  <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                    <div
                      class="h-2 rounded-full"
                      :class="budget.percentage_used > 80 ? 'bg-red-500' : budget.percentage_used > 60 ? 'bg-yellow-500' : 'bg-green-500'"
                      :style="{ width: Math.min(budget.percentage_used, 100) + '%' }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Active Goals -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Active Goals</h3>
          </div>
          <div class="p-6">
            <div v-if="active_goals.length === 0" class="text-center py-4">
              <FlagIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No active goals</h3>
              <p class="mt-1 text-sm text-gray-500">Set a financial goal to get started.</p>
              <div class="mt-6">
                <Link
                  :href="route('goals.create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  Create Goal
                </Link>
              </div>
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="goal in active_goals"
                :key="goal.id"
                class="p-4 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center justify-between mb-2">
                  <h4 class="text-sm font-medium text-gray-900">{{ goal.title }}</h4>
                  <span class="text-sm text-gray-500">{{ goal.progress_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                  <div
                    class="h-2 rounded-full bg-blue-500"
                    :style="{ width: Math.min(goal.progress_percentage, 100) + '%' }"
                  ></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                  <span>${{ formatCurrency(goal.current_amount) }} of ${{ formatCurrency(goal.target_amount) }}</span>
                  <span>{{ goal.days_remaining }} days left</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Transactions and Insights -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
            <Link
              :href="route('transactions.index')"
              class="text-sm text-blue-600 hover:text-blue-500"
            >
              View all
            </Link>
          </div>
          <div class="p-6">
            <div v-if="recent_transactions.length === 0" class="text-center py-4">
              <CurrencyDollarIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions yet</h3>
              <p class="mt-1 text-sm text-gray-500">Start by adding your first transaction.</p>
              <div class="mt-6">
                <Link
                  :href="route('transactions.create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  Add Transaction
                </Link>
              </div>
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="transaction in recent_transactions"
                :key="transaction.id"
                class="flex items-center justify-between py-2"
              >
                <div class="flex items-center">
                  <div
                    class="w-3 h-3 rounded-full mr-3"
                    :style="{ backgroundColor: transaction.category_color }"
                  ></div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ transaction.category_name }}</p>
                    <p class="text-xs text-gray-500">{{ transaction.note || 'No description' }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p
                    class="text-sm font-medium"
                    :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                  >
                    {{ transaction.type === 'income' ? '+' : '-' }}${{ formatCurrency(transaction.amount) }}
                  </p>
                  <p class="text-xs text-gray-500">{{ transaction.date }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Latest Insights -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">AI Insights</h3>
            <Link
              :href="route('insights.index')"
              class="text-sm text-blue-600 hover:text-blue-500"
            >
              View all
            </Link>
          </div>
          <div class="p-6">
            <div v-if="latest_insights.length === 0" class="text-center py-4">
              <CpuChipIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">No insights yet</h3>
              <p class="mt-1 text-sm text-gray-500">Generate AI insights to get personalized recommendations.</p>
              <div class="mt-6">
                <button
                  @click="generateInsights"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                  Generate Insights
                </button>
              </div>
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="insight in latest_insights"
                :key="insight.id"
                class="p-4 bg-gray-50 rounded-lg"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">{{ insight.title }}</h4>
                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ insight.content }}</p>
                    <p class="mt-2 text-xs text-gray-500">{{ insight.created_at }}</p>
                  </div>
                  <span
                    v-if="!insight.is_read"
                    class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    New
                  </span>
                </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SpendingChart from '@/Components/Charts/SpendingChart.vue'
import TrendChart from '@/Components/Charts/TrendChart.vue'
import {
  CurrencyDollarIcon,
  MinusCircleIcon,
  ChartBarIcon,
  FlagIcon,
  CpuChipIcon,
} from '@heroicons/vue/24/outline'

defineProps({
  overview: Object,
  active_budgets: Array,
  active_goals: Array,
  recent_transactions: Array,
  latest_insights: Array,
  chart_data: Object,
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

const generateInsights = () => {
  router.post(route('dashboard.generate-insights'))
}
</script>
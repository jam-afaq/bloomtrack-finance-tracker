<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
            Edit Budget
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Update your budget settings
          </p>
        </div>
        <Link
          :href="route('budgets.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        >
          Cancel
        </Link>
      </div>

      <!-- Form -->
      <div class="bg-white shadow rounded-lg p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Category -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
              Category <span class="text-red-500">*</span>
            </label>
            <select
              id="category"
              v-model="form.category_id"
              required
              class="block w-full pl-3 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select a category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Only expense categories are shown</p>
            <p v-if="form.errors.category_id" class="mt-2 text-sm text-red-600">{{ form.errors.category_id }}</p>
          </div>

          <!-- Amount -->
          <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
              Budget Amount <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
              </div>
              <input
                id="amount"
                type="number"
                step="0.01"
                v-model="form.amount"
                required
                class="block w-full pl-7 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="0.00"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">Maximum amount you want to spend in this category</p>
            <p v-if="form.errors.amount" class="mt-2 text-sm text-red-600">{{ form.errors.amount }}</p>
          </div>

          <!-- Date Range -->
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Start Date -->
            <div>
              <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                Start Date <span class="text-red-500">*</span>
              </label>
              <input
                id="start_date"
                type="date"
                v-model="form.start_date"
                required
                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <p v-if="form.errors.start_date" class="mt-2 text-sm text-red-600">{{ form.errors.start_date }}</p>
            </div>

            <!-- End Date -->
            <div>
              <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                End Date <span class="text-red-500">*</span>
              </label>
              <input
                id="end_date"
                type="date"
                v-model="form.end_date"
                required
                :min="form.start_date"
                class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <p v-if="form.errors.end_date" class="mt-2 text-sm text-red-600">{{ form.errors.end_date }}</p>
            </div>
          </div>

          <!-- Active Status -->
          <div>
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="form.is_active"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-700">Budget is active</span>
            </label>
            <p class="mt-1 text-xs text-gray-500">Inactive budgets won't show alerts or track spending</p>
            <p v-if="form.errors.is_active" class="mt-2 text-sm text-red-600">{{ form.errors.is_active }}</p>
          </div>

          <!-- Current Status (if budget exists) -->
          <div v-if="budget.spent_amount !== undefined" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3">Current Budget Status</h3>
            <div class="grid grid-cols-3 gap-4 text-center">
              <div>
                <p class="text-xs text-gray-500">Budget</p>
                <p class="text-lg font-semibold text-gray-900">${{ parseFloat(budget.amount).toFixed(2) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Spent</p>
                <p class="text-lg font-semibold text-red-600">${{ parseFloat(budget.spent_amount || 0).toFixed(2) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Remaining</p>
                <p class="text-lg font-semibold text-green-600">${{ parseFloat(budget.remaining_amount || budget.amount).toFixed(2) }}</p>
              </div>
            </div>
          </div>

          <!-- Info Box -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Budget Tips</h3>
                <div class="mt-2 text-sm text-blue-700">
                  <ul class="list-disc list-inside space-y-1">
                    <li>Changing dates will recalculate spent amounts</li>
                    <li>You can deactivate a budget without deleting it</li>
                    <li>Budget tracking is based on transaction dates within the period</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 pt-4">
            <Link
              :href="route('budgets.index')"
              class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-50"
            >
              <span v-if="!form.processing">Update Budget</span>
              <span v-else>Updating...</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  budget: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  }
})

// Format dates for input fields (YYYY-MM-DD)
const formatDateForInput = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toISOString().split('T')[0]
}

const form = useForm({
  category_id: props.budget.category_id,
  amount: props.budget.amount,
  start_date: formatDateForInput(props.budget.start_date),
  end_date: formatDateForInput(props.budget.end_date),
  is_active: props.budget.is_active ?? true
})

const submit = () => {
  form.put(route('budgets.update', props.budget.id))
}
</script>

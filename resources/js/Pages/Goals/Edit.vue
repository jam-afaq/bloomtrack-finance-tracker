<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
            Edit Goal
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Update your goal details and progress
          </p>
        </div>
        <Link
          :href="route('goals.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        >
          Cancel
        </Link>
      </div>

      <!-- Form -->
      <div class="bg-white shadow rounded-lg p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
              Goal Title <span class="text-red-500">*</span>
            </label>
            <input
              id="title"
              type="text"
              v-model="form.title"
              required
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="e.g., Emergency Fund, Vacation, New Car"
            />
            <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">{{ form.errors.title }}</p>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
              Description (Optional)
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Add details about your goal..."
            ></textarea>
            <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
          </div>

          <!-- Target Amount -->
          <div>
            <label for="target_amount" class="block text-sm font-medium text-gray-700 mb-1">
              Target Amount <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
              </div>
              <input
                id="target_amount"
                type="number"
                step="0.01"
                v-model="form.target_amount"
                required
                class="block w-full pl-7 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="0.00"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">How much do you want to save?</p>
            <p v-if="form.errors.target_amount" class="mt-2 text-sm text-red-600">{{ form.errors.target_amount }}</p>
          </div>

          <!-- Current Amount -->
          <div>
            <label for="current_amount" class="block text-sm font-medium text-gray-700 mb-1">
              Current Amount <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
              </div>
              <input
                id="current_amount"
                type="number"
                step="0.01"
                v-model="form.current_amount"
                required
                class="block w-full pl-7 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="0.00"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">How much have you saved so far?</p>
            <p v-if="form.errors.current_amount" class="mt-2 text-sm text-red-600">{{ form.errors.current_amount }}</p>
          </div>

          <!-- Target Date -->
          <div>
            <label for="target_date" class="block text-sm font-medium text-gray-700 mb-1">
              Target Date <span class="text-red-500">*</span>
            </label>
            <input
              id="target_date"
              type="date"
              v-model="form.target_date"
              required
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p class="mt-1 text-xs text-gray-500">When do you want to achieve this goal?</p>
            <p v-if="form.errors.target_date" class="mt-2 text-sm text-red-600">{{ form.errors.target_date }}</p>
          </div>

          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              id="status"
              v-model="form.status"
              required
              class="block w-full pl-3 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="paused">Paused</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <p v-if="form.errors.status" class="mt-2 text-sm text-red-600">{{ form.errors.status }}</p>
          </div>

          <!-- Progress Display -->
          <div v-if="goal.progress_percentage !== undefined" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-3">Current Progress</h3>
            <div class="space-y-3">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Progress</span>
                <span class="font-medium text-gray-900">{{ goal.progress_percentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                  class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-300"
                  :style="{ width: Math.min(goal.progress_percentage, 100) + '%' }"
                ></div>
              </div>
              <div class="grid grid-cols-3 gap-4 text-center pt-2">
                <div>
                  <p class="text-xs text-gray-500">Target</p>
                  <p class="text-sm font-semibold text-gray-900">${{ parseFloat(goal.target_amount).toFixed(2) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Current</p>
                  <p class="text-sm font-semibold text-blue-600">${{ parseFloat(goal.current_amount).toFixed(2) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Remaining</p>
                  <p class="text-sm font-semibold text-purple-600">${{ parseFloat(goal.remaining_amount).toFixed(2) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 pt-4">
            <Link
              :href="route('goals.index')"
              class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-50"
            >
              <span v-if="!form.processing">Update Goal</span>
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
  goal: {
    type: Object,
    required: true
  }
})

// Format date for input field
const formatDateForInput = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toISOString().split('T')[0]
}

const form = useForm({
  title: props.goal.title,
  description: props.goal.description || '',
  target_amount: props.goal.target_amount,
  current_amount: props.goal.current_amount || 0,
  target_date: formatDateForInput(props.goal.target_date),
  status: props.goal.status || 'active'
})

const submit = () => {
  form.put(route('goals.update', props.goal.id))
}
</script>

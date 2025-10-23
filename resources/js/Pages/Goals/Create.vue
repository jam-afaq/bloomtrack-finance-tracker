<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
            Create Goal
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Set a financial goal and track your progress
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
              Current Amount (Optional)
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
              :min="tomorrow"
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p class="mt-1 text-xs text-gray-500">When do you want to achieve this goal?</p>
            <p v-if="form.errors.target_date" class="mt-2 text-sm text-red-600">{{ form.errors.target_date }}</p>
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
                <h3 class="text-sm font-medium text-blue-800">Goal Tips</h3>
                <div class="mt-2 text-sm text-blue-700">
                  <ul class="list-disc list-inside space-y-1">
                    <li>Set realistic and achievable goals</li>
                    <li>Break large goals into smaller milestones</li>
                    <li>Track your progress regularly</li>
                    <li>Celebrate when you reach your goal!</li>
                  </ul>
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
              <span v-if="!form.processing">Create Goal</span>
              <span v-else>Creating...</span>
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

// Get tomorrow's date for min date validation
const tomorrow = new Date()
tomorrow.setDate(tomorrow.getDate() + 1)
const tomorrowStr = tomorrow.toISOString().split('T')[0]

const form = useForm({
  title: '',
  description: '',
  target_amount: '',
  current_amount: 0,
  target_date: ''
})

const submit = () => {
  form.post(route('goals.store'))
}
</script>

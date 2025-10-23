<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
            Add Transaction
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Record a new income or expense transaction
          </p>
        </div>
        <Link
          :href="route('transactions.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        >
          Cancel
        </Link>
      </div>

      <!-- Form -->
      <div class="bg-white shadow rounded-lg p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
            <div class="grid grid-cols-2 gap-4">
              <button
                type="button"
                @click="form.type = 'income'"
                :class="[
                  form.type === 'income'
                    ? 'bg-green-50 border-green-500 text-green-700'
                    : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                  'border-2 rounded-lg p-4 flex items-center justify-center font-medium transition-all duration-200'
                ]"
              >
                Income
              </button>
              <button
                type="button"
                @click="form.type = 'expense'"
                :class="[
                  form.type === 'expense'
                    ? 'bg-red-50 border-red-500 text-red-700'
                    : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                  'border-2 rounded-lg p-4 flex items-center justify-center font-medium transition-all duration-200'
                ]"
              >
                Expense
              </button>
            </div>
            <p v-if="form.errors.type" class="mt-2 text-sm text-red-600">{{ form.errors.type }}</p>
          </div>

          <!-- Amount -->
          <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
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
            <p v-if="form.errors.amount" class="mt-2 text-sm text-red-600">{{ form.errors.amount }}</p>
          </div>

          <!-- Category -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
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
            <p v-if="form.errors.category_id" class="mt-2 text-sm text-red-600">{{ form.errors.category_id }}</p>
          </div>

          <!-- Date -->
          <div>
            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input
              id="date"
              type="date"
              v-model="form.date"
              required
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p v-if="form.errors.date" class="mt-2 text-sm text-red-600">{{ form.errors.date }}</p>
          </div>

          <!-- Description -->
          <div>
            <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
            <textarea
              id="note"
              v-model="form.note"
              rows="3"
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Add a note about this transaction..."
            ></textarea>
            <p v-if="form.errors.note" class="mt-2 text-sm text-red-600">{{ form.errors.note }}</p>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 pt-4">
            <Link
              :href="route('transactions.index')"
              class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-50"
            >
              <span v-if="!form.processing">Add Transaction</span>
              <span v-else>Adding...</span>
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

defineProps({
  categories: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  type: 'expense',
  amount: '',
  category_id: '',
  date: new Date().toISOString().split('T')[0],
  note: ''
})

const submit = () => {
  form.post(route('transactions.store'))
}
</script>

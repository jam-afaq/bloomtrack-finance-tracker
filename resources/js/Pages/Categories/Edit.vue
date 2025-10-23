<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
            Edit Category
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Update category details
          </p>
        </div>
        <Link
          :href="route('categories.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
        >
          Cancel
        </Link>
      </div>

      <!-- Form -->
      <div class="bg-white shadow rounded-lg p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              required
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="e.g., Groceries, Salary, Entertainment"
            />
            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category Type</label>
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

          <!-- Color -->
          <div>
            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
            <div class="flex items-center gap-4">
              <input
                id="color"
                type="color"
                v-model="form.color"
                required
                class="h-12 w-20 border border-gray-300 rounded-lg cursor-pointer"
              />
              <input
                type="text"
                v-model="form.color"
                required
                pattern="^#[0-9A-Fa-f]{6}$"
                class="flex-1 px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="#3B82F6"
              />
            </div>
            <p v-if="form.errors.color" class="mt-2 text-sm text-red-600">{{ form.errors.color }}</p>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end gap-3 pt-4">
            <Link
              :href="route('categories.index')"
              class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-50"
            >
              <span v-if="!form.processing">Update Category</span>
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
  category: {
    type: Object,
    required: true
  }
})

const form = useForm({
  name: props.category.name,
  type: props.category.type,
  color: props.category.color,
  icon: props.category.icon || ''
})

const submit = () => {
  form.put(route('categories.update', props.category.id))
}
</script>

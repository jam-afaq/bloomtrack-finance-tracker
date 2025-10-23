<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Categories
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Organize your transactions with custom categories
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <Link
            :href="route('categories.create')"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Add Category
          </Link>
        </div>
      </div>

      <!-- Categories Grid -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="category in allCategories"
          :key="category.id"
          class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow duration-200"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <div
                class="w-12 h-12 rounded-lg flex items-center justify-center"
                :style="{ backgroundColor: category.color + '20' }"
              >
                <div
                  class="w-6 h-6 rounded-full"
                  :style="{ backgroundColor: category.color }"
                ></div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">{{ category.name }}</h3>
                <p class="text-sm text-gray-500">{{ category.type }}</p>
              </div>
            </div>
          </div>
          
          <div class="mt-4 flex items-center justify-between text-sm">
            <span class="text-gray-500">{{ category.transactions_count || 0 }} transactions</span>
            <div class="flex items-center gap-2">
              <button
                @click="editCategory(category)"
                class="text-blue-600 hover:text-blue-900 font-medium"
              >
                Edit
              </button>
              <button
                @click="deleteCategory(category.id)"
                class="text-red-600 hover:text-red-900 font-medium"
              >
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="allCategories.length === 0"
          class="col-span-full bg-white shadow rounded-lg p-12 text-center"
        >
          <TagIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by creating a new category.</p>
          <div class="mt-6">
            <Link
              :href="route('categories.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700"
            >
              <PlusIcon class="h-5 w-5 mr-2" />
              Add Category
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { PlusIcon, TagIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  incomeCategories: {
    type: Array,
    default: () => []
  },
  expenseCategories: {
    type: Array,
    default: () => []
  }
})

const allCategories = [...props.incomeCategories, ...props.expenseCategories]

const editCategory = (category) => {
  // Navigate to edit page or open modal
  router.visit(route('categories.edit', category.id))
}

const deleteCategory = (id) => {
  if (confirm('Are you sure you want to delete this category?')) {
    router.delete(route('categories.destroy', id))
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
              <Link :href="route('dashboard')" class="flex items-center">
                <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                  <span class="text-white font-bold text-sm">BT</span>
                </div>
                <span class="ml-2 text-xl font-bold text-gray-900">BloomTrack</span>
              </Link>
            </div>
            
            <!-- Desktop Navigation Links -->
            <div class="hidden md:ml-6 md:flex md:space-x-8">
              <Link
                :href="route('dashboard')"
                :class="[
                  $page.url === '/dashboard' 
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                <HomeIcon class="h-4 w-4 mr-1" />
                Dashboard
              </Link>
              
              <Link
                :href="route('transactions.index')"
                :class="[
                  $page.url.startsWith('/transactions') 
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                <CurrencyDollarIcon class="h-4 w-4 mr-1" />
                Transactions
              </Link>
              
              <Link
                :href="route('categories.index')"
                :class="[
                  $page.url.startsWith('/categories') 
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                <TagIcon class="h-4 w-4 mr-1" />
                Categories
              </Link>
              
              <Link
                :href="route('budgets.index')"
                :class="[
                  $page.url.startsWith('/budgets') 
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                <ChartBarIcon class="h-4 w-4 mr-1" />
                Budgets
              </Link>
              
              <Link
                :href="route('goals.index')"
                :class="[
                  $page.url.startsWith('/goals') 
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                <FlagIcon class="h-4 w-4 mr-1" />
                Goals
              </Link>
              
              <Link
                :href="route('insights.index')"
                :class="[
                  $page.url.startsWith('/insights') 
                    ? 'border-blue-500 text-gray-900' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                ]"
              >
                <CpuChipIcon class="h-4 w-4 mr-1" />
                Insights
              </Link>
            </div>
          </div>
          
          <!-- Right side -->
          <div class="flex items-center">
            <!-- Mobile menu button -->
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            >
              <span class="sr-only">Open main menu</span>
              <Bars3Icon v-if="!showMobileMenu" class="block h-6 w-6" />
              <XMarkIcon v-else class="block h-6 w-6" />
            </button>

            <!-- User Menu -->
            <div class="hidden md:flex md:items-center">
              <div class="relative">
                <button
                  @click="showUserMenu = !showUserMenu"
                  class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <span class="sr-only">Open user menu</span>
                  <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white font-medium text-sm">
                      {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </button>
                
                <!-- Dropdown menu -->
                <Transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <div
                    v-show="showUserMenu"
                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                  >
                    <Link
                      :href="route('profile.edit')"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      @click="showUserMenu = false"
                    >
                      Your Profile
                    </Link>
                    <Link
                      :href="route('logout')"
                      method="post"
                      as="button"
                      class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Sign out
                    </Link>
                  </div>
                </Transition>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile menu -->
        <Transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <div v-show="showMobileMenu" class="md:hidden">
            <div class="pt-2 pb-3 space-y-1">
              <Link
                :href="route('dashboard')"
                :class="[
                  $page.url === '/dashboard' 
                    ? 'bg-blue-50 border-blue-500 text-blue-700' 
                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                  'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
                ]"
                @click="showMobileMenu = false"
              >
                Dashboard
              </Link>
              <Link
                :href="route('transactions.index')"
                :class="[
                  $page.url.startsWith('/transactions') 
                    ? 'bg-blue-50 border-blue-500 text-blue-700' 
                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                  'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
                ]"
                @click="showMobileMenu = false"
              >
                Transactions
              </Link>
              <Link
                :href="route('categories.index')"
                :class="[
                  $page.url.startsWith('/categories') 
                    ? 'bg-blue-50 border-blue-500 text-blue-700' 
                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                  'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
                ]"
                @click="showMobileMenu = false"
              >
                Categories
              </Link>
              <Link
                :href="route('budgets.index')"
                :class="[
                  $page.url.startsWith('/budgets') 
                    ? 'bg-blue-50 border-blue-500 text-blue-700' 
                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                  'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
                ]"
                @click="showMobileMenu = false"
              >
                Budgets
              </Link>
              <Link
                :href="route('goals.index')"
                :class="[
                  $page.url.startsWith('/goals') 
                    ? 'bg-blue-50 border-blue-500 text-blue-700' 
                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                  'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
                ]"
                @click="showMobileMenu = false"
              >
                Goals
              </Link>
              <Link
                :href="route('insights.index')"
                :class="[
                  $page.url.startsWith('/insights') 
                    ? 'bg-blue-50 border-blue-500 text-blue-700' 
                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                  'block pl-3 pr-4 py-2 border-l-4 text-base font-medium'
                ]"
                @click="showMobileMenu = false"
              >
                Insights
              </Link>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
              <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white font-medium text-sm">
                      {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </div>
                <div class="ml-3">
                  <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
                  <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
                </div>
              </div>
              <div class="mt-3 space-y-1">
                <Link
                  :href="route('profile.edit')"
                  class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
                  @click="showMobileMenu = false"
                >
                  Your Profile
                </Link>
                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
                >
                  Sign out
                </Link>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  HomeIcon,
  CurrencyDollarIcon,
  TagIcon,
  ChartBarIcon,
  FlagIcon,
  CpuChipIcon,
  Bars3Icon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const showUserMenu = ref(false)
const showMobileMenu = ref(false)

// Close menus when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative') && !event.target.closest('button')) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

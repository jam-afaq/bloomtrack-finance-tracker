<script setup>
import { ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import { UserIcon, LockClosedIcon, BellIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
})

const user = usePage().props.auth.user
const activeTab = ref('profile')

const tabs = [
    { id: 'profile', name: 'Profile Information', icon: UserIcon },
    { id: 'password', name: 'Password', icon: LockClosedIcon },
    { id: 'notifications', name: 'Notifications', icon: BellIcon },
    { id: 'security', name: 'Security', icon: ShieldCheckIcon },
]
</script>

<template>
    <Head title="Profile Settings" />

    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Profile Settings
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage your account settings and preferences
                    </p>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-32"></div>
                <div class="px-6 pb-6">
                    <div class="flex items-end -mt-16 mb-6">
                        <div class="h-32 w-32 rounded-full bg-white p-2 shadow-lg">
                            <div class="h-full w-full rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-bold text-4xl">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-6 mb-2">
                            <h3 class="text-2xl font-bold text-gray-900">{{ user.name }}</h3>
                            <p class="text-gray-600">{{ user.email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white shadow rounded-lg">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                            ]"
                        >
                            <component
                                :is="tab.icon"
                                :class="[
                                    activeTab === tab.id ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500',
                                    '-ml-0.5 mr-2 h-5 w-5'
                                ]"
                            />
                            {{ tab.name }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Profile Information Tab -->
                    <div v-show="activeTab === 'profile'" class="max-w-3xl">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>

                    <!-- Password Tab -->
                    <div v-show="activeTab === 'password'" class="max-w-3xl">
                        <UpdatePasswordForm />
                    </div>

                    <!-- Notifications Tab -->
                    <div v-show="activeTab === 'notifications'" class="max-w-3xl">
                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    Notification Preferences
                                </h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Manage how you receive notifications about your financial activities.
                                </p>
                            </header>

                            <div class="mt-6 space-y-6">
                                <!-- Email Notifications -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="email-notifications"
                                            type="checkbox"
                                            checked
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <label for="email-notifications" class="font-medium text-gray-700">
                                            Email Notifications
                                        </label>
                                        <p class="text-sm text-gray-500">
                                            Receive email notifications about transactions and budget alerts.
                                        </p>
                                    </div>
                                </div>

                                <!-- Budget Alerts -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="budget-alerts"
                                            type="checkbox"
                                            checked
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <label for="budget-alerts" class="font-medium text-gray-700">
                                            Budget Alerts
                                        </label>
                                        <p class="text-sm text-gray-500">
                                            Get notified when you're approaching your budget limits.
                                        </p>
                                    </div>
                                </div>

                                <!-- Goal Updates -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="goal-updates"
                                            type="checkbox"
                                            checked
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <label for="goal-updates" class="font-medium text-gray-700">
                                            Goal Updates
                                        </label>
                                        <p class="text-sm text-gray-500">
                                            Receive updates about your financial goal progress.
                                        </p>
                                    </div>
                                </div>

                                <!-- AI Insights -->
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input
                                            id="ai-insights"
                                            type="checkbox"
                                            checked
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <label for="ai-insights" class="font-medium text-gray-700">
                                            AI Insights
                                        </label>
                                        <p class="text-sm text-gray-500">
                                            Get personalized financial insights and recommendations.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 pt-4">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                                    >
                                        Save Preferences
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Security Tab -->
                    <div v-show="activeTab === 'security'" class="max-w-3xl space-y-8">
                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    Two-Factor Authentication
                                </h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Add an extra layer of security to your account.
                                </p>
                            </header>

                            <div class="mt-6">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Two-Factor Authentication</p>
                                            <p class="text-sm text-gray-500">Not enabled</p>
                                        </div>
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            Enable
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    Active Sessions
                                </h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Manage and log out your active sessions on other browsers and devices.
                                </p>
                            </header>

                            <div class="mt-6 space-y-4">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">Current Device</p>
                                                <p class="text-sm text-gray-500">Mac - Chrome - Active now</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <DeleteUserForm />
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

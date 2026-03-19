<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'

/**
 * 🔒 Type safety (biar gak any)
 */
interface LogItem {
    id: number
    status: 'sent' | 'failed' | 'pending'
    created_at: string

    // dari relasi recipient (opsional)
    name?: string
    email?: string
    phone?: string
}

interface PaginatedLogs {
    data: LogItem[]
    links: {
        url: string | null
        label: string
        active: boolean
    }[]
    current_page: number
    per_page: number
}

const props = defineProps<{
    logs: PaginatedLogs
}>()

const breadcrumbs = [
    { title: 'Log', href: '#' },
]

/**
 * 🔁 Pagination handler
 */
const goToPage = (url: string | null) => {
    if (!url) return
    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    })
}

/**
 * 🔁 Retry handler (dummy, nanti sambung ke API)
 */
const retry = (id: number) => {
    console.log('Retry ID:', id)

    // contoh kalau mau pakai POST:
    // router.post(`/logs/retry/${id}`)
}
</script>

<template>
    <Head title="Log Pengiriman" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full mx-auto rounded-2xl shadow p-8 space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-100">
                    Log Pengiriman
                </h2>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    
                    <!-- Head -->
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-xs text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-xs text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-xs text-gray-500 uppercase text-end">Status</th>
                        </tr>
                    </thead>

                    <!-- Body -->
                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                        
                        <!-- Empty -->
                        <tr v-if="logs.data.length === 0">
                            <td colspan="5" class="text-center py-6 text-gray-500">
                                Tidak ada data
                            </td>
                        </tr>

                        <!-- Data -->
                        <tr v-for="(item, index) in logs.data" :key="item.id">

                            <!-- No -->
                            <td class="px-6 py-4 text-sm text-center">
                                {{
                                    (logs.current_page - 1) * logs.per_page + index + 1
                                }}
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 text-sm text-center">
                                {{ item.email ?? '-' }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-end text-sm">

                                <span
                                    class="px-3 py-1 rounded text-xs font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-700': item.status === 'sent',
                                        'bg-red-100 text-red-700': item.status === 'failed',
                                        'bg-yellow-100 text-yellow-700': item.status === 'pending',
                                    }"
                                >
                                    {{ item.status }}
                                </span>

                                <!-- Retry -->
                                <button
                                    v-if="item.status === 'failed'"
                                    @click="retry(item.id)"
                                    class="ml-3 px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600"
                                >
                                    Retry
                                </button>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-end mt-4 space-x-1">
                <button
                    v-for="(link, i) in logs.links"
                    :key="i"
                    v-html="link.label"
                    @click="goToPage(link.url)"
                    :disabled="!link.url"
                    class="px-3 py-1 text-sm rounded border"
                    :class="{
                        'bg-blue-500 text-white': link.active,
                        'text-gray-500 cursor-not-allowed': !link.url
                    }"
                />
            </div>

        </div>
    </AppLayout>
</template>
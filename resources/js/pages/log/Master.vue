<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const userPermissions = computed<string[]>(() => {
    return (page.props.auth?.permissions as string[]) ?? []
})

const can = (permission: string) => {
    return userPermissions.value.includes(permission)
}

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
    sentCount?: number
    failedCount?: number
    pendingCount?: number
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
    logs: PaginatedLogs,
    sentCount: number,
    pendingCount: number,
    failedCount: number
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
        <div v-if="!can('logs.view')" class="flex items-center justify-center h-96 text-muted-foreground">
            You don't have permission to access this page.
        </div>

        <div v-else class="dashboard-card">

            <!-- Header -->
            <div class="table-header">

                <div>

                    <h2>Delivery Logs</h2>

                    <span>
                        Monitor all email delivery activity.
                    </span>

                </div>

                <div class="header-decoration flex  gap-5 mt-5">

                    <div class="summary-item success">

                        <span>{{ sentCount }}</span>

                        Sent

                    </div>

                    <div class="summary-item warning">

                        <span>{{ pendingCount }}</span>

                        Pending

                    </div>

                    <div class="summary-item danger">

                        <span>{{ failedCount }}</span>

                        Failed

                    </div>

                </div>

            </div>

            <!-- Table -->
            <div class="dashboard-table">

                <table class="w-full text-center">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Email</th>

                            <th>Status</th>

                            <th class="text-center">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-if="logs.data.length === 0">

                            <td colspan="4" class="empty-state">

                                No delivery logs found

                            </td>

                        </tr>

                        <tr v-for="(item, index) in logs.data" :key="item.id">

                            <td>

                                {{ (logs.current_page - 1) * logs.per_page + index + 1 }}

                            </td>

                            <td>

                                <div class="email-cell">

                                    <div class="avatar">

                                        {{ item.email?.charAt(0).toUpperCase() }}

                                    </div>

                                    <div>

                                        <strong>

                                            {{ item.email }}

                                        </strong>

                                    </div>

                                </div>

                            </td>

                            <td>

                                <span class="status" :class="item.status">

                                    {{ item.status }}

                                </span>

                            </td>

                            <td class="text-center">

                                <button @click="retry(item.id)" class="btn-outline">

                                    Retry

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->

            <div class="pagination-wrapper">

                <Link v-for="(link, i) in logs.links" :key="i" :href="link.url || ''" preserve-scroll preserve-state
                    class="pagination-button" :class="{

                        active: link.active,

                        disabled: !link.url

                    }" v-html="link.label" />

            </div>

        </div>
    </AppLayout>
</template>
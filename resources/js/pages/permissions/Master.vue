<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Loading from '@/components/Loading.vue'
import type { BreadcrumbItem } from '@/types'
import permissionsRoute from '@/routes/permissions'
import { Plus } from 'lucide-vue-next'
import { computed } from 'vue'

const page = usePage()


/* =========================
Props
========================= */
const props = defineProps<{
    permissions: { id: number; name: string }[]
}>()
/* =========================
Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permission Management',
        href: permissionsRoute.index().url
    }
]

const permissionList = computed(() => props.permissions)

const permissions = computed<string[]>(() => {
    return (page.props.auth?.permissions as string[]) ?? []
})

const can = (permission: string) => {
    return permissions.value.includes(permission)
}

/* =========================
State
========================= */
const isLoading = ref(false)

const form = ref({
    name: ''
})

const successModal = ref({
    show: false,
    message: ''
})

/* =========================
Flash handler
========================= */

watch(() => page.props.flash?.success, (msg: any) => {
    if (msg) {
        successModal.value = {
            show: true,
            message: msg
        }
    }
})

/* =========================
Actions
========================= */
const submitForm = () => {
    isLoading.value = true

    router.post(permissionsRoute.store().url, form.value, {
        onSuccess: () => {
            form.value.name = ''
        },
        onFinish: () => {
            isLoading.value = false
        }
    })
}

const deletePermission = (id: number) => {
    if (!confirm('Yakin hapus permission?')) return

    isLoading.value = true

    router.delete(permissionsRoute.destroy(id).url, {
        onFinish: () => {
            isLoading.value = false
        }
    })
}

const closeSuccessModal = () => {
    successModal.value.show = false
}
</script>

<template>

    <Head title="Permission Management" />


    <AppLayout :breadcrumbs="breadcrumbs">

        <div v-if="!can('permissions.view')" class="flex items-center justify-center h-96 text-muted-foreground">
            You don't have permission to access this page.
        </div>

        <!-- CONTENT -->
        <div v-else class="p-6 space-y-6">
            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6">

                <div class="space-y-2">

                    <h1 class="text-2xl font-semibold">
                        Permission Management
                    </h1>

                    <p class="text-sm text-gray-500">
                        Manage permission sistem
                    </p>

                </div>

                <div class="flex gap-3">
                    <Link v-if="can('permissions.create')" :href="permissionsRoute.create().url"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-black shadow">
                        <Plus class="h-5 w-5" />
                        Add Permission
                    </Link>
                </div>
            </div>


            <!-- TABLE -->
            <div class="dashboard-table">

                <table class="dashboard-data-table">

                    <thead>

                        <tr>

                            <th>
                                Permission
                            </th>

                            <th class="w-40 text-center">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-for="perm in props.permissions" :key="perm.id">

                            <!-- Permission -->
                            <td>

                                <div class="table-user">

                                    <div class="table-avatar">

                                        {{ perm.name?.charAt(0).toUpperCase() || '?' }}

                                    </div>

                                    <div>

                                        <div class="table-title">

                                            {{ perm.name }}

                                        </div>

                                        <div class="table-subtitle">

                                            System Permission

                                        </div>

                                    </div>

                                </div>

                            </td>

                            <!-- Action -->
                            <td>

                                <div class="table-actions">
                                    <Link v-if="can('permissions.edit')" :href="permissionsRoute.edit(perm.id).url"
                                        class="btn-outline">
                                        Edit
                                    </Link>
                                    <button v-if="can('permissions.delete')" @click="deletePermission(perm.id)"
                                        class="btn-danger">

                                        Delete
                                    </button>

                                </div>

                            </td>

                        </tr>

                        <tr v-if="!permissions.length">

                            <td colspan="2" class="empty-state">

                                No permissions found

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </AppLayout>
</template>
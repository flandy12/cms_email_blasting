<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import blasting from '@/routes/blasting';
import type { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import roles from '@/routes/roles'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

const destroy = (id: number) => {
    if (!confirm('Delete this role?')) return

    router.delete(roles.destroy(id).url, {
        preserveScroll: true,
    })
}

const page = usePage()

const permissions = computed<string[]>(() => {
    return (page.props.auth?.permissions as string[]) ?? []
})

const can = (permission: string) => {
    return permissions.value.includes(permission)
}


/* =========================
Breadcrumb
========================= */

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: blasting.campaigns.index().url
    }
];

/* =========================
Props
========================= */
const props = defineProps<{
    roles: any[]
}>()

/* =========================
State
========================= */
const rolesData = ref(props.roles ?? [])
const loading = ref(false)

/* =========================
Modal State
========================= */
const showModal = ref(false)
const selectedRole = ref<any>(null)

/* =========================
Actions
========================= */
const openCreate = () => {
    selectedRole.value = null
    showModal.value = true
}

const openEdit = (role: any) => {
    selectedRole.value = role
    showModal.value = true
}


</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="!can('roles.view')" class="flex items-center justify-center h-96 text-muted-foreground">
            You don't have permission to access this page.
        </div>

        <div v-else class="p-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6 ">

                <div class="space-y-2">

                    <h1 class="text-2xl font-semibold">
                        Roles Menagement
                    </h1>

                    <p class="text-sm text-gray-500">
                        Manage roles
                    </p>

                </div>

                <div class="flex gap-3">
                    <Link v-if="can('roles.create')" :href="roles.create().url"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-black shadow">
                        <Plus class="h-5 w-5" />
                        Add Role
                    </Link>
                </div>
            </div>

            <div v-if="loading">Loading...</div>

            <div class="dashboard-table">

                <table class="dashboard-data-table">

                    <thead>

                        <tr>

                            <th class="w-[30%]">
                                Role
                            </th>

                            <th>
                                Permissions
                            </th>

                            <th class="w-40 text-center">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-for="role in rolesData" :key="role.id">

                            <!-- Role -->
                            <td>

                                <div class="table-user">

                                    <div class="table-avatar">

                                        {{ role.name.charAt(0).toUpperCase() }}

                                    </div>

                                    <div>

                                        <div class="table-title">

                                            {{ role.name }}

                                        </div>

                                        <div class="table-subtitle">

                                            {{ role.permissions.length }} Permission(s)

                                        </div>

                                    </div>

                                </div>

                            </td>

                            <!-- Permissions -->
                            <td>

                                <div class="flex flex-wrap gap-2">

                                    <span v-for="perm in role.permissions" :key="perm.id" class="status finished">

                                        {{ perm.name }}

                                    </span>

                                </div>

                            </td>

                            <!-- Action -->
                            <td>

                                <div class="table-actions">

                                    <Link v-if="can('roles.edit')" :href="roles.edit(role.id)" class="btn-outline">

                                        Edit

                                    </Link>

                                    <button v-if="can('roles.delete')" @click="destroy(role.id)" class="btn-danger">
                                        Delete
                                    </button>

                                </div>

                            </td>

                        </tr>

                        <tr v-if="!rolesData.length">

                            <td colspan="3" class="empty-state">

                                No roles available

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>
        </div>
    </AppLayout>
</template>
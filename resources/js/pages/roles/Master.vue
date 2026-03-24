<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import blasting from '@/routes/blasting';
import type { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import roles from '@/routes/roles'


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
        <div class="p-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6 px-5">

                <div class="space-y-2">

                    <h1 class="text-2xl font-semibold">
                        Roles Menagement
                    </h1>

                    <p class="text-sm text-gray-500">
                        Manage roles
                    </p>

                </div>

                <div class="flex gap-3">
                    <Link :href="roles.create().url"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-black shadow">
                    <Plus class="h-5 w-5" />
                    Add Role
                    </Link>
                </div>
            </div>

            <div v-if="loading">Loading...</div>

            <div class="bg-white dark:bg-gray-700/50 shadow rounded-2xl overflow-hidden mx-5">

                <table class="w-full">

                    <thead class="bg-gray-50  dark:bg-gray-600/50  border-b">

                        <tr>
                            <th class="p-2 text-left">Name</th>
                            <th class="p-2 text-left">Permissions</th>
                            <th class="p-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in rolesData" :key="role.id" class="border-t">
                            <td class="p-2">{{ role.name }}</td>
                            <td class="p-2">
                                <span v-for="perm in role.permissions" :key="perm.id"
                                    class="bg-gray-200 px-2 py-1 text-xs rounded mr-1">
                                    {{ perm.name }}
                                </span>
                            </td>
                            <td class="p-2 text-center space-x-2">
                                <button @click="openEdit(role)" class="text-blue-500">Edit</button>
                            </td>
                        </tr>
                        <tr v-if="!rolesData.length">

                            <td colspan="3" class="text-center py-14 text-gray-400">

                                No roles available

                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
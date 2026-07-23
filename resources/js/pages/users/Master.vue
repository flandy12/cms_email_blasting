<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import users from '@/routes/users';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

const destroy = (id: number) => {
    if (!confirm('Delete this user?')) return

    router.delete(users.destroy(id).url, {
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


const props = defineProps<{
    userData: {
        data: User[]
        links: any[]
        meta: any
    }
}>();

const Users = props.userData.data;

// 🔹 Breadcrumb
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: users,
    },
]

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="!can('users.view')" class="flex items-center justify-center h-96 text-muted-foreground">
            You don't have permission to access this page.
        </div>

        <div v-else class="p-6 space-y-6">
            <div>

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-6">
                    <div class="">
                        <h1 class="text-2xl font-bold">Users Menagement</h1>

                        <p class="text-gray-500 text-sm">
                            Manage your user
                        </p>
                    </div>
                    <!-- ADD -->
                    <Link v-if="can('users.create')" :href="users.create().url"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-black shadow hover:opacity-90 transition">
                        <Plus class="h-5 w-5" />
                        Add Users
                    </Link>

                </div>

                <!-- TABLE -->
                <div class="dashboard-table">

                    <table class="dashboard-data-table">

                        <thead>

                            <tr>

                                <th class="w-20">
                                    #
                                </th>

                                <th class="w-[35%]">
                                    User
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Joined
                                </th>

                                <th class="text-center w-56">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr v-for="(user, index) in Users" :key="user.id">

                                <td>

                                    {{ index + 1 }}

                                </td>

                                <td>

                                    <div class="table-user">

                                        <div class="table-avatar">

                                            {{ user.name.charAt(0).toUpperCase() }}

                                        </div>

                                        <div>

                                            <div class="table-title">

                                                {{ user.name }}

                                            </div>

                                            <div class="table-subtitle">

                                                User Account

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td class="text-muted">

                                    {{ user.email }}

                                </td>

                                <td class="text-muted">

                                    {{ user.created_at }}

                                </td>

                                <td>

                                    <div class="table-actions">

                                        <Link  v-if="can('users.edit')" :href="users.edit(user.id).url" class="btn-outline">

                                            Edit

                                        </Link>

                                        <Link  v-if="can('users.delete')"  @click="destroy(user.id)" class="btn-danger">

                                            Delete

                                        </Link>

                                    </div>

                                </td>

                            </tr>

                            <tr v-if="Users.length === 0">

                                <td colspan="5" class="empty-state">

                                    No users found

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


            </div>
        </div>
    </AppLayout>
</template>
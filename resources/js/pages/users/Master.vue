<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import users from '@/routes/users';
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';


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
        <div class="p-6 space-y-6">

            <!-- 🔹 HEADER -->
            <div class="">
                <h1 class="text-2xl font-bold">Users Menagement</h1>

                <p class="text-gray-500 text-sm">
                    Manage your user
                </p>
            </div>

            <div class="p-6">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Users</h1>
                    <!-- ADD -->
                <Link :href="users.create().url"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-black shadow hover:opacity-90 transition">
                        <Plus class="h-5 w-5" />
                        Add Users
                    </Link>

                </div>

                <!-- TABLE -->
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-4">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">

                            <thead class="border-b text-left">
                                <tr>
                                    <th class="py-2">No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(user, index) in Users" :key="user.id"
                                    class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">

                                    <td class="py-2">
                                        {{ index + 1 }}
                                    </td>

                                    <td class="font-medium">
                                        {{ user.name }}
                                    </td>

                                    <td>
                                        {{ user.email }}
                                    </td>

                                    <td>
                                        {{ user.created_at }}
                                    </td>

                                    <td class="px-6 py-4 text-center">

                                        <Link :href="users.edit(user.id).url"
                                            class="px-4 py-2 rounded-lg bg-primary text-black hover:opacity-90 transition">
                                            Edit
                                        </Link>

                                    </td>

                                </tr>

                                <!-- EMPTY STATE -->
                                <tr v-if="Users.length === 0">
                                    <td colspan="4" class="text-center py-4 text-gray-400">
                                        No users found
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </div>
    </AppLayout>
</template>
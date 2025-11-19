<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { email } from '@/routes';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    templates: {
        data: Array<{
            id: number;
            name: string;
            created_at: string;
        }>;
        links: any[];
    };
}>();


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Template',
        href: email().url,
    },
];

const template = ref(props.templates.data);

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="w-full divide-yw-full divide-gray-200 dark:divide-neutral-700">
                            <thead class="">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                        No</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                        Template Name</th>

                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                        Action</th>

                                  
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                <tr v-for="value in template" :key="value.id">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{ value.id }}</td>

                                    <td class="px-6 py-4 text-sm text-center font-medium text-gray-800 dark:text-neutral-200">
                                        {{ value.name }}</td>
                                    <td class="px-6 py-4 text-center text-sm font-medium space-x-3">
                                        <Link :href="`/template/1/show`">
                                        <button type="button"
                                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white-600 hover:text-white-800 focus:outline-hidden focus:text-white-800 disabled:opacity-50 disabled:pointer-events-none px-5 py-2 text-black bg-primary cursor-pointer">Edit</button>
                                        </Link>
                                        <button type="button"
                                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-danger px-5 py-2 text-black disabled:pointer-events-none cursor-pointer">Delete</button>
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
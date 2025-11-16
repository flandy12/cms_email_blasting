<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { email } from '@/routes';
import { Head } from '@inertiajs/vue3';
import { Upload } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Log',
        href: email().url,
    },
];

// Ref untuk input file
const fileInput = ref<HTMLInputElement | null>(null);

// Fungsi klik tombol import
const handleImport = () => {
    fileInput.value?.click(); // memicu klik input file
};

// Fungsi ketika file dipilih
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        console.log('File dipilih:', file.name);
        // TODO: Kirim file ke server via Inertia atau API
    }
};
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full mx-auto rounded-2xl shadow p-8 space-y-6">
            <!-- Header + Tombol Import -->
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-100">
                    Log Pengiriman
                </h2>
            </div>

            <!-- Tabel -->
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-800">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase ">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase ">
                                        Name</th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase ">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase ">
                                        Phone</th>
                                    <th
                                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase ">
                                        Status Pengiriman</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                <tr>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        1</td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        John Brown</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        john@site.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        088921113833</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium space-x-3">
                                        <button type="button"
                                            class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-danger px-5 py-2 text-black disabled:pointer-events-none cursor-pointer">Retry</button>
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

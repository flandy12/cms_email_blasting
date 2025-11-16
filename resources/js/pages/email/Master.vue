<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { email } from '@/routes';
import { Head } from '@inertiajs/vue3';
import { DeleteIcon, LoaderCircleIcon, PlayIcon, PlaySquareIcon, StarIcon, Trash2Icon, Upload } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    jobs: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            phone: string;
            status: string;
        }>;
        links: any[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Email',
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
        const formData = new FormData();
        formData.append('file', file);

        isLoading.value = true

        router.post('/email/import', formData, {
            onSuccess: () => {
                isLoading.value = false;
                window.location.href = '/email'
                console.log("Import berhasil");
            }
        });
    }
};

const dataEmail = ref(props.jobs.data);
const pagination = ref(props.jobs.links);
const total = ref(props.jobs.total);
const isLoading = ref(false)

const daleteDataAll = () => {
    if (!confirm("Yakin hapus semua data?")) return

    isLoading.value = true

    router.delete('/email/delete', {
        onFinish: () => {
            isLoading.value = false;
            window.location.href = '/email'
        }
    })
}

const search = ref('');

// hasil filter otomatis berubah ketika search berubah
// filtering
const filteredItems = computed(() => {
    if (!search.value) return dataEmail.value;

    return dataEmail.value.filter(item =>
        item.name?.toLowerCase().includes(search.value.toLowerCase()) ||
        item.email?.toLowerCase().includes(search.value.toLowerCase()) ||
        item.phone?.toLowerCase().includes(search.value.toLowerCase())
    );
});

</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="flex flex-col items-center space-y-3 ">

                <svg aria-hidden="true" class="w-10 h-10 text-white animate-spin fill-blue-500" viewBox="0 0 100 101">
                    <path d="M100 50.5908C100 78.2051..." />
                    <path d="M93.9676 39.0409C96.393..." />
                </svg>
                <p class="text-white text-sm font-medium">Processing...</p>
            </div>
        </div>

        <div class="w-full mx-auto rounded-2xl shadow p-8 space-y-6">
            <!-- Header + Tombol Import -->
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-100">
                    Master Email
                </h2>

                <div class="flex space-x-5">
                    <div>
                        <button type="button" @click="handleImport"
                            class="inline-flex items-center text-black gap-x-2 px-4 py-2 rounded-lg bg-primary cursor-pointer">
                            <Upload class="w-4 h-4" />
                            <span>Import Excel</span>
                        </button>

                        <!-- Input file tersembunyi -->
                        <input ref="fileInput" type="file" accept=".xlsx, .xls" class="hidden"
                            @change="handleFileChange" />
                    </div>

                    <div>
                        <button type="button" @click="handleImport"
                            class="inline-flex items-center gap-x-2 text-black px-4 py-2 rounded-lg bg-seccondary cursor-pointer">
                            <PlayIcon class="w-4 h-4" />
                            <span>Start Blast</span>
                        </button>

                        <!-- Input file tersembunyi -->
                        <input ref="fileInput" type="file" accept=".xlsx, .xls" class="hidden"
                            @change="handleFileChange" />
                    </div>

                    <div>
                        <button type="button" @click="daleteDataAll"
                            class="inline-flex items-center text-black gap-x-2 px-4 py-2 rounded-lg bg-danger cursor-pointer">
                            <Trash2Icon class="w-4 h-4" />
                            <span>Delete All</span>
                        </button>

                        <!-- Input file tersembunyi -->
                        <input ref="fileInput" type="file" accept=".xlsx, .xls" class="hidden"
                            @change="handleFileChange" />
                    </div>
                </div>
            </div>

            <div class="flex justify-between w-full">
                <p>Total : <span class="font-bold">{{ total }}</span> User</p>

                <div class="">
                    <label for="search" class="block mb-2.5 text-sm font-medium text-heading sr-only ">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" id="search" v-model="search"
                            class="block w-full p-3 ps-9 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                            placeholder="Search..." />
                    </div>
                </div>

            </div>

            <!-- Tabel -->
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-800">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        Name</th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        Phone</th>
                                    <th
                                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                <tr v-for="(value, key) in filteredItems" :key="key">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{ key + 1 }}</td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{ value?.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{ value?.email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{ value?.phone ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                        {{ value?.status }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->

            <nav aria-label="Page navigation example" v-if="dataEmail.length > 0" class="mx-auto mt-6">
                <ul class="flex -space-x-px justify-end text-sm mt-5">
                    <li v-for="(value, key) in pagination" :key="key">
                        <a :href="value.url ?? '#'" :class="[
                            'flex items-center justify-center text-sm h-9 px-3 border box-border font-medium focus:outline-none',
                            value.active
                                ? 'bg-primary text-black border-blue-600'
                                : 'bg-neutral-secondary-medium text-body border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading',
                            key === 0 ? 'rounded-s-base' : '',
                            key === pagination.length - 1 ? 'rounded-e-base' : '',
                            !value.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]" v-html="value.label" />
                    </li>
                </ul>
            </nav>

        </div>
    </AppLayout>
</template>

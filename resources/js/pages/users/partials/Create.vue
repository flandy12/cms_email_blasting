<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import type { BreadcrumbItem } from '@/types'
import Message from '@/components/Message.vue'
import Loading from '@/components/Loading.vue'
import users from '@/routes/users'

/* =========================
  Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'users', href: users.index().url },
]

/* =========================
  State
========================= */
const isLoading = ref(false)
const errors = ref<Record<string, string>>({})

const successModal = ref({
    show: false,
    message: '',
})

const closeSuccessModal = () => {
    successModal.value.show = false
}

/* =========================
  Form State
========================= */
const form = ref({
    name: '',
    email: '',
})


/* =========================
  Submit
========================= */
const submitForm = () => {
    isLoading.value = true
    errors.value = {}

    const payload = {
        name: form.value.name,
        email: form.value.email,
    }

    router.post(
        users.store().url,
        payload,
        {
            preserveScroll: true,
            onFinish: () => (isLoading.value = false),

            onSuccess: () => {
                successModal.value = {
                    show: true,
                    message: 'Template berhasil disimpan'
                }
            },

            onError: (err: any) => {
                Object.keys(err).forEach(key => {
                    errors.value[key] = err[key]
                })
            },
        }
    )
}

</script>

<template>

    <Head title="Create Template" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <Loading />
        </div>

        <!-- Success Modal -->
        <div v-if="successModal.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-green-600 text-2xl">✓</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Berhasil</h3>
                <p class="text-sm text-gray-600 mb-6">
                    {{ successModal.message }}
                </p>
                <button @click="closeSuccessModal"
                    class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                    OK
                </button>
            </div>
        </div>

        <div class="w-full rounded-2xl shadow p-8 space-y-6">
            <h2 class="text-xl font-semibold">Create New Users</h2>
            <p class="text-sm text-gray-500">
                Buat User
            </p>

            <form @submit.prevent="submitForm" class="space-y-6">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input v-model="form.name" type="text" class="w-full mt-2 border rounded-lg px-4 py-2" />
                    <p v-if="errors.name" class="text-red-500 text-sm mt-1">
                        {{ errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input v-model="form.email" type="text" class="w-full mt-2 border rounded-lg px-4 py-2" />
                    <p v-if="errors.subject" class="text-red-500 text-sm">
                        {{ errors.subject }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-5">
                    <Link :href="users.index().url"
                        class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-lg hover:bg-gray-400">
                        Cancel
                    </Link>

                    <button type="submit"
                        class="px-4 py-2 bg-primary text-black font-semibold rounded-lg hover:bg-green-700">
                        Save Users
                    </button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ccc;
    border-radius: 0.5rem;
    transition: 0.2s;
}

.btn:hover {
    background: #f3f4f6;
}
</style>

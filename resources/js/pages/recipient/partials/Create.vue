<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItem } from '@/types'
import blasting from '@/routes/blasting'

/* =========================
   Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Recipients', href: blasting.recipients.index().url },
    { title: 'Add Recipient', href: '#' },
]

/* =========================
   FORM STATE
========================= */
const form = ref({
    email: '',
    name: '',
    is_active: true,
    metadata: {} as Record<string, string>,
})

const errors = ref<Record<string, string[]>>({})
const isLoading = ref(false)

/* =========================
   METADATA HANDLER
========================= */

const metaKey = ref('')
const metaValue = ref('')

const addMeta = () => {
    const key = metaKey.value.trim()
    const value = metaValue.value.trim()

    if (!key || !value) return

    form.value.metadata[key] = value

    metaKey.value = ''
    metaValue.value = ''
}

const removeMeta = (key: string) => {
    delete form.value.metadata[key]
}

/* =========================
   SUBMIT
========================= */
const submit = () => {
    if (isLoading.value) return

    isLoading.value = true
    errors.value = {}

    router.post(
        blasting.recipients.store().url,
        {
            email: form.value.email,
            name: form.value.name || null,
            is_active: form.value.is_active,
            metadata: form.value.metadata,
        },
        {
            onFinish: () => {
                isLoading.value = false
            },
            onError: (err) => {
                errors.value = err
                isLoading.value = false
            },
        }
    )
}
</script>

<template>

    <Head title="Add Recipient" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full mx-auto  rounded-xl shadow p-6 space-y-6">
            <h2 class="text-lg font-semibold">Add Recipient</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- EMAIL -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium">Email</label>
                    <input v-model="form.email" type="email" class="input" placeholder="user@email.com" />
                    <p v-if="errors.email" class="error">
                        {{ errors.email }}
                    </p>
                </div>

                <!-- NAME -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium">Name (optional)</label>
                    <input v-model="form.name
                        " type="text" class="input" placeholder="John Doe" />
                    <p v-if="errors.name" class="error">
                        {{ errors.name }}
                    </p>
                </div>

                <!-- ACTIVE -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.is_active" id="is_active" />
                    <label for="is_active" class="text-sm">
                        Active recipient
                    </label>
                </div>

                <!-- METADATA -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium">Metadata (optional)</label>

                    <div class="flex gap-2">
                        <input v-model="metaKey" placeholder="key (e.g. city)" class="input" />
                        <input v-model="metaValue" placeholder="value (e.g. Jakarta)" class="input" />
                        <button type="button" @click="addMeta()" class="px-3 py-1 bg-primary text-black rounded">
                            +
                        </button>
                    </div>

                    <div v-if="Object.keys(form.metadata).length" class="space-y-1">
                        <div v-for="(value, key) in form.metadata" :key="key"
                            class="flex justify-between  px-3 py-1 rounded">
                            <span class="text-sm">{{ key }}: {{ value }}</span>
                            <button type="button" @click="removeMeta(key)" class="text-red-600 text-xs">
                                remove
                            </button>
                        </div>
                    </div>

                    <p v-if="errors.metadata" class="error">
                        {{ errors.metadata }}
                    </p>
                    <label>Data Meta</label>
                    <pre class="bg-gray-100 text-gray-800 p-2 text-xs rounded">

{{ JSON.stringify(form.metadata, null, 2) }}
</pre>


                </div>

                <!-- ACTION -->
                <div class="text-right gap-5 flex justify-end">
                    <button type="button" @click="router.visit(blasting.recipients.index().url)"
                        class="px-4 py-2 cursor-pointer bg-gray-500 text-white rounded-lg disabled:opacity-60">
                        Batal
                    </button>
                    <button type="submit" :disabled="isLoading"
                        class="px-4 py-2 bg-primary text-black rounded-lg disabled:opacity-60">
                        Save Recipient
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.input {
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 0.5rem;
    padding: 0.5rem;
}

.error {
    font-size: 0.875rem;
    color: red;
}
</style>

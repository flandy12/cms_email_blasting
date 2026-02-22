<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItem } from '@/types'
import campaigns from '@/routes/blasting/campaigns'

const props = defineProps<{
    templates: Array<{
        id: number
        name: string
    }>
}>()

/* =========================
   Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Campaign', href: campaigns.index() },
    { title: 'Create Campaign', href: '#' },
]

/* =========================
   FORM (MATCH DB 100%)
========================= */
const form = ref({
    template_id: '',
    name: '',
    schedule_at: '',
})

const errors = ref<Record<string, string[]>>({})
const isLoading = ref(false)

/* =========================
   SUBMIT
========================= */
const submit = () => {
    isLoading.value = true
    errors.value = {}

    router.post(
        campaigns.store().url,
        {
            template_id: form.value.template_id,
            name: form.value.name,
            schedule_at: form.value.schedule_at || null,
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

    <Head title="Create Campaign" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <Loading />
        </div>

        <div class="w-full mx-auto rounded-xl shadow p-6 space-y-6">
            <h2 class="text-lg font-semibold">Create Blasting Campaign</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- TEMPLATE -->
                <div>
                    <label class="block text-sm font-medium">Template</label>
                    <select v-model="form.template_id" class="input text-black bg-white">
                        <option value="" class="text-gray-500">-- Select Template --</option>
                        <option class="text-black bg-white" v-for="t in props.templates" :key="t.id" :value="t.id">
                            {{ t.name }}
                        </option>
                    </select>
                    <p v-if="errors.template_id" class="error mt-2">
                        {{ errors.template_id }}
                    </p>
                </div>

                <!-- NAME -->
                <div>
                    <label class="block text-sm font-medium">Campaign Name</label>
                    <input v-model="form.name" class="input" />
                    <p v-if="errors.name" class="error mt-2">
                        {{ errors.name }}
                    </p>
                </div>

                <!-- SCHEDULE -->
                <div class="text-white">
                    <label class="block text-sm font-medium">Schedule (optional)</label>
                    <input type="datetime-local" v-model="form.schedule_at" class="input text-white " />
                    <p v-if="errors.schedule_at" class="error mt-2">
                        {{ errors.schedule_at }}
                    </p>
                </div>

                <div class="text-right">
                    <button type="submit" class="px-4 py-2 bg-primary text-black rounded-lg">
                        Save Campaign
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

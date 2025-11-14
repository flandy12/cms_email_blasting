<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { email } from '@/routes'
import { Head } from '@inertiajs/vue3'
import { ref, computed,defineEmits  } from 'vue'
import type { BreadcrumbItem } from '@/types'
import Message from '@/components/Message.vue'

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Template', href: email().url },
]

const emit = defineEmits<{ (e: 'update:modelValue', value: string): void }>()

const onChange = (content: string) => emit('update:modelValue', content)

// Reactive form data
const form = ref({
    name: '',
    wording: '', // Konten editor akan di-bind ke sini
    to: '',
    urlType: 'static',
    url: '',
    subject: '',
    baseUrl: '',
    params: [] as { key: string; value: string }[],
})

// Tambah parameter
const addParam = () => form.value.params.push({ key: '', value: '' })

// Hapus parameter
const removeParam = (index: number) => form.value.params.splice(index, 1)

// Preview URL dinamis
const dynamicUrlPreview = computed(() => {
    let url = form.value.baseUrl?.trim()
    if (!url) return ''

    const queryParams: Record<string, string> = {}

    form.value.params.forEach(({ key, value }) => {
        if (!key || !value) return
        const pattern = new RegExp(`{${key}}`, 'g')
        if (pattern.test(url)) {
            url = url.replace(pattern, encodeURIComponent(value))
        } else {
            queryParams[key] = value
        }
    })

    const queryString = Object.entries(queryParams)
        .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
        .join('&')

    if (queryString) {
        url += (url.includes('?') ? '&' : '?') + queryString
    }

    return url
})

// Submit form
const submitForm = () => {
    const payload =
        form.value.urlType === 'static'
            ? { ...form.value, finalUrl: form.value.url }
            : { ...form.value, finalUrl: dynamicUrlPreview.value }

    console.log('Form Submitted:', payload)
    // TODO: Inertia.post('/templates', payload)
}
</script>

<template>

    <Head title="Create Template" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full mx-auto rounded-2xl shadow p-8 space-y-6">
            <div class="flex w-full flex-col lg:flex-row space-x-0 lg:space-x-6 space-y-6 lg:space-y-0">
                <!-- Form -->
                <div class="flex-9">
                    <h2 class="text-xl font-semibold">Edit Template</h2>
                    <p class="text-sm text-gray-600 mb-4">Fill in the details below to create a new email template.</p>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Template Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium mb-1">Template Name</label>
                            <input v-model="form.name" id="name" type="text" placeholder="e.g. Promo Akhir Tahun"
                                class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 px-4 py-2"
                                required />
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium mb-1">Subject</label>
                            <input v-model="form.subject" id="subject" type="text" placeholder="Title"
                                class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 px-4 py-2"
                                required />
                        </div>

                        <!-- Wording -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Wording</label>
                            <Message :content="form.wording" />
                        </div>

                        <!-- URL Type -->
                        <div>
                            <label class="block text-sm font-medium mb-2">URL Type</label>
                            <div class="flex items-center space-x-6">
                                <label class="inline-flex items-center space-x-2">
                                    <input type="radio" value="static" v-model="form.urlType" />
                                    <span class="text-sm">Static</span>
                                </label>
                                <label class="inline-flex items-center space-x-2">
                                    <input type="radio" value="dynamic" v-model="form.urlType" />
                                    <span class="text-sm">Dynamic</span>
                                </label>
                            </div>
                        </div>

                        <!-- Static URL -->
                        <div v-if="form.urlType === 'static'">
                            <label for="url" class="block text-sm font-medium mb-1">URL</label>
                            <input v-model="form.url" id="url" type="text" placeholder="https://example.com/promo"
                                class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 px-4 py-2" />
                        </div>

                        <!-- Dynamic URL -->
                        <div v-else class="space-y-3">
                            <label class="block text-sm font-medium">Dynamic URL</label>
                            <input v-model="form.baseUrl" type="text" placeholder="https://example.com/promo"
                                class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 px-4 py-2" />

                            <div class="space-y-2">
                                <div v-for="(param, index) in form.params" :key="index"
                                    class="flex items-center space-x-2">
                                    <input v-model="param.key" type="text" placeholder="param name (e.g. user_id)"
                                        class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500" />
                                    <input v-model="param.value" type="text" placeholder="value (e.g. 123)"
                                        class="flex-1 rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500" />
                                    <button type="button" @click="removeParam(index)"
                                        class="text-red-500 hover:text-red-700 text-sm font-medium">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <button type="button" @click="addParam"
                                class="text-sm text-blue-600 hover:underline font-medium">
                                + Add Parameter
                            </button>

                            <!-- URL Preview -->
                            <div class="bg-gray-50 border rounded-lg px-4 py-2 mt-3">
                                <p class="text-xs text-gray-600">Preview:</p>
                                <code class="text-sm text-black break-words">{{ dynamicUrlPreview }}</code>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                                Save Template
                            </button>
                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </AppLayout>
</template>

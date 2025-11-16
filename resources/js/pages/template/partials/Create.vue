<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { email } from '@/routes'
import { Head } from '@inertiajs/vue3'
import { ref, computed, defineEmits } from 'vue'
import type { BreadcrumbItem } from '@/types'
import Message from '@/components/Message.vue'

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Template', href: email().url },
]

const emit = defineEmits<{ (e: 'update:modelValue', value: string): void }>()
const onChange = (content: string) => emit('update:modelValue', content)

// Form
const form = ref({
    name: '',
    wording: '',
    to: '',
    urlType: 'static',
    buttonType: 'none', // button | wa
    url: '',
    subject: '',
    baseUrl: '',
    buttonText: '',
    params: [] as { key: string; value: string }[],
})

// Tambah & hapus parameter
const addParam = () => form.value.params.push({ key: '', value: '' })
const removeParam = (index: number) => form.value.params.splice(index, 1)

// Dynamic URL Preview
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

// Final URL (Button Click / WA Click)
const finalButtonUrl = computed(() => {
    const baseUrl =
        form.value.urlType === 'static'
            ? form.value.url
            : dynamicUrlPreview.value

    if (form.value.buttonType !== 'wa') {
        return baseUrl
    }

    // WA URL Builder
    const baseWa = "https://wa.me/?text="
    const message = `*${form.value.subject}*\n\n${form.value.wording || ""}`
    const text = `${message}\n\n${baseUrl}`

    return baseWa + encodeURIComponent(text)
})

// Submit
const submitForm = () => {
    const payload = {
        ...form.value,
        finalUrl: finalButtonUrl.value,
    }

    console.log('Form Submitted:', payload)
    // TODO: Inertia.post('/templates', payload)
}
</script>

<template>

    <Head title="Create Template" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full mx-auto rounded-2xl shadow p-8 space-y-6">
            <div class="flex w-full flex-col lg:flex-row space-x-0 lg:space-x-6 space-y-6 lg:space-y-0">

                <div class="flex-9">
                    <h2 class="text-xl font-semibold">Create New Template</h2>
                    <p class="text-sm text-gray-600 mb-4 ">Fill in the details below to create a new email template.</p>

                    <form @submit.prevent="submitForm" class="space-y-6">

                        <!-- Template Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium mb-1">Template Name</label>
                            <input v-model="form.name" id="name" type="text" placeholder="e.g. Promo Akhir Tahun"
                                class="w-full rounded-lg border border-gray-300 focus:ring-2 mt-3 focus:ring-blue-500 px-4 py-2"
                                required />
                        </div>

                        <!-- Wording -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Wording</label>
                            <Message :content="form.wording" :button="form.buttonType" :url="finalButtonUrl" :button-text="form.buttonText" />
                        </div>

                        <!-- Button Type -->
                        <div class="space-y-6">
                            <label class="text-sm font-semibold text-gray-200">Jenis Tombol</label>

                            <div class="grid grid-cols-3 gap-2 mt-3">
                                <button :class="form.buttonType === 'none'
                                    ? 'bg-primary text-black'
                                    : 'bg-gray-800 border border-gray-700 text-gray-300'"
                                    class="py-2 rounded-xl transition" @click="form.buttonType = 'none'">
                                    None
                                </button>

                                <button :class="form.buttonType === 'button'
                                    ? 'bg-primary text-black'
                                    : 'bg-gray-800 border border-gray-700 text-gray-300'"
                                    class="py-2 rounded-xl transition" @click="form.buttonType = 'button'">
                                    URL
                                </button>

                            </div>
                        </div>

                        <!-- URL Type -->
                        <div v-if="form.buttonType !== 'none'">
                            <div class="mb-5"> 
                                <label class="block text-sm font-medium mb-2">Button Text</label>
                                <div class="flex items-center space-x-6">
                                    
                                    <input v-model="form.buttonText" id="url" type="text"
                                        placeholder="Kunjungi Kami"
                                        class="w-full rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 px-4 py-2" />
                                </div>
                            </div>
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
                        </div>

                        <!-- Static URL -->
                        <div v-if="form.buttonType !== 'none'">
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
                                        <input v-model="param.key" type="text" placeholder="param name"
                                            class="flex-1 rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500" />
                                        <input v-model="param.value" type="text" placeholder="value"
                                            class="flex-1 rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500" />

                                        <button type="button" @click="removeParam(index)"
                                            class="text-red-500 hover:text-red-700 text-sm font-medium">✕</button>
                                    </div>
                                </div>

                                <button type="button" @click="addParam"
                                    class="text-sm text-blue-600 hover:underline font-medium">
                                    + Add Parameter
                                </button>

                                <!-- Dynamic URL Preview -->
                                <div class="bg-gray-50 border rounded-lg px-4 py-2 mt-3">
                                    <p class="text-xs text-gray-600">Preview:</p>
                                    <code class="text-sm text-black break-words">{{ dynamicUrlPreview }}</code>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-primary text-black hover:text-white hover:bg-[#66a43c]">
                                Save Template
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { email, templateStorePost, template } from '@/routes'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import type { BreadcrumbItem } from '@/types'
import Message from '@/components/Message.vue'
import Loading from '@/components/Loading.vue'
import { usePage } from '@inertiajs/vue3'


/* =========================
   Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Template', href: email().url },
]

/* =========================
   State
========================= */
const isLoading = ref(false)
const errors = ref<Record<string, string[]>>({})
const page = usePage()

const successModal = ref({
    show: false,
    message: '',
})

const props = defineProps<{
    template: {
        data: Array<{
            id: number
            name: string
            subject: string
            wording: string
            button_type: string
            button_text: string
            url_type: string
            url: string
            params: string
            is_active: boolean
        }>
        links: Array<{
            url: string | null
            label: string
            active: boolean
        }>
        current_page: number
        per_page: number
    }
}>()

const data = ref(props.template);

// Close modal
const closeSuccessModal = () => {
    successModal.value.show = false
}

const form = ref({
    name: data.value.name,
    subject: data.value.subject,
    wording: data.value.wording,
    buttonType: 'none', // none | button | wa
    buttonText: data.value.buttonText,
    urlType: 'static', // static | dynamic
    url: data.value.url,
    baseUrl: data.value.baseUrl,
    params: [] as { key: string; value: string }[],
})

/* =========================
   Params Handler
========================= */
const addParam = () => {
    form.value.params.push({ key: '', value: '' })
}

const removeParam = (index: number) => {
    form.value.params.splice(index, 1)
}

/* =========================
   Dynamic URL Preview
========================= */
const dynamicUrlPreview = computed(() => {
    let url = form.value.baseUrl.trim()
    if (!url) return ''

    const query: Record<string, string> = {}

    form.value.params.forEach(({ key, value }) => {
        if (!key || !value) return

        const pattern = new RegExp(`{${key}}`, 'g')

        if (pattern.test(url)) {
            url = url.replace(pattern, encodeURIComponent(value))
        } else {
            query[key] = value
        }
    })

    const qs = Object.entries(query)
        .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
        .join('&')

    return qs ? `${url}${url.includes('?') ? '&' : '?'}${qs}` : url
})

/* =========================
   Final Button URL
========================= */
const finalButtonUrl = computed(() => {
    if (form.value.buttonType === 'none') return ''

    const url =
        form.value.urlType === 'static'
            ? form.value.url
            : dynamicUrlPreview.value

    if (form.value.buttonType === 'button') return url

    // WA
    const waText = `*${form.value.subject}*\n\n${form.value.wording}\n\n${url}`
    return `https://wa.me/?text=${encodeURIComponent(waText)}`
})

/* =========================
   Submit
========================= */
const submitForm = () => {
    isLoading.value = true
    errors.value = {}

    router.post(templateStorePost.url(), {
        name: form.value.name,
        subject: form.value.subject,
        wording: form.value.wording,
        button_type: form.value.buttonType,
        button_text: form.value.buttonType === 'none' ? null : form.value.buttonText,
        url_type: form.value.buttonType === 'none' ? null : form.value.urlType,
        url:
            form.value.buttonType === 'none'
                ? null
                : form.value.urlType === 'static'
                    ? form.value.url
                    : dynamicUrlPreview.value,
        params: form.value.params,
    }, {
        preserveScroll: true,
        onFinish: () => {
            alert('Template Update successfully!');
            router.visit(template.url());
            isLoading.value = false;
        },
        onError: (err) => {
            errors.value = err
        },
    })
}

</script>

<template>

    <Head title="Edit Template" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <Loading />
        </div>

        <!-- Success Modal -->
        <div v-if="successModal.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center">

                <!-- Icon -->
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="text-green-600 text-2xl">✓</span>
                    </div>
                </div>

                <!-- Title -->
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    Berhasil
                </h3>

                <!-- Message -->
                <p class="text-sm text-gray-600 mb-6">
                    {{ successModal.message }}
                </p>

                <!-- Actions -->
                <div class="flex justify-center gap-3">
                    <button @click="closeSuccessModal"
                        class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                        OK
                    </button>
                </div>

            </div>
        </div>

        <div class="w-full mx-auto  rounded-2xl shadow p-8 space-y-6">
            <h2 class="text-xl font-semibold">Edit Template</h2>
            <p class="text-sm text-gray-500">
                Buat template untuk email / WhatsApp blasting
            </p>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium">Template Name</label>
                    <input v-model="form.name" type="text" class="w-full mt-2 border rounded-lg px-4 py-2" />
                    <div v-if="errors.name" class="text-red-500 text-sm mt-1">
                        {{ errors.name }}
                    </div>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium">Subject</label>
                    <input v-model="form.subject" type="text" class="w-full mt-2 border rounded-lg px-4 py-2" />
                    <p v-if="errors.subject" class="text-red-500 text-sm">{{ errors.subject }}</p>
                </div>

                <!-- Wording -->
                <div>
                    <label class="block text-sm font-medium">Wording</label>
                    <Message v-model="form.wording" :button="form.buttonType" :url="finalButtonUrl"
                        :button-text="form.buttonText" />
                    <p v-if="errors.wording" class="text-red-500 text-sm">{{ errors.wording }}</p>
                </div>

                <!-- Button Type -->
                <div>
                    <label class="block text-sm font-medium">Button Type</label>
                    <div class="flex space-x-2 mt-2">
                        <button type="button" @click="form.buttonType = 'none'" class="btn">None</button>
                        <button type="button" @click="form.buttonType = 'button'" class="btn">URL</button>
                        <button type="button" @click="form.buttonType = 'wa'" class="btn">WhatsApp</button>
                    </div>
                </div>

                <!-- Button Config -->
                <div v-if="form.buttonType !== 'none'" class="space-y-4">
                    <input v-model="form.buttonText" placeholder="Button Text"
                        class="w-full border rounded-lg px-4 py-2" />

                    <!-- URL Type -->
                    <div class="flex space-x-4">
                        <label>
                            <input type="radio" value="static" v-model="form.urlType" />
                            Static
                        </label>
                        <label>
                            <input type="radio" value="dynamic" v-model="form.urlType" />
                            Dynamic
                        </label>
                    </div>

                    <!-- Static -->
                    <input v-if="form.urlType === 'static'" v-model="form.url" placeholder="https://example.com"
                        class="w-full border rounded-lg px-4 py-2" />

                    <!-- Dynamic -->
                    <div v-else class="space-y-3">
                        <input v-model="form.baseUrl" placeholder="https://example.com/{id}"
                            class="w-full border rounded-lg px-4 py-2" />

                        <div v-for="(p, i) in form.params" :key="i" class="flex gap-2">
                            <input v-model="p.key" placeholder="key" class="flex-1 border px-2 py-1" />
                            <input v-model="p.value" placeholder="value" class="flex-1 border px-2 py-1" />
                            <button type="button" @click="removeParam(i)">✕</button>
                        </div>

                        <button type="button" @click="addParam" class="text-blue-600 text-sm">
                            + Add Param
                        </button>

                        <code class="block bg-gray-100 p-2 rounded text-sm">
              {{ dynamicUrlPreview }}
            </code>
                    </div>
                </div>

                <div class="flex justify-end gap-5">
                    <div class="text-right">
                        <Link :href="template()">
                            <button
                                class="px-4 py-2 bg-gray-300 text-black font-semibold rounded-lg cursor-pointer hover:bg-gray-500">
                                Cancel
                            </button>
                        </Link>
                    </div>

                    <div class="text-right">
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-black font-semibold rounded-lg cursor-pointer hover:bg-green-700">
                            Save Template
                        </button>
                    </div>
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
}
</style>

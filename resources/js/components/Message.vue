<script setup lang="ts">
import { ref, watch } from 'vue'
import Editor from '@tinymce/tinymce-vue'

// Props
const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    button: String,
    url: String,
    buttonText: String,
})

// Emit untuk v-model
const emit = defineEmits(['update:modelValue'])

// Local state editor → sync ke parent
const content = ref(props.modelValue || '<p>Mulai tulis di sini...</p>')

// Sinkronisasi: jika parent mengubah wording (jarang tapi penting)
watch(() => props.modelValue, val => {
    if (val !== content.value) {
        content.value = val
    }
})

// Kirim ke parent tiap editor berubah
watch(content, (val) => {
    emit('update:modelValue', val)
})
</script>

<template>
    <div class="space-y-4 mt-3">

        <!-- PREVIEW -->
        <div class="bg-gray-50 text-black border p-4 rounded">
            <h2 class="font-semibold mb-2">Preview Wording</h2>

            <div v-html="content" class="prose max-w-full"></div>

            <div v-if="props.button !== 'none'" class="mt-4">
                <a :href="props.url" target="_blank"
                    class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700">
                    {{ props.buttonText || 'Kunjungi' }}
                </a>
            </div>
        </div>

        <!-- EDITOR -->
        <Editor
            api-key="eurlu7d7btago4qbkngk9koxh3cn62potiv7f1ryk6kmosf7"
            v-model="content"
            :init="{
                height: 300,
                menubar: false,
                toolbar_mode: 'sliding',
                plugins:
                    'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar:
                    'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat'
            }"
        />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Editor from '@tinymce/tinymce-vue'

const props = defineProps({
    content: String,
    button: String,
    url: String,
    buttonText: String,
});


// WYSIWYG content
const content = ref('<p>Mulai tulis di sini...</p>')

// Button control
const button_type = ref<'none' | 'url' | 'wa'>('none')
const button_url = ref('')
const button_text = ref('Klik di sini')

// Optional submit
const submitForm = () => {
    console.log('Content submitted:', content.value)
}
</script>

<template>
    <div class="space-y-4 mt-3">

        <!-- PREVIEW -->
        <div class="bg-gray-50 text-black border p-4 rounded">
            <h2 class="font-semibold mb-2">Preview Wording</h2>

            <!-- Wording HTML -->
            <div v-html="content" class="prose max-w-full"></div>

            <!-- Button Preview -->
            <div v-if="props.button !== 'none'" class="mt-4">
                <a :href="props.url" target="_blank"
                    class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700">
                    {{ props.buttonText || 'Kunjungi' }}
                </a>
            </div>
        </div>

        <!-- EDITOR -->
        <Editor api-key="eurlu7d7btago4qbkngk9koxh3cn62potiv7f1ryk6kmosf7" v-model="content" :init="{
            height: 300,
            menubar: false,
            toolbar_mode: 'sliding',
            plugins:
                'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar:
                'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat'
        }" />
    </div>
</template>

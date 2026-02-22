<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItem } from '@/types'
import blasting from '@/routes/blasting';

const props = defineProps<{
  templates: Array<{
    id: number
    name: string
  }>
  campaign: {
    id: number
    template_id: number,
    name: string
    status: 'draft' | 'running' | 'paused' | 'finished'
    scheduled_at: string | null
  }
}>()

/* =========================
   Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Campaign', href: blasting.campaigns.index().url },
  { title: 'Edit Campaign', href: '#' },
]

// untuk INPUT datetime-local
const toDatetimeLocal = (value: string | null) => {
  if (!value) return ''

  const date = new Date(value)
  const pad = (n: number) => String(n).padStart(2, '0')

  return (
    date.getFullYear() +
    '-' +
    pad(date.getMonth() + 1) +
    '-' +
    pad(date.getDate()) +
    'T' +
    pad(date.getHours()) +
    ':' +
    pad(date.getMinutes())
  )
}

// untuk DISPLAY saja (table / text)
const formatDatetime = (value: string | null) => {
  if (!value) return '-'

  const date = new Date(value)
  const pad = (n: number) => String(n).padStart(2, '0')

  return (
    pad(date.getDate()) +
    '/' +
    pad(date.getMonth() + 1) +
    '/' +
    date.getFullYear() +
    ' ' +
    pad(date.getHours()) +
    ':' +
    pad(date.getMinutes())
  )
}


/* =========================
   FORM (match DB)
========================= */
const form = ref({
  template_id: props.campaign.template_id,
  name: props.campaign.name,
  scheduled_at: toDatetimeLocal(props.campaign.scheduled_at),
})

const errors = ref<Record<string, string[]>>({})
const isLoading = ref(false)

/* =========================
   SUBMIT
========================= */
const submit = () => {
  if (isLoading.value) return

  isLoading.value = true
  errors.value = {}

  // console.log('Form submitted:', form.value);
  router.put(
    blasting.campaigns.update(props.campaign.id).url,
    {
      template_id: form.value.template_id,
      name: form.value.name,
      scheduled_at: form.value.scheduled_at,
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

  <Head title="Edit Campaign" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Loading Overlay -->
    <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      Loading...
    </div>

    <div class="w-full mx-auto rounded-xl shadow p-6 space-y-6">
      <h2 class="text-lg font-semibold">Edit Blasting Campaign</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- TEMPLATE -->
        <div>
          <label class="block text-sm font-medium">Template</label>
          <select v-model="form.template_id" class="input bg-white text-black">
            <option disabled value="">-- Select Template --</option>
            <option v-for="t in templates" :key="t.id" :value="t.id" class="bg-white text-black">
              {{ t.name }}
            </option>
          </select>
          <p v-if="errors.template_id" class="error">
            {{ errors.template_id }}
          </p>
        </div>

        <!-- NAME -->
        <div>
          <label class="block text-sm font-medium">Campaign Name</label>
          <input v-model="form.name" class="input" />
          <p v-if="errors.name" class="error">
            {{ errors.name }}
          </p>
        </div>

        <!-- SCHEDULE -->
        <div>
          <label class="block text-sm font-medium">Schedule (optional)</label>
          <input type="datetime-local" v-model="form.scheduled_at" class="input bg-white text-black" />
          <p v-if="errors.scheduled_at" class="error">
            {{ errors.scheduled_at }}
          </p>
        </div>

        <!-- ACTION -->
        <div class="text-right space-x-3">
           <button type="button" @click="router.visit(blasting.campaigns.index().url)"
            class="px-4 py-2 cursor-pointer bg-gray-500 text-white rounded-lg disabled:opacity-60">
            Batal
          </button>
          <button type="submit" :disabled="isLoading"
            class="px-4 py-2 cursor-pointer  bg-primary text-black rounded-lg disabled:opacity-60">
            Update Campaign
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

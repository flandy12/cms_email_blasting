<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Loading from '@/components/Loading.vue'
import type { BreadcrumbItem } from '@/types'
import roles from '@/routes/roles'

/* =========================
Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Create Role',
    href: roles.create().url
  }
]

/* =========================
Props
========================= */
const props = defineProps<{
  permissions: { id: number; name: string }[]
  errors: Record<string, string>
}>()

/* =========================
State
========================= */
const isLoading = ref(false)

const successModal = ref({
  show: false,
  message: ''
})

const form = ref({
  name: '',
  permissions: [] as string[]
})

/* =========================
Actions
========================= */
const submitForm = () => {
  isLoading.value = true

  router.post(roles.store().url, form.value, {
    onSuccess: () => {
      successModal.value = {
        show: true,
        message: 'Role berhasil dibuat'
      }

      form.value = {
        name: '',
        permissions: []
      }
    },
    onError: () => {
      // validation error otomatis dari props.errors
    },
    onFinish: () => {
      isLoading.value = false
    }
  })
}

const closeSuccessModal = () => {
  successModal.value.show = false
  router.visit(roles.index().url)
}
</script>


<template>

  <Head title="Create Role" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <!-- Loading -->
    <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <Loading />
    </div>

    <!-- Success Modal -->
    <div v-if="successModal.show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white rounded-2xl text-black shadow-xl w-full max-w-md p-6 text-center">
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

    <!-- FORM -->
    <div class="w-full rounded-2xl shadow p-8 space-y-6">
      <h2 class="text-xl font-semibold">Create New Role</h2>
      <p class="text-sm text-gray-500">
        Buat Role dan assign permission
      </p>

      <form @submit.prevent="submitForm" class="space-y-6">

        <!-- Role Name -->
        <div>
          <label class="block text-sm font-medium">Role Name</label>
          <input v-model="form.name" type="text"
            class="w-full mt-2 border rounded-lg px-4 py-2" />

          <p v-if="errors.name" class="text-red-500 text-sm mt-1">
            {{ errors.name }}
          </p>
        </div>

        <!-- Permissions -->
        <div>
          <label class="block text-sm font-medium mb-2">Permissions</label>

          <div class="max-h-60 overflow-y-auto border rounded-lg p-4">
            <label
              v-for="perm in permissions"
              :key="perm.id"
              class="flex items-center space-x-2 mb-2"
            >
              <input
                type="checkbox"
                :value="perm.name"
                v-model="form.permissions"
              />
              <span class="text-sm">{{ perm.name }}</span>
            </label>
          </div>

          <p v-if="errors.permissions" class="text-red-500 text-sm mt-1">
            {{ errors.permissions }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-5">
          <Link :href="roles.index().url"
            class="px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400">
            Cancel
          </Link>

          <button type="submit"
            class="px-4 py-2 bg-primary text-black rounded-lg hover:bg-green-700">
            Save Role
          </button>
        </div>

      </form>
    </div>

  </AppLayout>
</template>
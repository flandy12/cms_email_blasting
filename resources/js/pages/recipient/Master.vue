<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItem } from '@/types'
import Loading from '@/components/Loading.vue'
import blasting from '@/routes/blasting'


/* =========================
Props
========================= */

const props = defineProps<{
  recipients: {
    data: any[]
    links: any[]
    current_page: number
    per_page: number
  }
}>()

const recipients = ref(props.recipients)


/* =========================
State
========================= */

const file = ref<File | null>(null)
const isLoading = ref(false)


/* =========================
Breadcrumb
========================= */

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Recipient', href: blasting.recipients.index().url },
]


/* =========================
Import Excel
========================= */

const handleImport = () => {

  if (!file.value) return

  isLoading.value = true

  const formData = new FormData()
  formData.append('file', file.value)

  router.post(
    blasting.recipients.import(),
    formData,
    {
      forceFormData: true,

      onFinish: () => {
        isLoading.value = false
      }
    }
  )
}


/* =========================
Delete Recipient
========================= */

const recipientDelete = (id: number) => {

  if (!confirm('Delete this recipient?')) return

  isLoading.value = true

  router.delete(
    blasting.recipients.destroy(id),
    {
      onFinish: () => {
        isLoading.value = false
      }
    }
  )

}


/* =========================
Delete All
========================= */

const deleteAll = () => {

  if (!confirm('Delete all recipients?')) return

  isLoading.value = true

  router.delete(
    blasting.recipients.destroyAll(),
    {
      onFinish: () => {
        isLoading.value = false
      }
    }
  )

}

</script>



<template>

  <Head title="Recipients" />

  <AppLayout :breadcrumbs="breadcrumbs">


    <!-- Loading Overlay -->

    <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <Loading />
    </div>

    <!-- HEADER -->

    <div class="flex justify-between items-center mb-6  px-5">

      <div>

        <h1 class="text-2xl font-semibold">
          Recipients
        </h1>

        <p class="text-sm text-gray-500">
          Manage email recipients
        </p>

      </div>



      <div class="flex gap-3">

        <button command="show-modal" commandfor="dialog-import" class="px-4 py-2 rounded-lg bg-primary text-black">
          Import
        </button>


        <Link :href="blasting.recipients.create()" class="px-4 py-2 rounded-lg bg-gray-400 text-black">
          Add Recipient
        </Link>


        <button v-if="recipients.data.length" @click="deleteAll" class="px-4 py-2 rounded-lg bg-red-500 text-white">
          Delete All
        </button>


      </div>

    </div>

    <!-- IMPORT DIALOG -->

    <dialog id="dialog-import" class="p-0 border-0 bg-transparent">

      <div class="fixed inset-0 bg-black/40 flex items-center justify-center">

        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">

          <!-- Title -->
          <h2 class="text-lg font-semibold mb-4">
            Import Recipients
          </h2>


          <!-- File Input -->
          <input type="file" accept=".xlsx,.xls" @change="(e: any) => file = e.target.files[0]"
            class="border p-2 w-full mb-5 rounded-lg" />


          <!-- Buttons -->
          <div class="flex justify-end gap-3">

            <button command="close" commandfor="dialog-import" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
              Cancel
            </button>


            <button @click="handleImport" class="px-4 py-2 bg-primary text-black rounded-lg">
              Import
            </button>


          </div>

        </div>

      </div>

    </dialog>

    <!-- TABLE -->

    <div class="bg-white dark:bg-gray-700/50 shadow rounded-2xl overflow-hidden mx-5">

      <table class="w-full">

        <thead class="bg-gray-50  dark:bg-gray-600/50  border-b">

          <tr>

            <th class="px-6 py-3 text-left text-xs uppercase">
              No
            </th>

            <th class="px-6 py-3 text-center text-xs uppercase">
              Name
            </th>

            <th class="px-6 py-3 text-center text-xs uppercase">
              Email
            </th>

            <th class="px-6 py-3 text-center text-xs uppercase">
              Action
            </th>

          </tr>

        </thead>



        <tbody>

          <tr v-for="(recipient, index) in recipients.data" :key="recipient.id" class="border-b hover:bg-gray-50">

            <td class="px-6 py-4">

              {{ (recipients.current_page - 1) * recipients.per_page + index + 1 }}

            </td>

            <td class="px-6 py-4 text-center">

              {{ recipient.name || '-' }}

            </td>

            <td class="px-6 py-4 text-center">

              {{ recipient.email || '-' }}

            </td>

            <td class="px-6 py-4 text-center space-x-2">
              <Link :href="blasting.recipients.edit(recipient.id).url" class="px-4 py-2 bg-primary rounded text-black">
                Edit
              </Link>
              <button @click="recipientDelete(recipient.id)" class="px-4 py-2 bg-red-500 text-white rounded">
                Delete
              </button>

            </td>

          </tr>

          <tr v-if="!recipients.data.length">

            <td colspan="4" class="text-center py-10 text-gray-400">

              No recipients available

            </td>

          </tr>


        </tbody>

      </table>

    </div>

    <!-- PAGINATION -->

    <div v-if="recipients.links.length > 3" class="flex justify-end mt-6">

      <nav class="flex gap-2">

        <Link v-for="(link, i) in recipients.links" :key="i" :href="link.url || ''" preserve-scroll preserve-state
          class="px-4 py-2 border rounded-lg text-sm" :class="{

            'bg-primary text-black font-semibold':
              link.active,

            'text-gray-400 cursor-not-allowed':
              !link.url

          }" v-html="link.label" />

      </nav>

    </div>
  </AppLayout>

</template>
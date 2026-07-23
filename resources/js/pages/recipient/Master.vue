<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import type { BreadcrumbItem } from '@/types'
import Loading from '@/components/Loading.vue'
import blasting from '@/routes/blasting'
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const permissions = computed<string[]>(() => {
  return (page.props.auth?.permissions as string[]) ?? []
})

const can = (permission: string) => {
  return permissions.value.includes(permission)
}


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
const dialogImport = ref(null)


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

  router.delete(blasting.recipients.destroyAll(), {
    preserveScroll: true,

    onStart: () => {
      isLoading.value = true
    },

    onFinish: () => {
      isLoading.value = false
    },

    onError: (errors) => {
      console.error(errors)
      alert('Failed to delete data')
    },

    onSuccess: () => {
      alert('All data successfully reset');
      // ✅ CLOSE MODAL
      dialogImport.value?.close();

      // optional reset file
      file.value = null
    }
  })
}

</script>



<template>

  <Head title="Recipients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div v-if="!can('recipients.view')" class="flex justify-center items-center h-96 text-gray-500">
      You don't have permission to access this page.
    </div>
    <template v-else>
      <!-- Loading Overlay -->

      <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <Loading />
      </div>

      <!-- HEADER -->

      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8 p-6">

        <div>

          <h1 class="text-3xl font-bold tracking-tight text-foreground">
            Recipients
          </h1>


          <p class="mt-2 text-sm text-muted-foreground">
            Manage email recipients
          </p>

        </div>

        <div class="page-actions space-x-2">

          <button  v-if="can('recipients.import')" command="show-modal" commandfor="dialog-import"
            class="btn-outline  btn-primary text-black hover:text-black">
            Import
          </button>

          <Link  v-if="can('recipients.create')" :href="blasting.recipients.create()" class="btn-outline">
            Add Recipient
          </Link>

          <button v-if="recipients.data.length && can('recipients.delete')" @click="deleteAll" class="btn-danger">
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

              <button command="close" commandfor="dialog-import"
                class="px-4 py-2 border rounded-lg hover:btn-danger hover:text-black">
                Cancel
              </button>


              <button @click="handleImport" :disabled="isLoading" class="px-4 py-2 bg-primary text-black rounded-lg">
                <span v-if="!isLoading">Import</span>
                <span v-else>Loading...</span>
              </button>
            </div>

          </div>

        </div>

      </dialog>

      <!-- TABLE -->

      <div class="dashboard-table mx-6">

        <table class="dashboard-data-table">

          <thead>

            <tr>

              <th class="w-20">
                #
              </th>

              <th class="w-[35%]">
                Recipient
              </th>

              <th>
                Email
              </th>

              <th class="w-48 text-center">
                Action
              </th>

            </tr>

          </thead>

          <tbody>

            <tr v-for="(recipient, index) in recipients.data" :key="recipient.id">

              <!-- Number -->
              <td>

                {{ (recipients.current_page - 1) * recipients.per_page + index + 1 }}

              </td>

              <!-- Recipient -->
              <td>

                <div class="table-user">

                  <div class="table-avatar">

                    {{ (recipient.name || '?').charAt(0).toUpperCase() }}

                  </div>

                  <div>

                    <div class="table-title">

                      {{ recipient.name || 'Unknown Recipient' }}

                    </div>

                    <div class="table-subtitle">

                      Email Recipient

                    </div>

                  </div>

                </div>

              </td>

              <!-- Email -->
              <td class="text-muted">

                {{ recipient.email || '-' }}

              </td>

              <!-- Action -->
              <td>

                <div class="table-actions">

                  <Link v-if="can('recipients.edit')" :href="blasting.recipients.edit(recipient.id).url" class="btn-outline">

                    Edit

                  </Link>

                  <button v-if="can('recipients.delete')" @click="recipientDelete(recipient.id)" class="btn-danger">

                    Delete

                  </button>

                </div>

              </td>

            </tr>

            <tr v-if="!recipients.data.length">

              <td colspan="4" class="empty-state">

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
    </template>
  </AppLayout>

</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItem } from '@/types'
import Loading from '@/components/Loading.vue'
import blasting from '@/routes/blasting'

const props = defineProps<{
  recipients: {
    name: string
    email: string
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
    current_page: number
    per_page: number
  }
}>()

const recipients = ref(props.recipients)
const file = ref<File | null>(null);
const isLoading = ref(false);

/* =========================
   Breadcrumb
========================= */
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Recipient', href: blasting.recipients.index().url },
]

const handleImport = async () => {
  if (!file.value) return
  isLoading.value = true;
  const formData = new FormData()
  formData.append('file', file.value)

  try {
    await router.post(blasting.recipients.import(), formData, {
      forceFormData: true,
      onSuccess: () => {
        isLoading.value = false;
        router.reload()
      },
      onError: () => {
        isLoading.value = false;
      },
    })
  } catch (error) {
    isLoading.value = false;
    console.error('Error importing file:', error);
  }
}

const recipientDelete = (id: number) => {
  isLoading.value = true;
  router.delete(blasting.recipients.destroy(id), {
    onSuccess: () => {
      isLoading.value = false;
      router.reload()
    },
  })
}

</script>


<template>

  <Head title="Blasting Campaigns" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="overflow-y-auto">

      <div v-if="isLoading" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <Loading />
      </div>
      <!--import Dialog -->
      <div class="text-end me-5 mb-5">
        <div class="flex gap-5 justify-end">
          <button command="show-modal" commandfor="dialog-import"
            class="rounded-md bg-primary px-2.5 py-1.5 text-sm font-semibold text-black cursor-pointer inset-ring inset-ring-white/5 hover:bg-primary">Import
            Data</button>

          <button command="show-modal" commandfor="dialog-import"
            class="rounded-md bg-gray-400 px-2.5 py-1.5 text-sm font-semibold text-black cursor-pointer inset-ring inset-ring-white/5 hover:bg-primary">Add
            Recipient</button>
        </div>
        <el-dialog>
          <dialog id="dialog-import" aria-labelledby="dialog-title"
            class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
            <el-dialog-backdrop
              class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

            <div tabindex="0"
              class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
              <el-dialog-panel
                class="relative transform overflow-hidden rounded-lg bg-gray-800 text-left shadow-xl outline -outline-offset-1 outline-white/10 transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-lg data-closed:sm:translate-y-0 data-closed:sm:scale-95">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                  <div class="">

                    <div class="">
                      <h3 id="dialog-title" class="text-base font-semibold text-white">Import Recipients</h3>
                      <div class="mt-2">
                        <input type="file" accept=".excel,.xlsx" @change="(e: any) => file = e.target.files[0]"
                          class="mb-2 text-black mt-3 bg-gray-400 py-2 px-3 w-full rounded " />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                  <button type="button" command="close" commandfor="dialog-import" @click="handleImport()"
                    class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-black hover:bg-primary/20 sm:ml-3 sm:w-auto">Save</button>
                  <button type="button" command="close" commandfor="dialog-import"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20 sm:mt-0 sm:w-auto">Cancel</button>
                </div>
              </el-dialog-panel>
            </div>
          </dialog>
        </el-dialog>
      </div>

      <!-- Delete Dialog -->
      <el-dialog>
        <dialog id="dialog" aria-labelledby="dialog-title"
          class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
          <el-dialog-backdrop
            class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

          <div tabindex="0"
            class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
            <el-dialog-panel
              class="relative transform overflow-hidden rounded-lg bg-gray-800 text-left shadow-xl outline -outline-offset-1 outline-white/10 transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-lg data-closed:sm:translate-y-0 data-closed:sm:scale-95">
              <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div
                    class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-500/10 sm:mx-0 sm:size-10">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                      aria-hidden="true" class="size-6 text-red-400">
                      <path
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                        stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 id="dialog-title" class="text-base font-semibold text-white">Delete Recipient</h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-400">Are you sure you want to delete this recipient? All of your
                        data will be permanently removed. This action cannot be undone.</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" command="close" commandfor="dialog"
                  class="inline-flex w-full justify-center rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-red-400 sm:ml-3 sm:w-auto">Delete</button>
                <button type="button" command="close" commandfor="dialog"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20 sm:mt-0 sm:w-auto">Cancel</button>
              </div>
            </el-dialog-panel>
          </div>
        </dialog>
      </el-dialog>

      <table class="w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs uppercase">No</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Name</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Email</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Action</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <tr v-for="(recipient, index) in recipients.data" :key="recipient.id">
            <td class="px-6 py-4">
              {{ (recipients.current_page - 1) * recipients.per_page + index + 1 }}
            </td>

            <td class="px-6 py-4 text-center">
              {{ recipient.name ?? '' }}
            </td>
            <td class="px-6 py-4 text-center">
              {{ recipient.email ?? '-' }}
            </td>

            <td class="px-6 py-4 text-center space-x-2 ">
              <Link :href="blasting.recipients.edit(recipient.id).url" class="cursor-pointer">
                <button class="px-4 py-2 rounded-lg bg-primary text-black cursor-pointer">
                  Edit
                </button>
              </Link>

              <button class="px-4 py-2 rounded-lg bg-danger cursor-pointer" @click="recipientDelete(recipient.id)"
                command="show-modal" commandfor="dialog">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div class="flex justify-end mt-6" v-if="recipients.links.length > 3">
        <nav class="inline-flex space-x-1">
          <Link v-for="(link, i) in recipients.links" :key="i" :href="link.url || ''" preserve-scroll preserve-state
            class="px-3 py-2 border rounded-lg text-sm" :class="{
              'bg-primary text-black font-semibold': link.active,
              'text-gray-400 cursor-not-allowed': !link.url,
              'hover:bg-gray-100': link.url && !link.active,
            }" v-html="link.label" />
        </nav>
      </div>
    </div>
  </AppLayout>
</template>
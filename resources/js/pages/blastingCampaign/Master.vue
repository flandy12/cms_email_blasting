<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItem } from '@/types'
import { Plus } from 'lucide-vue-next'
import blasting from '@/routes/blasting'

const props = defineProps<{
  campaigns: {
    data: Array<{
      id: number
      name: string
      status: 'draft' | 'running' | 'paused' | 'finished'
      scheduled_at: string | null
      total_recipient: number
      sent_count: number
      failed_count: number
      created_at: string
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

const campaigns = ref(props.campaigns)

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Campaign', href: blasting.campaigns.index() },
]

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

</script>


<template>

  <Head title="Blasting Campaigns" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <!-- Absolute Plus Button -->
    <div class="flex justify-end">
      <Link :href="blasting.campaigns.create().url"
        class=" z-10 flex h-12 w-48 rounded items-center justify-center bg-primary text-black shadow hover:bg-primary/90 transition"
        aria-label="Add Template">
        <span class="mr-2">Add Template</span>
        <Plus class="h-5 w-5" />
      </Link>
    </div>

    <div class="overflow-y-auto">
      <table class="w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs uppercase">No</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Name</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Status</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Scheduled</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Sent</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Failed</th>
            <th class="px-6 py-3 text-center text-xs uppercase">Action</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <tr v-for="(campaign, index) in campaigns.data" :key="campaign.id">
            <td class="px-6 py-4">
              {{ (campaigns.current_page - 1) * campaigns.per_page + index + 1 }}
            </td>

            <td class="px-6 py-4 text-center">
              {{ campaign.name }}
            </td>

            <td class="px-6 py-4 text-center">
              <span class="px-2 py-1 rounded text-black capitalize" :class="{
                'bg-gray-200': campaign.status === 'draft',
                'bg-green-200': campaign.status === 'running',
                'bg-yellow-200': campaign.status === 'paused',
                'bg-blue-200': campaign.status === 'finished',
              }">
                {{ campaign.status }}
              </span>
            </td>

            <td class="px-6 py-4 text-center">
              {{ formatDatetime(campaign.scheduled_at) ?? '-' }}
            </td>

            <td class="px-6 py-4 text-center">
              {{ campaign.sent_count }} / {{ campaign.total_recipient }}
            </td>

            <td class="px-6 py-4 text-center">
              {{ campaign.failed_count }}
            </td>

            <td class="px-6 py-4 text-center space-x-2 ">
              <Link :href="blasting.campaigns.show(campaign.id).url" class="cursor-pointer">
                <button class="px-4 py-2 rounded-lg bg-primary text-black cursor-pointer">
                  Edit
                </button>
              </Link>

              <button class="px-4 py-2 rounded-lg bg-danger cursor-pointer">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div class="flex justify-end mt-6" v-if="campaigns.links.length > 3">
        <nav class="inline-flex space-x-1">
          <Link v-for="(link, i) in campaigns.links" :key="i" :href="link.url || ''" preserve-scroll preserve-state
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

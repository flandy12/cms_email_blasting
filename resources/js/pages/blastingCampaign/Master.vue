<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItem } from '@/types'
import { Plus, Trash2 } from 'lucide-vue-next'
import blasting from '@/routes/blasting'


/* =========================
Props
========================= */

const props = defineProps<{
  campaigns: {
    data: any[]
    links: any[]
    current_page: number
    per_page: number
  }
}>()

const campaigns = ref(props.campaigns)


/* =========================
Breadcrumb
========================= */

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Campaign',
    href: blasting.campaigns.index().url
  }
]



/* =========================
Format Datetime
========================= */

const formatDatetime = (value: string | null) => {

  if (!value) return '-'

  const d = new Date(value)

  const pad = (n: number) => String(n).padStart(2, '0')

  return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`

}



/* =========================
Delete One
========================= */

const destroy = (id: number) => {

  if (!confirm('Delete this campaign?')) return

  router.delete(
    blasting.campaigns.destroy(id).url,
    {
      preserveScroll: true
    }
  )

}



/* =========================
Delete All
========================= */

const deleteAll = () => {

  if (!confirm('Delete all campaigns?')) return

  router.delete(
    blasting.campaigns.destroyAll().url,
    {
      preserveScroll: true
    }
  )

}

</script>




<template>

  <Head title="Campaigns" />

  <AppLayout :breadcrumbs="breadcrumbs">



    <!-- HEADER -->

    <div class="flex justify-between items-center mb-6 px-5">

      <div>

        <h1 class="text-2xl font-semibold">
          Campaigns
        </h1>

        <p class="text-sm text-gray-500">
          Manage email blasting campaigns
        </p>

      </div>

      <div class="flex gap-3">
        <Link :href="blasting.campaigns.create().url"
          class="flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-black shadow">
          <Plus class="h-5 w-5" />
          Add Campaign
        </Link>

        <button v-if="campaigns.data.length" @click="deleteAll"
          class="flex items-center gap-2 px-5 py-3 rounded-xl bg-red-500 text-white shadow">
          <Trash2 class="h-5 w-5" />
          Delete All
        </button>
      </div>
    </div>

    <!-- TABLE -->

    <div class="bg-white dark:bg-gray-700/50 shadow rounded-2xl overflow-hidden mx-5">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-600/50 border-b">
          <tr>

            <th class="px-6 py-3 text-left text-xs uppercase">
              No
            </th>

            <th class="px-6 py-3 text-center text-xs uppercase">
              Name
            </th>

            <th class="px-6 py-3 text-center text-xs uppercase">
              Status
            </th>

            <th class="px-6 py-3 text-center text-xs uppercase">
              Scheduled
            </th>


            <th class="px-6 py-3 text-center text-xs uppercase">
              Sent
            </th>


            <th class="px-6 py-3 text-center text-xs uppercase">
              Failed
            </th>


            <th class="px-6 py-3 text-center text-xs uppercase">
              Action
            </th>
          </tr>
        </thead>

        <tbody>

          <tr v-for="(campaign, index) in campaigns.data" :key="campaign.id"
            class="border-b hover:bg-gray-50 transition">

            <td class="px-6 py-4">
              {{ (campaigns.current_page - 1) * campaigns.per_page + index + 1 }}
            </td>

            <td class="px-6 py-4 text-center font-medium">

              {{ campaign.name }}

            </td>

            <td class="px-6 py-4 text-center">

              <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize" :class="{

                'bg-gray-200 text-black':
                  campaign.status === 'draft',

                'bg-green-200 text-black':
                  campaign.status === 'running',

                'bg-yellow-200 text-black':
                  campaign.status === 'paused',

                'bg-blue-200 text-black':
                  campaign.status === 'finished'

              }">

                {{ campaign.status }}

              </span>


            </td>



            <td class="px-6 py-4 text-center">

              {{ formatDatetime(campaign.scheduled_at) }}

            </td>

            <td class="px-6 py-4 text-center">

              {{ campaign.sent_count }}
              /
              {{ campaign.total_recipient }}

            </td>

            <td class="px-6 py-4 text-center">

              {{ campaign.failed_count }}

            </td>

            <td class="px-6 py-4 text-center space-x-2" v-if="campaign.sent_count <= campaign.total_recipient && campaign.status != 'finished'">
              <Link :href="blasting.campaigns.show(campaign.id).url" class="px-4 py-2 bg-primary rounded text-black">
                Detail
              </Link>
              <button @click="destroy(campaign.id)" class="px-4 py-2 bg-red-500 text-white rounded">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="!campaigns.data.length">

            <td colspan="7" class="text-center py-12 text-gray-400">

              No campaigns available

            </td>

          </tr>
        </tbody>
      </table>
    </div>


    <!-- PAGINATION -->

    <div v-if="campaigns.links.length > 3" class="flex justify-end mt-6">

      <nav class="flex gap-2">
        <Link v-for="(link, i) in campaigns.links" :key="i" :href="link.url || ''" preserve-scroll preserve-state
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
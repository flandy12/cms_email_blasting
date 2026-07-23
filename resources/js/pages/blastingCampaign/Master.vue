<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import type { BreadcrumbItem } from '@/types'
import { Plus, Trash2 } from 'lucide-vue-next'
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
    title: 'Schedule List',
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

  <Head title="Schedule List" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <div v-if="!can('schedule.view')" class="flex items-center justify-center h-96 text-muted-foreground">
      You don't have permission to access this page.
    </div>

    <template v-else>
      <!-- HEADER -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8 p-6">

        <div>

          <h1 class="text-3xl font-bold tracking-tight text-foreground">
            Schedule List
          </h1>


          <p class="mt-2 text-sm text-muted-foreground">
            Manage your email campaign schedule.
          </p>

        </div>

        <Link :href="blasting.campaigns.create().url" class="flex btn-primary">

          <Plus class="w-6 h-6" />

          Add Schedule

        </Link>

      </div>

      <!-- TABLE -->

      <div class="dashboard-table mx-6">

        <table class="w-full text-center">

          <thead>

            <tr>

              <th>Campaign</th>

              <th>Status</th>

              <th>Schedule</th>

              <th>Progress</th>

              <th>Failed</th>

              <th>Action</th>

            </tr>

          </thead>

          <tbody>

            <tr v-for="campaign in campaigns.data" :key="campaign.id">

              <td>

                <div class="flex flex-col text-left">

                  <strong>

                    {{ campaign.name }}

                  </strong>

                  <small class="text-muted">

                    {{ campaign.total_recipient }} recipients

                  </small>

                </div>

              </td>

              <td>

                <span class="status" :class="campaign.status">

                  {{ campaign.status }}

                </span>

              </td>

              <td>

                {{ formatDatetime(campaign.scheduled_at) }}

              </td>

              <td>

                <div class="progress-wrapper">

                  <div class="progress">

                    <div class="progress-fill" :style="{
                      width:
                        ((campaign.sent_count /
                          campaign.total_recipient) * 100) + '%'
                    }">

                    </div>

                  </div>

                  <span>

                    {{ campaign.sent_count }}/{{ campaign.total_recipient }}

                  </span>

                </div>

              </td>

              <td>

                {{ campaign.failed_count }}

              </td>

              <td>

                <div class="flex gap-2 justify-center">

                  <Link class="btn-outline">

                    Detail

                  </Link>

                  <button class="btn-danger">

                    Delete

                  </button>

                </div>

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
    </template>

  </AppLayout>


</template>
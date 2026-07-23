<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { stat } from 'fs'

// 🔹 Breadcrumb
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
]

const props = defineProps(['stats', 'campaigns']);

// 🔹 State
const stats = ref({
  total_campaign: 0,
  sent: 0,
  failed: 0,
  pending: 0,
})

const campaigns = ref<any[]>([]);

campaigns.value = props.campaigns;
stats.value = props.stats;

// 🔹 Status Badge
const statusClass = (status: string) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-700'
    case 'running':
      return 'bg-blue-100 text-blue-700'
    case 'pending':
      return 'bg-yellow-100 text-yellow-700'
    case 'failed':
      return 'bg-red-100 text-red-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

</script>

<template>

  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-8 p-8 bg-background min-h-screen">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-foreground">
            Email Blasting Dashboard
          </h1>

          <p class="mt-1 text-muted-foreground">
            Monitor campaign performance and delivery status.
          </p>
        </div>
      </div>

      <!-- Statistics -->
      <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">

        <div class="dashboard-card border border-2 border-white" z>
          <p>Total Campaign</p>
          <h2>{{ stats.total_campaign }}</h2>
        </div>

        <div class="dashboard-card success">
          <p>Sent</p>
          <h2>{{ stats.sent }}</h2>
        </div>

        <div class="dashboard-card danger">
          <p>Failed</p>
          <h2>{{ stats.failed }}</h2>
        </div>

        <div class="dashboard-card warning">
          <p>Pending</p>
          <h2>{{ stats.pending }}</h2>
        </div>

      </div>

      <!-- Campaign -->
      <div class="dashboard-table">

        <div class="table-header">
          <div>
            <h2>Recent Campaign</h2>
            <span>Latest email campaign activity</span>
          </div>
        </div>

        <div class="overflow-x-auto">

          <table class="dashboard-data-table">

            <thead>

              <tr>

                <th class="w-[35%]">
                  Campaign
                </th>

                <th class="w-[15%] text-center">
                  Status
                </th>

                <th class="w-[30%]">
                  Progress
                </th>

                <th class="w-[20%]">
                  Created
                </th>

              </tr>

            </thead>

            <tbody>

              <tr v-for="c in campaigns" :key="c.id">

                <!-- Campaign -->
                <td>

                  <div class="campaign-info">

                    <div class="campaign-avatar">

                      {{ c.name.charAt(0).toUpperCase() }}

                    </div>

                    <div>

                      <div class="campaign-title">

                        {{ c.name }}

                      </div>

                      <div class="campaign-subtitle">

                        {{ c.total }} recipients

                      </div>

                    </div>

                  </div>

                </td>

                <!-- Status -->
                <td class="text-center">

                  <span class="status" :class="statusClass(c.status)">

                    {{ c.status }}

                  </span>

                </td>

                <!-- Progress -->
                <td>

                  <div class="progress-wrapper">

                    <div class="progress">

                      <div class="progress-fill" :style="{
                        width:
                          ((c.sent_count / c.total) * 100) + '%'
                      }" />

                    </div>

                    <div class="progress-text">

                      {{ c.sent_count }}

                      <span>/ {{ c.total }}</span>

                    </div>

                  </div>

                </td>

                <!-- Date -->
                <td>

                  <div class="date-cell">

                    {{ c.created_at }}

                  </div>

                </td>

              </tr>

            </tbody>

          </table>
        </div>

      </div>

    </div>
  </AppLayout>
</template>
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

const props = defineProps(['stats','campaigns']);

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
    <div class="p-6 space-y-6">

      <!-- 🔹 HEADER -->
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Email Blasting Dashboard</h1>
      </div>

      <!-- 🔹 STATS -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="card">
          <p>Total Campaign</p>
          <h2>{{ stats.total_campaign }}</h2>
        </div>

        <div class="card text-green-600">
          <p>Sent</p>
          <h2>{{ stats.sent }}</h2>
        </div>

        <div class="card text-red-600">
          <p>Failed</p>
          <h2>{{ stats.failed }}</h2>
        </div>

        <div class="card text-yellow-600">
          <p>Pending</p>
          <h2>{{ stats.pending }}</h2>
        </div>

      </div>

      <!-- 🔹 CAMPAIGN TABLE -->
      <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-4">
        <h2 class="font-semibold mb-4">Recent Campaign</h2>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">

            <thead class="text-left border-b">
              <tr>
                <th class="py-2">Name</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Created</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="c in campaigns"
                :key="c.id"
                class="border-b hover:bg-gray-50 dark:hover:bg-gray-800"
              >
                <td class="py-2 font-medium">{{ c.name }}</td>

                <td>
                  <span
                    class="px-2 py-1 rounded text-xs"
                    :class="statusClass(c.status)"
                  >
                    {{ c.status }}
                  </span>
                </td>

                <td>
                  {{ c.sent_count }} / {{ c.total }}
                </td>

                <td>
                  {{ c.created_at }}
                </td>
              </tr>

              <tr v-if="campaigns.length === 0">
                <td colspan="4" class="text-center py-4 text-gray-400">
                  No data available
                </td>
              </tr>
            </tbody>

          </table>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
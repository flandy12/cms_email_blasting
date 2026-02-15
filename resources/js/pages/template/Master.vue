<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { email, templateCreate } from '@/routes';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  templates: {
    data: Array<{
      id: number
      name: string
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

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Template', href: email().url },
]

const template = ref(props.templates.data);

</script>

<template>

  <Head title="Template" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Absolute Plus Button -->
    <div class="flex justify-end">
      <Link :href="templateCreate()"
        class=" z-10 flex h-12 w-48 rounded items-center justify-center bg-primary text-black shadow hover:bg-primary/90 transition"
        aria-label="Add Template">
        <span class="mr-2">Add Template</span>
        <Plus class="h-5 w-5" />
      </Link>
    </div>

    <div class="overflow-y-auto">
      <table class="w-full h-[200px] justify-between divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">No</th>
            <th class="px-6 py-3 text-center text-xs font-medium uppercase">
              Template Name
            </th>
            <th class="px-6 py-3 text-center text-xs font-medium uppercase">
              Action
            </th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <tr v-for="(value, index) in templates.data" :key="value.id">
            <!-- NUMBER -->
            <td class="px-6 py-4">
              {{ (templates.current_page - 1) * templates.per_page + index + 1 }}
            </td>

            <!-- NAME -->
            <td class="px-6 py-4 text-center">
              {{ value.name }}
            </td>

            <!-- ACTION -->
            <td class="px-6 py-4 text-center space-x-2">
              <Link :href="`/template/${value.id}/show`">
                <button class="px-4 py-2 rounded-lg bg-primary text-black">
                  Edit
                </button>
              </Link>

              <button class="px-4 py-2 rounded-lg bg-danger">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div class="flex justify-end mt-6">
        <nav class="inline-flex space-x-1">
          <Link v-for="(link, i) in templates.links" :key="i" :href="link.url || ''" preserve-scroll preserve-state
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

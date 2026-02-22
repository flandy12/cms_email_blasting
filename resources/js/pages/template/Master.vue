<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import { computed } from 'vue'
import templates from '@/routes/templates'

interface Template {
  id: number
  name: string
  created_at: string
}

interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

const props = defineProps<{
  templates: {
    data: Template[]
    links: PaginationLink[]
    current_page: number
    per_page: number
  }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Template', href: templates.index().url },
]

const templateData = computed(() => props.templates.data)
const paginationLinks = computed(() => props.templates.links)

const startNumber = computed(() =>
  (props.templates.current_page - 1) * props.templates.per_page
)

const handleDelete = (id: number) => {
  if (!confirm('Are you sure you want to delete this template?')) {
    return
  }

  // gunakan inertia delete manual supaya clean
  router.delete(templates.delete(id).url, {
    preserveScroll: true,
    preserveState: true,
  })
}
</script>

<template>

  <Head title="Template" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Add Button -->
    <div class="flex justify-end mb-4">
      <Link :href="templates.create().url"
        class="flex h-12 w-48 rounded items-center justify-center bg-primary text-black shadow hover:bg-primary/90 transition">
        <span class="mr-2">Add Template</span>
        <Plus class="h-5 w-5" />
      </Link>
    </div>

    <div class="overflow-y-auto">
      <table class="w-full divide-y divide-gray-200">
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
          <tr v-for="(value, index) in templateData" :key="value.id" class="">
            <!-- NUMBER -->
            <td class="px-6 py-4">
              {{ startNumber + index + 1 }}
            </td>

            <!-- NAME -->
            <td class="px-6 py-4 text-center">
              {{ value.name }}
            </td>

            <!-- ACTION -->
            <td class="px-6 py-4 text-center space-x-2">
              <Link :href="templates.edit(value.id).url">
                <button class="px-4 py-2 rounded-lg bg-primary text-black hover:opacity-90">
                  Edit
                </button>
              </Link>

              <button @click="handleDelete(value.id)"
                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- PAGINATION -->
      <div class="flex justify-end mt-6">
        <nav class="inline-flex space-x-1">
          <Link v-for="(link, i) in paginationLinks" :key="i" :href="link.url || ''" preserve-scroll preserve-state
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

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Plus, Trash2 } from 'lucide-vue-next'
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

const breadcrumbs = [
  { title: 'Template', href: templates.index().url },
]

const templateData = computed(() => props.templates.data)
const paginationLinks = computed(() => props.templates.links)

const startNumber = computed(() =>
  (props.templates.current_page - 1) * props.templates.per_page
)

const deleteAll = () => {

  if (!confirm("Delete all templates?")) return

  router.delete(templates.destroyAll().url, {
    preserveScroll: true,
  })

}
</script>

<template>

  <Head title="Template" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-3 px-5">

      <div>
      <h1 class="text-2xl font-semibold">
          Template Management
        </h1>

        <p class="text-gray-500 text-sm">
          Manage your email templates
        </p>
      </div>


      <div class="flex gap-3">

        <!-- ADD -->
        <Link :href="templates.create().url"
          class="flex items-center gap-2 px-5 py-3 rounded-xl bg-primary text-black shadow hover:opacity-90 transition">
          <Plus class="h-5 w-5" />
          Add Template
        </Link>


        <!-- DELETE ALL -->
        <button v-if="templateData.length" @click="deleteAll"
          class="flex items-center gap-2 px-5 py-3 rounded-xl bg-red-500 text-white shadow hover:bg-red-600 transition">
          <Trash2 class="h-5 w-5" />
          Delete All
        </button>

      </div>

    </div>

    <!-- TABLE -->
    <div class="bg-white dark:bg-gray-700/50 shadow rounded-2xl overflow-hidden mx-5">

      <table class="w-full">

        <thead class="bg-gray-50  dark:bg-gray-600/50  border-b">

          <tr>

            <th class="px-6 py-4 text-left text-xs uppercase">
              No
            </th>

            <th class="px-6 py-4 text-center text-xs uppercase">
              Template Name
            </th>

            <th class="px-6 py-4 text-center text-xs uppercase">
              Action
            </th>

          </tr>

        </thead>


        <tbody>

          <!-- DATA -->
          <tr v-for="(value, index) in templateData" :key="value.id" class="border-b hover:bg-gray-50 transition">

            <td class="px-6 py-4">

              {{ startNumber + index + 1 }}

            </td>


            <td class="px-6 py-4 text-center font-medium">

              {{ value.name }}

            </td>


            <td class="px-6 py-4 text-center">

              <Link :href="templates.edit(value.id).url"
                class="px-4 py-2 rounded-lg bg-primary text-black hover:opacity-90 transition">
                Edit
              </Link>

            </td>

          </tr>


          <!-- EMPTY STATE -->
          <tr v-if="!templateData.length">

            <td colspan="3" class="text-center py-14 text-gray-400">

              No templates available

            </td>

          </tr>


        </tbody>

      </table>

    </div>


    <!-- PAGINATION -->
    <div v-if="paginationLinks.length > 3" class="flex justify-end mt-6">

      <nav class="flex gap-2">

        <Link v-for="(link, i) in paginationLinks" :key="i" :href="link.url || ''" preserve-scroll preserve-state
          class="px-4 py-2 border rounded-lg text-sm transition" :class="{

            'bg-primary text-black font-semibold': link.active,

            'text-gray-400 cursor-not-allowed':
              !link.url,

            'hover:bg-gray-100':
              link.url && !link.active

          }" v-html="link.label" />

      </nav>

    </div>


  </AppLayout>

</template>
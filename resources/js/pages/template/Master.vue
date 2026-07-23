<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Plus, Trash2 } from 'lucide-vue-next'
import { computed } from 'vue'
import templates from '@/routes/templates'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const permissions = computed<string[]>(() => {
  return (page.props.auth?.permissions as string[]) ?? []
})

const can = (permission: string) => {
  return permissions.value.includes(permission)
}

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

    <div v-if="can('template-campaign.view')">
      <!-- HEADER -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8 p-6">

        <div>

          <h1 class="text-3xl font-bold tracking-tight text-foreground">
            Template Management
          </h1>

          <p class="mt-2 text-sm text-muted-foreground">
            Manage and organize your email templates.
          </p>

        </div>

        <div class="flex gap-3">

          <Link v-if="can('template-campaign.create')" :href="templates.create().url" class="btn-primary">
            <Plus class="w-5 h-5" />

            Add Template
          </Link>

          <button v-if="templateData.length && can('template-campaign.delete')" @click="deleteAll" class="btn-danger">
            <Trash2 class="w-5 h-5" />

            Delete All
          </button>

        </div>

      </div>

      <!-- TABLE -->
      <div class="dashboard-table mx-6">

        <table class="dashboard-data-table">

          <thead>

            <tr>

              <th class="w-20">
                #
              </th>

              <th class="w-[70%]">
                Template
              </th>

              <th class="w-48 text-center">
                Action
              </th>

            </tr>

          </thead>

          <tbody>

            <tr v-for="(value, index) in templateData" :key="value.id">

              <!-- Number -->
              <td>

                {{ startNumber + index + 1 }}

              </td>

              <!-- Template -->
              <td>

                <div class="table-user">

                  <div class="table-avatar">

                    {{ value.name.charAt(0).toUpperCase() }}

                  </div>

                  <div>

                    <div class="table-title">

                      {{ value.name }}

                    </div>

                    <div class="table-subtitle">

                      Email Template

                    </div>

                  </div>

                </div>

              </td>

              <!-- Action -->
              <td class="text-center p-0">

                <div class="table-actions text-center">

                  <Link v-if="can('template-campaign.edit')" :href="templates.edit(value.id).url" class="btn-outline">

                    Edit

                  </Link>

                </div>

              </td>

            </tr>

            <tr v-if="!templateData.length">

              <td colspan="3" class="empty-state">

                No templates available

              </td>

            </tr>

          </tbody>

        </table>

      </div>


      <!-- PAGINATION -->
      <div v-if="paginationLinks.length > 3" class="flex justify-end mt-8">

        <nav class="flex gap-2">

          <Link v-for="(link, i) in paginationLinks" :key="i" :href="link.url || ''" preserve-scroll preserve-state
            class="pagination-button" :class="{

              active: link.active,

              disabled: !link.url

            }" v-html="link.label" />

        </nav>

      </div>
    </div>

    <div v-else class="flex items-center justify-center py-20 text-muted-foreground">
      You don't have permission to access this page.
    </div>

  </AppLayout>

</template>
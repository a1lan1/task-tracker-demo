<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { dashboard } from '@/routes'
import { showProject } from '@/routes/projects'
import { useProjectStore } from '@/stores/project'
import AppLayout from '@/layouts/AppLayout.vue'
import ProjectFormModal from '@/components/Project/ProjectFormModal.vue'
import type { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Projects',
    href: dashboard().url
  }
]

const projectStore = useProjectStore()
const { fetchAll } = projectStore

const dialog = ref(false)

onMounted(() => {
  fetchAll()
})
</script>

<template>
  <Head title="Projects" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex justify-end">
        <v-btn
          color="primary"
          density="compact"
          @click="dialog = true"
        >
          Add Project
        </v-btn>
      </div>

      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <template v-if="projectStore.projects.length">
          <Link
            v-for="p in projectStore.projects"
            :key="p.id"
            :href="showProject(p.id).url"
            class="relative block overflow-hidden rounded-xl border border-sidebar-border/70 p-4 transition-colors hover:bg-muted/50 dark:border-sidebar-border"
          >
            <div class="mb-1 text-base font-semibold">
              {{ p.name }}
            </div>
            <div class="mb-3 text-sm text-muted-foreground">
              {{ p.description || 'No description' }}
            </div>
          </Link>
        </template>
        <template v-else>
          <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <PlaceholderPattern />
          </div>
          <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <PlaceholderPattern />
          </div>
          <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <PlaceholderPattern />
          </div>
        </template>
      </div>
    </div>

    <ProjectFormModal v-model:dialog="dialog" />
  </AppLayout>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { echo } from '@laravel/echo-vue'
import { Head } from '@inertiajs/vue3'
import { dashboard } from '@/routes'
import { useTaskStore } from '@/stores/task'
import AppLayout from '@/layouts/AppLayout.vue'
import KanbanBoard from '@/components/kanban/KanbanBoard.vue'
import TaskDetailsModal from '@/components/Task/TaskDetailsModal.vue'
import TaskFormModal from '@/components/Task/TaskFormModal.vue'
import { useCommentStore } from '@/stores/comment'
import { VBtn } from 'vuetify/components'
import type { BreadcrumbItem } from '@/types'
import type { Project } from '@/types/project'
import type { Comment } from '@/types/comment'
import type { TaskStatusUpdatedEvent } from '@/types/task'

const props = defineProps<{
  project: Project
}>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Projects',
    href: dashboard().url
  },
  {
    title: props.project.name
  }
]

const taskStore = useTaskStore()
const { selectedTask } = storeToRefs(taskStore)
const { fetchAll, updateTaskStatusFromEvent } = taskStore

const commentStore = useCommentStore()
const { addComment } = commentStore

const dialog = ref(false)

onMounted(() => {
  fetchAll(props.project.id)

  echo()
    .private(`project.${props.project.id}`)
    .listen('.task-status-updated', (event: TaskStatusUpdatedEvent) => updateTaskStatusFromEvent(event))
    .listen('.comment-created', (comment: Comment) => {
      if (selectedTask.value?.id === comment.task_id) {
        addComment(comment)
      }
    })
})

onUnmounted(() => {
  echo().leave(`project.${props.project.id}`)
})
</script>

<template>
  <Head :title="project.name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex justify-end">
        <v-btn
          color="primary"
          density="compact"
          @click="dialog = true"
        >
          Add Task
        </v-btn>
      </div>

      <KanbanBoard />
      <TaskDetailsModal :members="project.members" />
      <TaskFormModal
        v-model:dialog="dialog"
        :project-id="project.id"
        :members="project.members"
      />
    </div>
  </AppLayout>
</template>

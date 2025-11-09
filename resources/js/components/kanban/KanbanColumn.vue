<script setup lang="ts">
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { VueDraggableNext as draggable, type DragChangeEvent } from 'vue-draggable-next'
import { useTaskStore } from '@/stores/task'
import KanbanTask from '@/components/kanban/KanbanTask.vue'
import type { TaskStatus } from '@/types/task'

const props = defineProps<{
  title: string
  status: TaskStatus
}>()

const taskStore = useTaskStore()
const { tasksByStatus } = storeToRefs(taskStore)
const { updateTaskStatus, setTasksFromGroupedStatus } = taskStore

const tasksForColumn = computed({
  get() {
    return tasksByStatus.value[props.status] ?? []
  },
  set(newTasks) {
    const oldTasks = tasksByStatus.value[props.status] ?? []
    // If length is same, it's a reorder within the column.
    if (newTasks.length === oldTasks.length) {
      const newTasksByStatus = { ...tasksByStatus.value }
      newTasksByStatus[props.status] = newTasks
      setTasksFromGroupedStatus(newTasksByStatus)
    }
  }
})

async function onChange(event: DragChangeEvent) {
  if (event.added) {
    const taskId = event.added.element.id
    const newStatus = props.status
    await updateTaskStatus(taskId, newStatus)
  }
}
</script>

<template>
  <div class="flex h-full flex-col gap-4 rounded-xl bg-muted/40 p-4">
    <h3 class="font-semibold">
      {{ title }}
    </h3>
    <div class="flex min-h-12 flex-1 flex-col">
      <draggable
        v-model="tasksForColumn"
        group="tasks"
        item-key="id"
        class="flex flex-col gap-4"
        @change="onChange"
      >
        <KanbanTask
          v-for="task in tasksForColumn"
          :key="task.id"
          :task="task"
          :data-task-id="task.id"
        />
      </draggable>
    </div>
  </div>
</template>

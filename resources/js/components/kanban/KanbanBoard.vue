<script setup lang="ts">
import KanbanColumn from '@/components/kanban/KanbanColumn.vue'
import { TaskStatusEnum } from '@/types/task'

const statuses = {
  [TaskStatusEnum.TODO]: 'To Do',
  [TaskStatusEnum.IN_PROGRESS]: 'In Progress',
  [TaskStatusEnum.DONE]: 'Done'
}
</script>

<template>
  <div class="grid flex-1 auto-cols-fr grid-flow-col gap-4 overflow-x-auto">
    <KanbanColumn
      v-for="(title, status) in statuses"
      :key="status"
      :title="title"
      :status="status"
    />
  </div>
</template>

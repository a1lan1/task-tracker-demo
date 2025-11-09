<script setup lang="ts">
import { useTaskStore } from '@/stores/task'
import type { Task } from '@/types/task'

defineProps<{
  task: Task
}>()

const taskStore = useTaskStore()
const { fetchTask } = taskStore
</script>

<template>
  <div
    class="cursor-pointer rounded-lg border bg-card p-4 text-card-foreground shadow-sm hover:bg-muted/50"
    @click="fetchTask(task.id)"
  >
    {{ task.title }}
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { z } from 'zod'
import {
  VDialog,
  VCard,
  VCardText,
  VTextField,
  VTextarea,
  VSelect,
  VCardActions,
  VBtn,
  VSpacer
} from 'vuetify/components'
import { useTaskStore } from '@/stores/task'
import { TaskStatusEnum, type TaskForm } from '@/types/task'
import type { User } from '@/types/user'
import { useZodValidation } from '@/composables/useZodValidation'

const props = defineProps<{
  projectId: number
  members: User[]
}>()

const taskStore = useTaskStore()
const { storing } = storeToRefs(taskStore)
const { createTask } = taskStore

const dialog = defineModel<boolean>('dialog', { required: true })

const taskSchema = z.object({
  title: z.string().min(1, 'Title is required').max(100, 'Title is too long'),
  description: z.string().nullable().transform(val => val === '' ? null : val),
  project_id: z.number(),
  status: z.enum(TaskStatusEnum),
  assignee_id: z.number().nullable()
})

const newTask = ref<TaskForm>({
  title: '',
  description: '',
  project_id: props.projectId,
  status: TaskStatusEnum.TODO,
  assignee_id: null
})

const { errors, validate, hasErrors } = useZodValidation(taskSchema, newTask)

const statusOptions = Object.values(TaskStatusEnum).map((status) => ({
  title: status.replace('_', ' '),
  value: status
}))

const submit = async() => {
  if (!validate()) return

  await createTask(newTask.value)
  dialog.value = false
  newTask.value = {
    title: '',
    description: '',
    project_id: props.projectId,
    status: TaskStatusEnum.TODO,
    assignee_id: null
  }
}
</script>

<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
  >
    <v-card title="Create Task">
      <v-card-text>
        <v-text-field
          v-model="newTask.title"
          label="Task title"
          name="title"
          :error-messages="errors.title"
        />
        <v-textarea
          v-model="newTask.description"
          label="Task description"
          name="description"
          :error-messages="errors.description"
        />
        <v-select
          v-model="newTask.status"
          :items="statusOptions"
          label="Status"
          name="status"
          item-title="title"
          item-value="value"
          :error-messages="errors.status"
        />
        <v-select
          v-model="newTask.assignee_id"
          :items="members"
          label="Assignee"
          name="assignee_id"
          item-title="name"
          item-value="id"
          clearable
          :error-messages="errors.assignee_id"
        />
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn
          text
          @click="dialog = false"
        >
          Cancel
        </v-btn>
        <v-btn
          color="success"
          variant="elevated"
          :loading="storing"
          :disabled="hasErrors"
          @click="submit"
        >
          Create
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

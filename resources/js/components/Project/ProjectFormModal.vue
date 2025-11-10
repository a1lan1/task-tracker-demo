<script setup lang="ts">
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { z } from 'zod'
import { VDialog, VCard, VCardTitle, VCardText, VBtn, VTextField, VTextarea, VCardActions, VSpacer } from 'vuetify/components'
import { useProjectStore } from '@/stores/project'
import { useZodValidation } from '@/composables/useZodValidation'
import type { ProjectForm } from '@/types/project'

const projectStore = useProjectStore()
const { storing } = storeToRefs(projectStore)
const { createProject } = projectStore

const dialog = defineModel<boolean>('dialog', { required: true })

const projectSchema = z.object({
  name: z.string()
    .min(1, 'Project name is required')
    .max(100, 'Project name is too long (maximum 100 characters)'),
  description: z.string()
    .max(1000, 'Description is too long (maximum 1000 characters)')
    .nullable()
    .transform(val => val === '' ? null : val)
})

const newProject = ref<ProjectForm>({
  name: '',
  description: ''
})

const { errors, validate, hasErrors } = useZodValidation(projectSchema, newProject)

const submit = async() => {
  if (!validate()) return

  await createProject(newProject.value)
  dialog.value = false
  newProject.value = { name: '', description: '' }
}
</script>

<template>
  <v-dialog
    v-model="dialog"
    max-width="600px"
  >
    <v-card>
      <v-card-title>
        <span class="text-h5">Create Project</span>
      </v-card-title>
      <v-card-text>
        <v-text-field
          v-model="newProject.name"
          label="Project name"
          name="name"
          :error-messages="errors.name"
        />
        <v-textarea
          v-model="newProject.description"
          label="Project description"
          name="description"
          :error-messages="errors.description"
        />
      </v-card-text>

      <v-card-actions class="pt-0">
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
          :disabled="hasErrors"
          :loading="storing"
          @click="submit"
        >
          Create
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

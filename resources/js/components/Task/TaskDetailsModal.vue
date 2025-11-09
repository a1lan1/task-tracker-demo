<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { computed, ref, watch } from 'vue'
import { z } from 'zod'
import { useTaskStore } from '@/stores/task'
import { useCommentStore } from '@/stores/comment'
import {
  VDialog,
  VCard,
  VCardTitle,
  VCardSubtitle,
  VBtn,
  VCardText,
  VTextField,
  VSkeletonLoader,
  VAvatar
} from 'vuetify/components'
import type { User } from '@/types/user'

defineProps<{
  members: User[]
}>()

const taskStore = useTaskStore()
const { selectedTask } = storeToRefs(taskStore)
const { deselectTask } = taskStore

const commentStore = useCommentStore()
const { loading, storing, comments } = storeToRefs(commentStore)
const { fetchComments, createComment, resetComments } = commentStore

const isModalOpen = computed({
  get: () => selectedTask.value !== null,
  set: (value) => {
    if (!value) {
      deselectTask()
    }
  }
})

watch(selectedTask, (newTask) => {
  if (newTask) {
    fetchComments(newTask.id)
  } else {
    resetComments()
  }
})

const commentSchema = z.object({
  body: z.string()
    .min(1, 'Comment cannot be empty')
    .max(500, 'Comment is too long (maximum 500 characters)')
})

const newCommentBody = ref('')
const commentError = ref('')

function validateComment() {
  try {
    commentSchema.parse({ body: newCommentBody.value.trim() })
    commentError.value = ''

    return true
  } catch (err) {
    if (err instanceof z.ZodError) {
      commentError.value = err.issues[0].message
    }

    return false
  }
}

async function submitComment() {
  if (!validateComment()) return

  if (selectedTask.value) {
    await createComment(selectedTask.value.id, newCommentBody.value.trim())
    newCommentBody.value = ''
    commentError.value = ''
  }
}
</script>

<template>
  <VDialog
    v-if="selectedTask"
    v-model="isModalOpen"
    max-width="800"
    class="h-[90vh]"
  >
    <VCard class="flex flex-col">
      <VCardTitle>{{ selectedTask.title }}</VCardTitle>
      <VCardText v-if="selectedTask.description">
        {{ selectedTask.description }}
      </VCardText>

      <VCardText class="flex items-center gap-4">
        <div>
          <VCardSubtitle class="!p-0">
            Assignee
          </VCardSubtitle>
          <div
            v-if="selectedTask.assignee"
            class="flex items-center gap-2 mt-2"
          >
            <VAvatar
              v-if="selectedTask.assignee.avatar"
              :image="selectedTask.assignee.avatar"
              size="32"
            />
            <span>{{ selectedTask.assignee.name }}</span>
          </div>
          <div
            v-else
            class="mt-2 text-sm text-muted-foreground"
          >
            No one assigned
          </div>
        </div>
        <div>
          <VCardSubtitle class="!p-0">
            Members
          </VCardSubtitle>
          <div class="flex items-center gap-2 mt-2">
            <VAvatar
              v-for="member in members"
              :key="member.id"
              :image="member.avatar"
              size="32"
            />
          </div>
        </div>
      </VCardText>

      <VCardSubtitle>Comments</VCardSubtitle>

      <VCardText class="flex-grow overflow-y-auto pt-0">
        <VSkeletonLoader
          v-if="loading"
          type="list-item-two-line"
        />

        <TransitionGroup
          v-if="comments.length"
          tag="div"
          name="list"
          class="space-y-4"
        >
          <div
            v-for="comment in comments"
            :key="comment.id"
            class="flex items-start gap-3"
          >
            <VAvatar
              :image="comment.user.avatar"
              size="32"
            />
            <div class="text-sm">
              <p class="font-semibold">
                {{ comment.user.name }}
              </p>
              <p>{{ comment.body }}</p>
            </div>
          </div>
        </TransitionGroup>
        <p
          v-else
          class="mt-4 text-sm text-muted-foreground"
        >
          No comments yet.
        </p>
      </VCardText>
      <div class="px-6 pb-4">
        <VTextField
          v-model="newCommentBody"
          label="Add a comment..."
          variant="outlined"
          density="compact"
          :error-messages="commentError"
          autofocus
          @keyup.enter="submitComment"
        >
          <template #append>
            <VBtn
              color="primary"
              :loading="storing"
              :disabled="!newCommentBody.trim()"
              @click="submitComment"
            >
              Submit
            </VBtn>
          </template>
        </VTextField>
      </div>
    </VCard>
  </VDialog>
</template>

<style>
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>

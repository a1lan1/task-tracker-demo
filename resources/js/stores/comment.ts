import { defineStore } from 'pinia'
import { Paginated, PaginationMeta } from '@/types/common'
import type { Comment } from '@/types/comment'

interface State {
  loading: boolean;
  storing: boolean;
  comments: Comment[];
  pagination: PaginationMeta;
}

export const useCommentStore = defineStore('comment', {
  state: (): State => ({
    loading: false,
    storing: false,
    comments: [],
    pagination: {
      current_page: 1,
      per_page: 10,
      total: 0,
      last_page: 0
    }
  }),

  actions: {
    async fetchComments(taskId: number): Promise<void> {
      try {
        this.loading = true
        const { data } = await this.$axios.get<Paginated<Comment>>(`/tasks/${taskId}/comments`)
        this.comments = data.data
        this.pagination = data.meta
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while fetching comments.'
        })
      } finally {
        this.loading = false
      }
    },
    async createComment(taskId: number, body: string): Promise<void> {
      try {
        this.storing = true
        // We just send the request. The UI will be updated via the websocket event.
        await this.$axios.post(`/tasks/${taskId}/comments`, { body })
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while creating comment.'
        })
      } finally {
        this.storing = false
      }
    },
    resetComments() {
      this.comments = []
    },
    addComment(comment: Comment) {
      if (!this.comments.some(({ id }) => id === comment.id)) {
        this.comments.unshift(comment)
      }
    }
  }
})

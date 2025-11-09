import { defineStore } from 'pinia'
import { TaskStatusEnum } from '@/types/task'
import type { Task, TaskStatus, TaskForm, TaskStatusUpdatedEvent } from '@/types/task'

interface State {
  loading: boolean;
  storing: boolean;
  tasks: Task[];
  selectedTask: Task | null;
}

export const useTaskStore = defineStore('task', {
  state: (): State => ({
    loading: false,
    storing: false,
    tasks: [],
    selectedTask: null
  }),

  getters: {
    tasksByStatus: (state): Record<TaskStatus, Task[]> => {
      const initial: Record<TaskStatus, Task[]> = {
        [TaskStatusEnum.TODO]: [],
        [TaskStatusEnum.IN_PROGRESS]: [],
        [TaskStatusEnum.DONE]: []
      }

      return state.tasks.reduce(
        (acc, task) => {
          if (acc[task.status]) {
            acc[task.status].push(task)
          }

          return acc
        },
        initial
      )
    }
  },

  actions: {
    async fetchAll(projectId: number): Promise<void> {
      try {
        this.loading = true
        const { data } = await this.$axios.get(`/projects/${projectId}/tasks`)
        this.tasks = data
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while fetching tasks.'
        })
      } finally {
        this.loading = false
      }
    },
    async fetchTask(id: number): Promise<void> {
      try {
        this.loading = true
        const { data } = await this.$axios.get(`/tasks/${id}`)
        this.selectedTask = data
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while fetching project.'
        })
      } finally {
        this.loading = false
      }
    },
    async createTask(task: TaskForm): Promise<void> {
      try {
        this.storing = true
        const { data } = await this.$axios.post('/tasks', task)
        this.upsertTask(data)
        this.$snackbar.success({
          text: 'Task created successfully!'
        })
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while creating the task.'
        })
      } finally {
        this.storing = false
      }
    },
    async updateTask(taskId: number, payload: Partial<Pick<Task, 'title' | 'description' | 'status' | 'assignee_id'>>) {
      try {
        this.storing = true
        const { data } = await this.$axios.patch(`/tasks/${taskId}`, payload)
        this.upsertTask(data)
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while updating task.'
        })
      } finally {
        this.storing = false
      }
    },
    async updateTaskStatus(taskId: number, status: TaskStatus) {
      const task = this.tasks.find(({ id }) => id === taskId)

      if (task && task.status !== status) {
        const oldStatus = task.status
        task.status = status // Optimistic update

        try {
          this.storing = true
          await this.$axios.patch(`/tasks/status/${taskId}`, { status })
        } catch (e: any) {
          task.status = oldStatus // Revert on failure

          this.$snackbar.error({
            text: e.message || 'An error occurred while updating task status.'
          })
        } finally {
          this.storing = false
        }
      }
    },
    setTasksFromGroupedStatus(groupedTasks: Record<TaskStatus, Task[]>) {
      this.tasks = Object.values(groupedTasks).flat()
    },
    updateTaskStatusFromEvent(event: TaskStatusUpdatedEvent) {
      const task = this.tasks.find((t) => t.id === event.task_id)
      if (task) {
        task.status = event.new_status
      }
    },
    upsertTask(taskData: Task) {
      const index = this.tasks.findIndex((t) => t.id === taskData.id)
      if (index !== -1) {
        this.tasks[index] = { ...this.tasks[index], ...taskData }
      } else {
        this.tasks.push(taskData)
      }
    },
    deselectTask() {
      this.selectedTask = null
    }
  }
})

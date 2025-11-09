import type { User } from './user'

export enum TaskStatusEnum {
  TODO = 'todo',
  IN_PROGRESS = 'in_progress',
  DONE = 'done'
}

export type Task = {
  id: number
  project_id: number
  title: string
  description: string | null
  status: TaskStatusEnum
  assignee_id: number | null
  assignee: User | null
}

export type TaskForm = {
  project_id: number
  title: string
  description: string | null
  status: TaskStatusEnum
  assignee_id: number | null
}

export interface TaskStatusUpdatedEvent {
  task_id: number
  project_id: number
  old_status: TaskStatusEnum
  new_status: TaskStatusEnum
}

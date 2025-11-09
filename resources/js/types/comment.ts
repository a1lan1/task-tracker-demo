import type { User } from './index'

export type Comment = {
  id: number
  task_id: number
  body: string
  user: User
}

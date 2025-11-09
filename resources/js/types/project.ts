import type { User } from './user'

export type Project = {
  id: number
  name: string
  description: string | null
  owner_id: number
  members: User[]
}

export type ProjectForm = {
  name: string
  description: string | null
}

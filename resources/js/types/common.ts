export type PaginationMeta = {
  current_page: number
  per_page: number
  total: number
  last_page: number
}

export type Paginated<T> = {
  data: T[]
  meta: PaginationMeta
}

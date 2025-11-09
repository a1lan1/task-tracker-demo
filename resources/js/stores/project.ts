import { defineStore } from 'pinia'
import type { Project, ProjectForm } from '@/types/project'

interface State {
  loading: boolean;
  storing: boolean;
  searching: boolean;
  projects: Project[];
  current: Project | null;
}

export const useProjectStore = defineStore('project', {
  state: (): State => ({
    loading: false,
    storing: false,
    searching: false,  projects: [],
    current: null
  }),

  actions: {
    async fetchAll(): Promise<void> {
      try {
        this.loading = true
        const { data } = await this.$axios.get('/projects')
        this.projects = data
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while fetching projects.'
        })
      } finally {
        this.loading = false
      }
    },
    async fetchOne(id: number): Promise<void> {
      try {
        this.loading = true
        const { data } = await this.$axios.get(`/projects/${id}`)
        this.current = data
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while fetching project.'
        })
      } finally {
        this.loading = false
      }
    },
    async createProject(project: ProjectForm): Promise<void> {
      try {
        this.storing = true
        await this.$axios.post('/projects', project)
        await this.fetchAll()
        this.$snackbar.success({
          text: 'Project created successfully!'
        })
      } catch (e: any) {
        this.$snackbar.error({
          text: e.message || 'An error occurred while creating the project.'
        })
      } finally {
        this.storing = false
      }
    }
  }
})

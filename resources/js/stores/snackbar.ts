import { defineStore } from 'pinia'

export type SnackbarColor = 'success' | 'info' | 'warning' | 'error'

export interface SnackbarMessage {
  text: string;
  color?: SnackbarColor;
  timeout?: number;
}

interface State {
  messages: SnackbarMessage[]
}

export const useSnackbarStore = defineStore('snackbar', {
  state: (): State => ({
    messages: []
  }),

  actions: {
    showMessage(item: SnackbarMessage) {
      const defaults: Partial<SnackbarMessage> = {
        color: 'info',
        timeout: 5000
      }
      this.messages.push({
        ...defaults,
        ...item
      })
    }
  }
})

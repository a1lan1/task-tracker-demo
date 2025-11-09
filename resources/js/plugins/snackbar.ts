import { useSnackbarStore, type SnackbarMessage, type SnackbarColor } from '@/stores/snackbar'
import type { App } from 'vue'
import { type PiniaPluginContext } from 'pinia'

interface SnackbarShortcuts {
  (options: Omit<SnackbarMessage, 'color'>, color: SnackbarColor): void
  success(options: Omit<SnackbarMessage, 'color'>): void
  info(options: Omit<SnackbarMessage, 'color'>): void
  warning(options: Omit<SnackbarMessage, 'color'>): void
  error(options: Omit<SnackbarMessage, 'color'>): void
}

export const snackbar: SnackbarShortcuts = (options, color) => {
  const snackbarStore = useSnackbarStore()
  snackbarStore.showMessage({ ...options, color })
}

export function piniaSnackbarPlugin({ store }: PiniaPluginContext) {
  store.$snackbar = snackbar
}

snackbar.success = options => snackbar(options, 'success')
snackbar.info = options => snackbar(options, 'info')
snackbar.warning = options => snackbar(options, 'warning')
snackbar.error = options => snackbar(options, 'error')

export default {
  install(app: App) {
    app.config.globalProperties.$snackbar = snackbar
  }
}

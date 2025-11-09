import type { App } from 'vue'
import axios from './axios'
import pinia from './pinia'
import vuetify from './vuetify'
import snackbar from './snackbar'

export function registerPlugins(app: App) {
  app
    .use(pinia)
    .use(axios)
    .use(vuetify)
    .use(snackbar)
}

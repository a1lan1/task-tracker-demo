import { createPinia } from 'pinia'
import piniaPersist from 'pinia-plugin-persistedstate'
import { piniaAxiosPlugin } from '@/plugins/axios'

const pinia = createPinia()

pinia
  .use(piniaPersist)
  .use(piniaAxiosPlugin)

export default pinia

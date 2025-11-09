import 'pinia'
import type { AxiosInstance } from 'axios'

declare module 'pinia' {
  export interface PiniaCustomProperties {
    $axios: AxiosInstance;
  }
}

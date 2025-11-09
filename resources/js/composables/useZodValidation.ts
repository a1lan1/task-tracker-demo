import { ref, computed, watch, type Ref } from 'vue'
import { z, type ZodSchema } from 'zod'

export function useZodValidation<T extends ZodSchema>(
  schema: T,
  formData: Ref<z.infer<T>>
) {
  const errors = ref<Record<string, string>>({})

  const validate = () => {
    errors.value = {}
    try {
      schema.parse(formData.value)

      return true
    } catch (err) {
      if (err instanceof z.ZodError) {
        err.issues.forEach((error) => {
          const path = error.path.join('.')
          errors.value[path] = error.message
        })
      }

      return false
    }
  }

  const hasErrors = computed(() => Object.keys(errors.value).length > 0)

  watch(
    formData,
    () => {
      errors.value = {}
    },
    { deep: true }
  )

  return {
    errors,
    validate,
    hasErrors
  }
}

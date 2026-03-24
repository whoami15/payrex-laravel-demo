<script setup>
import { cn } from "@/lib/utils"
import { alertVariants } from "."

const props = defineProps({
  class: { type: [String, Object, Array], default: undefined },
  variant: { type: String, default: undefined },
})
</script>

<template>
  <div
    data-slot="alert"
    :class="cn(alertVariants({ variant }), props.class)"
    role="alert"
  >
    <slot />
  </div>
</template>

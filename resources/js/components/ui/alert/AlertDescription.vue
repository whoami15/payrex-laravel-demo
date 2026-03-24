<script setup>
import { cn } from "@/lib/utils"

const props = defineProps({
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <div
    data-slot="alert-description"
    :class="cn('text-muted-foreground col-start-2 grid justify-items-start gap-1 text-sm [&_p]:leading-relaxed', props.class)"
  >
    <slot />
  </div>
</template>

<script setup>
import { cn } from "@/lib/utils"

const props = defineProps({
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <ol
    data-slot="breadcrumb-list"
    :class="cn('text-muted-foreground flex flex-wrap items-center gap-1.5 text-sm break-words sm:gap-2.5', props.class)"
  >
    <slot />
  </ol>
</template>

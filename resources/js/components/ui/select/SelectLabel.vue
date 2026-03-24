<script setup>
import { SelectLabel } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps({
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
  for: { type: String, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <SelectLabel
    data-slot="select-label"
    :class="cn('text-muted-foreground px-2 py-1.5 text-xs', props.class)"
  >
    <slot />
  </SelectLabel>
</template>

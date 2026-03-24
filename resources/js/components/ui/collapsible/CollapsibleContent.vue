<script setup>
import { CollapsibleContent } from "reka-ui"

const props = defineProps({
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
  forceMount: { type: Boolean, default: undefined },
})
</script>

<template>
  <CollapsibleContent
    data-slot="collapsible-content"
    v-bind="props"
  >
    <slot />
  </CollapsibleContent>
</template>

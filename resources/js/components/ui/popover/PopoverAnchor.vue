<script setup>
import { PopoverAnchor } from "reka-ui"

const props = defineProps({
  reference: { type: Object, default: undefined },
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
})
</script>

<template>
  <PopoverAnchor
    data-slot="popover-anchor"
    v-bind="props"
  >
    <slot />
  </PopoverAnchor>
</template>

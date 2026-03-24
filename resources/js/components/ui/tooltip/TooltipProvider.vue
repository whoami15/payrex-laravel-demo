<script setup>
import { TooltipProvider } from "reka-ui"

const props = defineProps({
  delayDuration: { type: Number, default: 0 },
  skipDelayDuration: { type: Number, default: undefined },
  disableHoverableContent: { type: Boolean, default: undefined },
  disableClosingTrigger: { type: Boolean, default: undefined },
})
</script>

<template>
  <TooltipProvider v-bind="props">
    <slot />
  </TooltipProvider>
</template>

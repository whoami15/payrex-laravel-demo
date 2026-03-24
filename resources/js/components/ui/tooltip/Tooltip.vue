<script setup>
import { TooltipRoot, useForwardPropsEmits } from "reka-ui"

const props = defineProps({
  defaultOpen: { type: Boolean, default: undefined },
  open: { type: Boolean, default: undefined },
  delayDuration: { type: Number, default: undefined },
  disableHoverableContent: { type: Boolean, default: undefined },
  disableClosingTrigger: { type: Boolean, default: undefined },
})
const emits = defineEmits(["update:open"])

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <TooltipRoot
    v-slot="slotProps"
    data-slot="tooltip"
    v-bind="forwarded"
  >
    <slot v-bind="slotProps" />
  </TooltipRoot>
</template>

<script setup>
import { PopoverRoot, useForwardPropsEmits } from "reka-ui"

const props = defineProps({
  defaultOpen: { type: Boolean, default: undefined },
  open: { type: Boolean, default: undefined },
  modal: { type: Boolean, default: undefined },
})
const emits = defineEmits(["update:open"])

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <PopoverRoot
    v-slot="slotProps"
    data-slot="popover"
    v-bind="forwarded"
  >
    <slot v-bind="slotProps" />
  </PopoverRoot>
</template>

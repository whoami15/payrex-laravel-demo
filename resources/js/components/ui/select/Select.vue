<script setup>
import { SelectRoot, useForwardPropsEmits } from "reka-ui"

const props = defineProps({
  open: { type: Boolean, default: undefined },
  defaultOpen: { type: Boolean, default: undefined },
  defaultValue: { type: [String, Number, Boolean, Object, Array], default: undefined },
  modelValue: { type: [String, Number, Boolean, Object, Array], default: undefined },
  by: { type: [String, Function], default: undefined },
  dir: { type: String, default: undefined },
  multiple: { type: Boolean, default: undefined },
  autocomplete: { type: String, default: undefined },
  disabled: { type: Boolean, default: undefined },
  name: { type: String, default: undefined },
  required: { type: Boolean, default: undefined },
})
const emits = defineEmits(["update:modelValue", "update:open"])

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <SelectRoot
    v-slot="slotProps"
    data-slot="select"
    v-bind="forwarded"
  >
    <slot v-bind="slotProps" />
  </SelectRoot>
</template>

<script setup>
import { reactiveOmit } from "@vueuse/core"
import { SelectSeparator } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps({
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
})

const delegatedProps = reactiveOmit(props, "class")
</script>

<template>
  <SelectSeparator
    data-slot="select-separator"
    v-bind="delegatedProps"
    :class="cn('bg-border pointer-events-none -mx-1 my-1 h-px', props.class)"
  />
</template>

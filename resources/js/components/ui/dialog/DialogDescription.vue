<script setup>
import { reactiveOmit } from "@vueuse/core"
import { DialogDescription, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps({
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
})

const delegatedProps = reactiveOmit(props, "class")

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <DialogDescription
    data-slot="dialog-description"
    v-bind="forwardedProps"
    :class="cn('text-muted-foreground text-sm', props.class)"
  >
    <slot />
  </DialogDescription>
</template>

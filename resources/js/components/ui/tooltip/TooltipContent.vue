<script setup>
import { reactiveOmit } from "@vueuse/core"
import { TooltipArrow, TooltipContent, TooltipPortal, useForwardPropsEmits } from "reka-ui"
import { cn } from "@/lib/utils"

defineOptions({
  inheritAttrs: false,
})

const props = defineProps({
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
  forceMount: { type: Boolean, default: undefined },
  side: { type: String, default: undefined },
  sideOffset: { type: Number, default: 4 },
  align: { type: String, default: undefined },
  alignOffset: { type: Number, default: undefined },
  avoidCollisions: { type: Boolean, default: undefined },
  collisionBoundary: { type: [Object, Array], default: undefined },
  collisionPadding: { type: [Number, Object], default: undefined },
  sticky: { type: String, default: undefined },
  hideWhenDetached: { type: Boolean, default: undefined },
  arrowPadding: { type: Number, default: undefined },
  hidden: { type: Boolean, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
})

const emits = defineEmits(["escapeKeyDown", "pointerDownOutside"])

const delegatedProps = reactiveOmit(props, "class")
const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <TooltipPortal>
    <TooltipContent
      data-slot="tooltip-content"
      v-bind="{ ...forwarded, ...$attrs }"
      :class="cn('bg-foreground text-background animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 w-fit rounded-lg px-3 py-1.5 text-xs font-medium text-balance shadow-lg', props.class)"
    >
      <slot />

      <TooltipArrow class="bg-foreground fill-foreground z-50 size-2.5 translate-y-[calc(-50%_-_2px)] rotate-45 rounded-[2px]" />
    </TooltipContent>
  </TooltipPortal>
</template>

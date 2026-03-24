<script setup>
import { reactiveOmit } from "@vueuse/core"
import {
  DropdownMenuContent,
  DropdownMenuPortal,
  useForwardPropsEmits,
} from "reka-ui"
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
  loop: { type: Boolean, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
})
const emits = defineEmits([
  "escapeKeyDown",
  "pointerDownOutside",
  "focusOutside",
  "interactOutside",
  "closeAutoFocus",
])

const delegatedProps = reactiveOmit(props, "class")

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <DropdownMenuPortal>
    <DropdownMenuContent
      data-slot="dropdown-menu-content"
      v-bind="{ ...$attrs, ...forwarded }"
      :class="cn('bg-popover/95 backdrop-blur-xl text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 max-h-(--reka-dropdown-menu-content-available-height) min-w-[8rem] origin-(--reka-dropdown-menu-content-transform-origin) overflow-x-hidden overflow-y-auto rounded-xl border p-1.5 shadow-lg shadow-black/[0.08] dark:shadow-black/[0.3]', props.class)"
    >
      <slot />
    </DropdownMenuContent>
  </DropdownMenuPortal>
</template>

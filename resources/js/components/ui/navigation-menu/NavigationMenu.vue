<script setup>
import { reactiveOmit } from "@vueuse/core"
import {
  NavigationMenuRoot,
  useForwardPropsEmits,
} from "reka-ui"
import { cn } from "@/lib/utils"
import NavigationMenuViewport from "./NavigationMenuViewport.vue"

const props = defineProps({
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
  modelValue: { type: String, default: undefined },
  defaultValue: { type: String, default: undefined },
  dir: { type: String, default: undefined },
  orientation: { type: String, default: undefined },
  delayDuration: { type: Number, default: undefined },
  skipDelayDuration: { type: Number, default: undefined },
  disableClickTrigger: { type: Boolean, default: undefined },
  disablePointerTrigger: { type: Boolean, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
  viewport: { type: Boolean, default: true },
})
const emits = defineEmits(["update:modelValue"])

const delegatedProps = reactiveOmit(props, "class", "viewport")
const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <NavigationMenuRoot
    v-slot="slotProps"
    data-slot="navigation-menu"
    :data-viewport="viewport"
    v-bind="forwarded"
    :class="cn('group/navigation-menu relative flex max-w-max flex-1 items-center justify-center', props.class)"
  >
    <slot v-bind="slotProps" />
    <NavigationMenuViewport v-if="viewport" />
  </NavigationMenuRoot>
</template>

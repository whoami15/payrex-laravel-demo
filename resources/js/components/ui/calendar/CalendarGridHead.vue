<script setup>
import { CalendarGridHead } from "reka-ui"

const props = defineProps({
  as: { type: [String, Object], default: undefined },
  asChild: { type: Boolean, default: undefined },
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <CalendarGridHead
    data-slot="calendar-grid-head"
    v-bind="props"
  >
    <slot />
  </CalendarGridHead>
</template>

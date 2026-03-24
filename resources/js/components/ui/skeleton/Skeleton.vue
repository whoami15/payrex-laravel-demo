<script setup>
import { cn } from "@/lib/utils"

const props = defineProps({
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <div
    data-slot="skeleton"
    :class="cn('animate-pulse rounded-lg bg-gradient-to-r from-primary/[0.06] via-primary/[0.12] to-primary/[0.06] bg-[length:200%_100%]', props.class)"
  />
</template>

<script setup>
import { Loader2Icon } from "lucide-vue-next"
import { cn } from "@/lib/utils"

const props = defineProps({
  class: { type: [String, Object, Array], default: undefined },
})
</script>

<template>
  <Loader2Icon
    role="status"
    aria-label="Loading"
    :class="cn('size-4 animate-spin text-current drop-shadow-[0_0_3px_rgba(106,99,239,0.3)]', props.class)"
  />
</template>

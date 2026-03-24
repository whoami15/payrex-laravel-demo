<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    class: { type: [String, Object, Array], default: undefined },
});
</script>

<template>
    <h3
        data-slot="card-title"
        :class="cn('leading-none font-semibold tracking-tight', props.class)"
    >
        <slot />
    </h3>
</template>

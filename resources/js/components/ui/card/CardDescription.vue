<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    class: { type: [String, Object, Array], default: undefined },
});
</script>

<template>
    <p
        data-slot="card-description"
        :class="cn('text-muted-foreground text-sm', props.class)"
    >
        <slot />
    </p>
</template>

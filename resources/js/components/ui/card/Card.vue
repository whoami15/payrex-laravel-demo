<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    class: { type: [String, Object, Array], default: undefined },
});
</script>

<template>
    <div
        data-slot="card"
        :class="
            cn(
                'bg-card text-card-foreground flex flex-col rounded-xl border border-border/50 overflow-hidden dark:border-border/30',
                props.class,
            )
        "
    >
        <slot />
    </div>
</template>

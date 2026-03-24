<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    class: { type: [String, Object, Array], default: undefined },
});
</script>

<template>
    <div
        data-slot="card-footer"
        :class="cn('flex items-center px-6 [.border-t]:pt-6', props.class)"
    >
        <slot />
    </div>
</template>

<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    class: { type: [String, Object, Array], default: undefined },
});
</script>

<template>
    <div data-slot="card-content" :class="cn('px-6 pt-5 pb-6', props.class)">
        <slot />
    </div>
</template>

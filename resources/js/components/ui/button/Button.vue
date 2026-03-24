<script setup>
import { Primitive } from 'reka-ui';
import { cn } from '@/lib/utils';
import { buttonVariants } from '.';

const props = defineProps({
    variant: { type: String, default: undefined },
    size: { type: String, default: undefined },
    class: { type: [String, Object, Array], default: undefined },
    as: { type: String, default: 'button' },
    asChild: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});
</script>

<template>
    <Primitive
        data-slot="button"
        :data-variant="variant"
        :data-size="size"
        :as="as"
        :as-child="asChild"
        :disabled="disabled"
        :class="cn(buttonVariants({ variant, size }), props.class)"
    >
        <slot />
    </Primitive>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

defineProps({
    icon: { type: Object, required: true },
    title: { type: String, required: true },
    description: { type: String, default: undefined },
    actionLabel: { type: String, default: undefined },
    actionHref: { type: String, default: undefined },
});
</script>

<template>
    <div class="flex flex-col items-center gap-3 py-12 text-center">
        <component :is="icon" class="size-10 text-muted-foreground/50" />
        <div>
            <p class="text-sm font-medium">{{ title }}</p>
            <p v-if="description" class="mt-1 text-sm text-muted-foreground">
                {{ description }}
            </p>
        </div>
        <Button
            v-if="actionLabel && actionHref"
            as-child
            variant="outline"
            size="sm"
        >
            <Link :href="actionHref">{{ actionLabel }}</Link>
        </Button>
    </div>
</template>

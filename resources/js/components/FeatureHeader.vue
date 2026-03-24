<script setup>
import SourceLinks from '@/components/SourceLinks.vue';

defineProps({
    title: { type: String, required: true },
    docs: { type: String, default: undefined },
    controller: { type: String, default: undefined },
});
</script>

<template>
    <div class="animate-fade-in-up">
        <h1 class="text-2xl font-semibold tracking-tight">
            {{ title }}
        </h1>
        <p class="mt-1 max-w-2xl text-sm leading-relaxed text-muted-foreground">
            <slot />
        </p>
        <SourceLinks :docs="docs" :controller="controller" />
    </div>
</template>

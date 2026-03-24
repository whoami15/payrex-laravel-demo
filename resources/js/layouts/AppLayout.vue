<script setup>
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';

defineProps({
    breadcrumbs: { type: Array, default: () => [] },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            v-if="subtitle"
            data-test="layout-subtitle"
            class="border-b border-sidebar-border/70 bg-primary/5 px-6 py-2 text-sm text-primary md:px-4"
        >
            {{ subtitle }}
        </div>
        <slot />
    </AppLayout>
</template>

<script setup>
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';

defineProps({
    title: { type: String, default: undefined },
    description: { type: String, default: undefined },
});
</script>

<template>
    <AuthLayout :title="title" :description="description">
        <slot />
    </AuthLayout>
</template>

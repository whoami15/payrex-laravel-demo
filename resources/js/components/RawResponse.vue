<script setup>
import CodeBlock from '@/components/CodeBlock.vue';

defineProps({
    data: { type: Object, required: true },
    title: { type: String, default: 'Raw API Response' },
});
</script>

<template>
    <CodeBlock
        lang="json"
        :title="title"
        :code="JSON.stringify(data, null, 2)"
    />
</template>

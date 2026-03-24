<script setup>
defineOptions({
    inheritAttrs: false,
});

defineProps({
    className: { type: [String, Object, Array], default: undefined },
});
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 60 60"
        fill="none"
        :class="className"
        v-bind="$attrs"
    >
        <circle cx="30" cy="30" r="30" fill="currentColor" />
    </svg>
</template>

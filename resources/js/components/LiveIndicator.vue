<script setup>
defineProps({
    label: { type: String, default: 'Live' },
});
</script>

<template>
    <div class="flex items-center gap-1.5">
        <span class="relative flex size-2">
            <span
                class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-75"
            />
            <span
                class="relative inline-flex size-2 rounded-full bg-emerald-500"
            />
        </span>
        <span
            class="text-xs font-medium text-emerald-600 dark:text-emerald-400"
        >
            {{ label }}
        </span>
    </div>
</template>

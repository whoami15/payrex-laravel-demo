<script setup>
import { computed } from 'vue';

const props = defineProps({
    event: { type: String, required: true },
});

const colors = {
    green: 'bg-green-50 text-green-700 ring-green-200 dark:bg-green-900/30 dark:text-green-300 dark:ring-green-800',
    red: 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-800',
    amber: 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800',
    blue: 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-800',
    sky: 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-900/30 dark:text-sky-300 dark:ring-sky-800',
    neutral:
        'bg-neutral-100 text-neutral-700 ring-neutral-200 dark:bg-neutral-900/30 dark:text-neutral-300 dark:ring-neutral-800',
    violet: 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:ring-violet-800',
    emerald:
        'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800',
};

const colorClass = computed(() => {
    const e = props.event;

    if (
        e.endsWith('.succeeded') ||
        e.endsWith('.completed') ||
        e.endsWith('.paid')
    )
        return colors.green;

    if (
        e.endsWith('.failed') ||
        e.endsWith('.voided') ||
        e.endsWith('.uncollectible') ||
        e.endsWith('.deleted')
    )
        return colors.red;

    if (
        e.endsWith('.expired') ||
        e.endsWith('.will_be_due') ||
        e.endsWith('.overdue') ||
        e.endsWith('.marked_uncollectible')
    )
        return colors.amber;

    if (e.startsWith('refund.')) return colors.blue;

    if (
        e.endsWith('.created') ||
        e.endsWith('.updated') ||
        e.endsWith('.finalized') ||
        e.endsWith('.sent')
    )
        return colors.sky;

    if (e.endsWith('.disabled')) return colors.neutral;

    if (e.endsWith('.amount_capturable')) return colors.violet;

    if (e.startsWith('payout.')) return colors.emerald;

    return 'bg-muted text-muted-foreground ring-border';
});
</script>

<template>
    <span
        :class="colorClass"
        class="inline-flex items-center rounded-md px-2 py-0.5 font-mono text-[11px] font-medium ring-1 ring-inset"
    >
        {{ event }}
    </span>
</template>

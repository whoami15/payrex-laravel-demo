<script setup>
import { Check } from 'lucide-vue-next';

const props = defineProps({
    options: { type: Array, required: true },
    selected: { type: Array, required: true },
});

const emit = defineEmits(['toggle']);

function isSelected(value) {
    return props.selected.includes(value);
}
</script>

<template>
    <div class="grid grid-cols-2 gap-2 lg:grid-cols-4">
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            :class="[
                'relative flex flex-col items-center gap-1.5 rounded-lg border px-3 py-3 text-center transition-all duration-150',
                isSelected(option.value)
                    ? 'border-foreground/70 text-foreground shadow-[inset_0_0_0_0.5px_rgba(0,0,0,0.3)]'
                    : 'border-input/60 text-muted-foreground/80 hover:border-foreground/30',
            ]"
            @click="emit('toggle', option.value)"
        >
            <Check
                v-if="isSelected(option.value)"
                class="absolute top-1.5 right-1.5 size-4 stroke-[2.5]"
            />
            <component :is="option.icon" v-if="option.icon" class="size-5" />
            <span class="text-xs font-medium">{{ option.label }}</span>
        </button>
    </div>
</template>

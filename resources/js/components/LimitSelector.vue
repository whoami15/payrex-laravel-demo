<script setup>
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

defineProps({
    modelValue: { type: String, required: true },
});

const emit = defineEmits(['update:modelValue']);

const options = ['5', '10', '25', '50', '100'];
</script>

<template>
    <Select
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <SelectTrigger class="w-[110px]">
            <SelectValue placeholder="Limit" />
        </SelectTrigger>
        <SelectContent>
            <SelectItem v-for="opt in options" :key="opt" :value="opt">
                {{ opt }} rows
            </SelectItem>
        </SelectContent>
    </Select>
</template>

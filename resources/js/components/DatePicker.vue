<script setup>
import { CalendarDate } from '@internationalized/date';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

const props = defineProps({
    modelValue: { type: String, default: '' }, // YYYY-MM-DD string
    placeholder: { type: String, default: 'Pick a date' },
});

const emit = defineEmits(['update:modelValue']);

const calendarValue = computed({
    get() {
        if (!props.modelValue) return undefined;
        const [y, m, d] = props.modelValue.split('-').map(Number);
        return new CalendarDate(y, m, d);
    },
    set(val) {
        if (!val) {
            emit('update:modelValue', '');
            return;
        }
        const y = String(val.year);
        const m = String(val.month).padStart(2, '0');
        const d = String(val.day).padStart(2, '0');
        emit('update:modelValue', `${y}-${m}-${d}`);
    },
});

const displayDate = computed(() => {
    if (!props.modelValue) return '';
    const date = new Date(props.modelValue + 'T00:00:00');
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="
                    cn(
                        'w-full justify-start text-left font-normal',
                        !modelValue && 'text-muted-foreground',
                    )
                "
            >
                <CalendarIcon class="mr-2 size-4" />
                {{ displayDate || placeholder }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model="calendarValue" />
        </PopoverContent>
    </Popover>
</template>

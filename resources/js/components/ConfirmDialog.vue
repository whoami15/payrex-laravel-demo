<script setup>
import { AlertTriangle } from 'lucide-vue-next';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';

defineProps({
    title: { type: String, default: 'Are you sure?' },
    description: { type: String, default: 'This action cannot be undone.' },
    confirmLabel: { type: String, default: 'Continue' },
    cancelLabel: { type: String, default: 'Cancel' },
    variant: { type: String, default: 'default' },
});

const emit = defineEmits(['confirm']);
</script>

<template>
    <AlertDialog>
        <AlertDialogTrigger as-child>
            <slot />
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <div class="flex gap-4">
                    <div
                        v-if="variant === 'destructive'"
                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-destructive/10"
                    >
                        <AlertTriangle class="size-5 text-destructive" />
                    </div>
                    <div>
                        <AlertDialogTitle>{{ title }}</AlertDialogTitle>
                        <AlertDialogDescription class="mt-1">
                            {{ description }}
                        </AlertDialogDescription>
                    </div>
                </div>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>{{ cancelLabel }}</AlertDialogCancel>
                <AlertDialogAction
                    :class="
                        variant === 'destructive'
                            ? 'bg-destructive text-white hover:bg-destructive/90'
                            : ''
                    "
                    @click="emit('confirm')"
                >
                    {{ confirmLabel }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

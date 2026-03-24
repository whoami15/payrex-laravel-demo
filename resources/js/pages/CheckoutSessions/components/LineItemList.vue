<script setup>
import { Minus, Plus, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { formatPesos } from '@/lib/formatters';

const props = defineProps({
    lineItems: { type: Array, required: true },
});

const emit = defineEmits(['remove']);

const reversedItems = computed(() =>
    props.lineItems.map((item, index) => ({ item, index })).reverse(),
);
</script>

<template>
    <div class="space-y-4">
        <p
            v-if="lineItems.length === 0"
            class="rounded-lg border border-dashed p-5 text-center text-sm text-muted-foreground"
        >
            Select products above to build your checkout.
        </p>

        <div
            v-if="lineItems.length > 0"
            class="relative overflow-hidden rounded-lg border"
        >
            <div
                class="max-h-40 divide-y overflow-y-auto"
                :class="{ 'pb-8': lineItems.length > 1 }"
            >
                <TransitionGroup name="line-item">
                    <div
                        v-for="{ item, index } in reversedItems"
                        :key="item.name + '-' + index"
                        class="flex items-center gap-3 overflow-hidden p-3"
                    >
                        <img
                            v-if="item.image"
                            :src="item.image"
                            :alt="item.name"
                            class="hidden size-10 shrink-0 rounded-md border object-cover sm:block"
                        />

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">
                                {{ item.name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ formatPesos(item.amount) }} each
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-7"
                                :disabled="item.quantity <= 1"
                                @click="item.quantity--"
                            >
                                <Minus class="size-3" />
                            </Button>
                            <span class="w-8 text-center text-sm font-medium">
                                {{ item.quantity }}
                            </span>
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                class="size-7"
                                @click="item.quantity++"
                            >
                                <Plus class="size-3" />
                            </Button>
                        </div>

                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            class="size-7 shrink-0"
                            @click="emit('remove', index)"
                        >
                            <Trash2 class="size-3 text-destructive" />
                        </Button>
                    </div>
                </TransitionGroup>
            </div>
            <div
                v-if="lineItems.length > 1"
                class="pointer-events-none absolute inset-x-0 bottom-0 flex h-8 items-center justify-center border-t bg-background/80 text-xs text-muted-foreground backdrop-blur-sm"
            >
                Scroll for more items
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-item-move,
.line-item-enter-active {
    transition: all 0.25s cubic-bezier(0.23, 1, 0.32, 1);
}

.line-item-leave-active {
    transition: all 0.2s ease-in;
    position: absolute;
    width: 100%;
}

.line-item-enter-from {
    opacity: 0;
    transform: translateY(-12px);
}

.line-item-leave-to {
    opacity: 0;
    transform: translateX(12px);
}
</style>

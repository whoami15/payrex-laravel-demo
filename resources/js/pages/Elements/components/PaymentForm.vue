<script setup>
import { Loader2, Lock } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import { Button } from '@/components/ui/button';

const props = defineProps({
    payrexInstance: { type: Object, required: true },
    elementsInstance: { type: Object, required: true },
    returnUrl: { type: String, required: true },
    formattedAmount: { type: String, required: true },
    description: { type: String, required: true },
    brandColor: { type: String, required: true },
});

const emit = defineEmits(['cancel']);

const paying = shallowRef(false);
const errorMessage = shallowRef('');

async function confirmPayment() {
    if (!props.payrexInstance || !props.elementsInstance) return;

    paying.value = true;
    errorMessage.value = '';

    try {
        const result = await props.payrexInstance.attachPaymentMethod({
            elements: props.elementsInstance,
            options: {
                return_url: props.returnUrl,
            },
        });

        if (result?.error) {
            errorMessage.value = result.error.message;
        }
    } catch (e) {
        errorMessage.value = e.message || 'Payment failed.';
    } finally {
        paying.value = false;
    }
}
</script>

<template>
    <div class="rounded-xl border bg-card">
        <div class="flex items-center justify-between p-5">
            <div>
                <p class="text-sm font-semibold">Complete Payment</p>
                <p class="mt-0.5 text-sm text-muted-foreground">
                    {{ formattedAmount }} - {{ description }}
                </p>
            </div>
            <Button variant="outline" size="sm" @click="emit('cancel')">
                Cancel
            </Button>
        </div>

        <div class="border-t p-5">
            <div
                class="rounded-lg border p-4"
                :style="{ borderColor: brandColor + '30' }"
            >
                <div id="payment-element" class="min-h-fit" />
            </div>
        </div>

        <div class="border-t p-5">
            <div
                v-if="errorMessage"
                class="mb-3 rounded-lg bg-destructive/10 p-3 text-sm text-destructive"
            >
                {{ errorMessage }}
            </div>

            <Button
                class="w-full"
                :disabled="paying"
                :style="{ backgroundColor: brandColor }"
                @click="confirmPayment"
            >
                <Loader2 v-if="paying" class="size-4 animate-spin" />
                Pay {{ formattedAmount }}
            </Button>

            <div class="mt-3 flex justify-center">
                <span
                    class="inline-flex items-center gap-1 rounded-full border border-green-300 px-3 py-1 text-xs text-green-600 dark:border-green-800 dark:text-green-400"
                >
                    <Lock class="size-3" />
                    Secured by PayRex
                </span>
            </div>
        </div>
    </div>
</template>

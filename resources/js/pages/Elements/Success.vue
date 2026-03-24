<script setup>
import { Head, Link, usePoll } from '@inertiajs/vue3';
import { ArrowLeft, Check, Clock, ExternalLink, X } from 'lucide-vue-next';
import { computed, markRaw, watch } from 'vue';
import LiveIndicator from '@/components/LiveIndicator.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatAmount, formatTimestamp } from '@/lib/formatters';
import { paymentIntentStatusColors } from '@/lib/statusColors';

const props = defineProps({
    paymentIntent: { type: Object, default: null },
    status: { type: String, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'PayRex Elements', href: '/elements' },
    { title: 'Payment Result' },
];

const isPending = computed(() =>
    ['processing', 'awaiting_payment_method', 'awaiting_next_action'].includes(
        props.status,
    ),
);

const { stop } = usePoll(
    3000,
    { only: ['paymentIntent', 'status'] },
    { autoStart: isPending.value },
);

watch(isPending, (pending) => {
    if (!pending) stop();
});

const statusConfig = {
    succeeded: {
        icon: markRaw(Check),
        title: 'Payment Successful',
        description: 'Your payment has been processed and confirmed.',
        iconColor: 'text-green-600 dark:text-green-400',
        ringColor:
            'bg-green-50 ring-green-100 dark:bg-green-950/50 dark:ring-green-900/50',
        badgeClass: paymentIntentStatusColors.succeeded,
    },
    processing: {
        icon: markRaw(Clock),
        title: 'Payment Processing',
        description:
            'Your payment is being processed. This page updates automatically.',
        iconColor: 'text-blue-500',
        ringColor: 'ring-blue-500/20',
        badgeClass: paymentIntentStatusColors.processing,
    },
    awaiting_payment_method: {
        icon: markRaw(X),
        title: 'Payment Not Completed',
        description: 'The payment was not completed. You can try again.',
        iconColor: 'text-amber-500',
        ringColor: 'ring-amber-500/20',
        badgeClass: paymentIntentStatusColors.awaiting_payment_method,
    },
    canceled: {
        icon: markRaw(X),
        title: 'Payment Canceled',
        description: 'This payment has been canceled.',
        iconColor: 'text-red-500',
        ringColor: 'ring-red-500/20',
        badgeClass: paymentIntentStatusColors.canceled,
    },
};

const config = computed(
    () => statusConfig[props.status] || statusConfig.awaiting_payment_method,
);
</script>

<template>
    <Head title="Payment Result" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="page-enter flex flex-1 flex-col items-center justify-center p-4"
        >
            <div class="w-full max-w-md">
                <div class="rounded-xl border bg-card">
                    <div
                        class="flex flex-col items-center gap-4 px-6 pt-10 pb-6"
                    >
                        <div
                            class="flex size-16 items-center justify-center rounded-full ring-8"
                            :class="[
                                config.ringColor,
                                status === 'succeeded' &&
                                    'animate-success-ring',
                            ]"
                        >
                            <component
                                :is="config.icon"
                                :class="[
                                    config.iconColor,
                                    status === 'succeeded' &&
                                        'animate-check-draw',
                                ]"
                                class="size-8"
                            />
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <h1 class="text-lg font-semibold">
                                    {{ config.title }}
                                </h1>
                                <LiveIndicator
                                    v-if="isPending"
                                    label="Checking"
                                />
                            </div>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ config.description }}
                            </p>
                        </div>
                        <p
                            v-if="paymentIntent"
                            class="text-3xl font-bold tracking-tight"
                        >
                            {{ formatAmount(paymentIntent.amount) }}
                        </p>
                    </div>

                    <div v-if="paymentIntent" class="divide-y border-t">
                        <div
                            class="flex items-center justify-between px-6 py-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Status</span
                            >
                            <Badge :class="config.badgeClass">
                                {{ status }}
                            </Badge>
                        </div>
                        <div
                            class="flex items-center justify-between px-6 py-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Payment Intent</span
                            >
                            <code class="max-w-[200px] truncate text-xs">{{
                                paymentIntent.id
                            }}</code>
                        </div>
                        <div
                            v-if="paymentIntent.description"
                            class="flex items-center justify-between px-6 py-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Description</span
                            >
                            <span class="text-sm font-medium">{{
                                paymentIntent.description
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between px-6 py-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Currency</span
                            >
                            <span class="text-sm font-medium">{{
                                paymentIntent.currency
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between px-6 py-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Created</span
                            >
                            <span class="text-sm">{{
                                formatTimestamp(paymentIntent.created_at)
                            }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2 border-t p-4">
                        <Button as-child class="flex-1">
                            <Link href="/elements">
                                <ArrowLeft class="size-4" />
                                New Payment
                            </Link>
                        </Button>
                        <Button
                            v-if="paymentIntent"
                            as-child
                            variant="outline"
                            class="flex-1"
                        >
                            <Link
                                :href="`/payment-intents/${paymentIntent.id}`"
                            >
                                <ExternalLink class="size-4" />
                                View Details
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-check-draw {
    stroke-dasharray: 40;
    stroke-dashoffset: 40;
    animation: check-draw 0.4s cubic-bezier(0.23, 1, 0.32, 1) 0.15s forwards;
}

.animate-success-ring {
    animation: ring-pop 0.35s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

@keyframes check-draw {
    to {
        stroke-dashoffset: 0;
    }
}

@keyframes ring-pop {
    0% {
        transform: scale(0.8);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

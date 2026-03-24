<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Ban, CreditCard, Download, ExternalLink } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import RawResponse from '@/components/RawResponse.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatAmount, formatTimestamp } from '@/lib/formatters';
import { paymentIntentStatusColors } from '@/lib/statusColors';

const props = defineProps({
    paymentIntent: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payment Intents', href: '/payment-intents' },
    { title: props.paymentIntent.id },
];

const processingAction = shallowRef(null);

function action(path) {
    processingAction.value = path;
    router.post(
        `/payment-intents/${props.paymentIntent.id}/${path}`,
        {},
        {
            preserveScroll: true,
            onFinish: () => (processingAction.value = null),
        },
    );
}
</script>

<template>
    <Head :title="`Payment Intent ${paymentIntent.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div class="rounded-xl border bg-card">
                <div
                    class="flex flex-col gap-6 p-6 xl:flex-row xl:items-start xl:justify-between"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                        >
                            <CreditCard class="size-6 text-muted-foreground" />
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1
                                    class="text-xl font-semibold tracking-tight"
                                >
                                    Payment Intent
                                </h1>
                                <Badge
                                    variant="secondary"
                                    :class="
                                        paymentIntentStatusColors[
                                            paymentIntent.status
                                        ] || ''
                                    "
                                >
                                    {{ paymentIntent.status }}
                                </Badge>
                            </div>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                <code class="text-xs break-all">{{
                                    paymentIntent.id
                                }}</code>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-if="paymentIntent.status === 'awaiting_capture'"
                            :disabled="!!processingAction"
                            @click="action('capture')"
                        >
                            <Spinner v-if="processingAction === 'capture'" />
                            <Download v-else class="size-4" />
                            Capture
                        </Button>
                        <Button
                            v-if="paymentIntent.latest_payment"
                            variant="outline"
                            as-child
                        >
                            <Link
                                :href="`/payments/${typeof paymentIntent.latest_payment === 'string' ? paymentIntent.latest_payment : paymentIntent.latest_payment.id}`"
                                prefetch="hover"
                            >
                                <ExternalLink class="size-4" />
                                View Payment
                            </Link>
                        </Button>
                        <ConfirmDialog
                            v-if="
                                paymentIntent.status ===
                                'awaiting_payment_method'
                            "
                            title="Cancel payment intent?"
                            description="This will cancel the payment intent. This action cannot be undone."
                            confirm-label="Cancel"
                            variant="destructive"
                            @confirm="action('cancel')"
                        >
                            <Button
                                variant="destructive"
                                :disabled="!!processingAction"
                            >
                                <Spinner v-if="processingAction === 'cancel'" />
                                <Ban v-else class="size-4" />
                                Cancel
                            </Button>
                        </ConfirmDialog>
                    </div>
                </div>

                <div class="grid grid-cols-2 divide-x border-t xl:grid-cols-4">
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Amount
                        </p>
                        <p class="mt-1 text-xl font-bold tracking-tight">
                            {{ formatAmount(paymentIntent.amount) }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Received
                        </p>
                        <p class="mt-1 text-xl font-bold tracking-tight">
                            {{
                                formatAmount(paymentIntent.amount_received || 0)
                            }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Capturable
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            {{
                                formatAmount(
                                    paymentIntent.amount_capturable || 0,
                                )
                            }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Currency
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            {{ paymentIntent.currency }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-medium"
                            >Details</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="divide-y">
                            <div
                                v-if="paymentIntent.description"
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Description</span
                                >
                                <span class="text-sm font-medium">{{
                                    paymentIntent.description
                                }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Created</span
                                >
                                <span class="text-sm">{{
                                    formatTimestamp(paymentIntent.created_at)
                                }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Updated</span
                                >
                                <span class="text-sm">{{
                                    formatTimestamp(paymentIntent.updated_at)
                                }}</span>
                            </div>
                            <div
                                v-if="paymentIntent.statement_descriptor"
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Statement Descriptor</span
                                >
                                <span class="text-sm font-medium">{{
                                    paymentIntent.statement_descriptor
                                }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-medium"
                            >Payment Methods</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="pm in paymentIntent.payment_methods"
                                :key="pm"
                                variant="outline"
                                class="font-mono text-xs"
                            >
                                {{ pm }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <RawResponse :data="paymentIntent" />
        </div>
    </AppLayout>
</template>

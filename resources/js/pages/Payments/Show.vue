<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { CreditCard, RotateCcw } from 'lucide-vue-next';
import { computed } from 'vue';
import RawResponse from '@/components/RawResponse.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    formatAmount,
    formatSnakeCase,
    formatTimestamp,
} from '@/lib/formatters';
import { paymentStatusColors } from '@/lib/statusColors';

const props = defineProps({
    payment: { type: Object, required: true },
});

const payment = computed(() => props.payment);

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    props.payment.payment_intent_id
        ? {
              title: 'Payment Intent',
              href: `/payment-intents/${props.payment.payment_intent_id}`,
          }
        : { title: 'Payment' },
    { title: props.payment.id },
];

const customerInfo = computed(() => {
    const c = payment.value.customer;
    if (!c) return null;
    if (typeof c === 'string') return { id: c };
    return c;
});

const paymentMethodName = computed(() =>
    formatSnakeCase(payment.value.payment_method?.type),
);
</script>

<template>
    <Head :title="`Payment ${payment.id}`" />

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
                                    Payment
                                </h1>
                                <Badge
                                    variant="secondary"
                                    :class="
                                        paymentStatusColors[payment.status] ||
                                        ''
                                    "
                                >
                                    {{ payment.status }}
                                </Badge>
                                <Badge
                                    v-if="payment.refunded"
                                    variant="secondary"
                                    class="bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300"
                                >
                                    refunded
                                </Badge>
                            </div>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                <code class="text-xs break-all">{{
                                    payment.id
                                }}</code>
                            </p>
                        </div>
                    </div>

                    <Button
                        v-if="payment.status === 'paid' && !payment.refunded"
                        variant="outline"
                        as-child
                    >
                        <Link :href="`/payments/${payment.id}/refund`">
                            <RotateCcw class="size-4" />
                            Refund
                        </Link>
                    </Button>
                </div>

                <div class="grid grid-cols-2 divide-x border-t xl:grid-cols-4">
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Amount
                        </p>
                        <p class="mt-1 text-xl font-bold tracking-tight">
                            {{ formatAmount(payment.amount) }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Net
                        </p>
                        <p class="mt-1 text-xl font-bold tracking-tight">
                            {{ formatAmount(payment.net_amount || 0) }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Fee
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            {{ formatAmount(payment.fee || 0) }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Method
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            {{ paymentMethodName }}
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
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Payment Intent</span
                                >
                                <Link
                                    v-if="payment.payment_intent_id"
                                    :href="`/payment-intents/${payment.payment_intent_id}`"
                                    class="text-sm text-primary hover:underline"
                                    prefetch="hover"
                                >
                                    {{ payment.payment_intent_id }}
                                </Link>
                                <span v-else class="text-sm">—</span>
                            </div>
                            <div
                                v-if="payment.description"
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Description</span
                                >
                                <span class="text-sm font-medium">{{
                                    payment.description
                                }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Currency</span
                                >
                                <span class="text-sm font-medium">{{
                                    payment.currency
                                }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Created</span
                                >
                                <span class="text-sm">{{
                                    formatTimestamp(payment.created_at)
                                }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between py-2.5"
                            >
                                <span class="text-sm text-muted-foreground"
                                    >Updated</span
                                >
                                <span class="text-sm">{{
                                    formatTimestamp(payment.updated_at)
                                }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-sm font-medium"
                                >Customer</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <template v-if="customerInfo">
                                <div class="space-y-1">
                                    <p
                                        v-if="customerInfo.name"
                                        class="text-sm font-medium"
                                    >
                                        {{ customerInfo.name }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        <span v-if="customerInfo.email"
                                            >{{ customerInfo.email }} ·
                                        </span>
                                        <code class="text-xs">{{
                                            customerInfo.id
                                        }}</code>
                                    </p>
                                </div>
                            </template>
                            <p v-else class="text-sm text-muted-foreground">
                                No customer linked.
                            </p>
                        </CardContent>
                    </Card>

                    <Card v-if="payment.refunded">
                        <CardHeader>
                            <CardTitle class="text-sm font-medium"
                                >Refund</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <div class="divide-y">
                                <div
                                    class="flex items-center justify-between py-2.5"
                                >
                                    <span class="text-sm text-muted-foreground"
                                        >Amount Refunded</span
                                    >
                                    <span class="text-sm font-medium">
                                        {{
                                            formatAmount(
                                                payment.amount_refunded || 0,
                                            )
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center justify-between py-2.5"
                                >
                                    <span class="text-sm text-muted-foreground"
                                        >Type</span
                                    >
                                    <span class="text-sm font-medium">
                                        {{
                                            payment.amount_refunded ===
                                            payment.amount
                                                ? 'Full refund'
                                                : 'Partial refund'
                                        }}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <Card v-if="payment.billing">
                <CardHeader>
                    <CardTitle class="text-sm font-medium"
                        >Billing Details</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="space-y-1 text-sm">
                            <p v-if="payment.billing.name" class="font-medium">
                                {{ payment.billing.name }}
                            </p>
                            <p
                                v-if="payment.billing.email"
                                class="text-muted-foreground"
                            >
                                {{ payment.billing.email }}
                            </p>
                            <p
                                v-if="payment.billing.phone"
                                class="text-muted-foreground"
                            >
                                {{ payment.billing.phone }}
                            </p>
                        </div>
                        <div
                            v-if="payment.billing.address"
                            class="space-y-1 text-sm"
                        >
                            <p
                                v-if="payment.billing.address.line1"
                                class="font-medium"
                            >
                                {{ payment.billing.address.line1 }}
                            </p>
                            <p v-if="payment.billing.address.line2">
                                {{ payment.billing.address.line2 }}
                            </p>
                            <p class="text-muted-foreground">
                                {{
                                    [
                                        payment.billing.address.city,
                                        payment.billing.address.state,
                                        payment.billing.address.postal_code,
                                    ]
                                        .filter(Boolean)
                                        .join(', ')
                                }}
                            </p>
                            <p
                                v-if="payment.billing.address.country"
                                class="text-muted-foreground"
                            >
                                {{ payment.billing.address.country }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <RawResponse :data="payment" />
        </div>
    </AppLayout>
</template>

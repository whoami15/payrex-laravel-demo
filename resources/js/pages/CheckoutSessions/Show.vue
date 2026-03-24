<script setup>
import { Head } from '@inertiajs/vue3';
import { ShoppingCart } from 'lucide-vue-next';
import CheckoutSessionActions from './components/CheckoutSessionActions.vue';
import RawResponse from '@/components/RawResponse.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatAmount } from '@/lib/formatters';
import { checkoutSessionStatusColors } from '@/lib/statusColors';

const props = defineProps({
    checkoutSession: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Checkout Sessions', href: '/checkout-sessions' },
    { title: props.checkoutSession.id },
];
</script>

<template>
    <Head :title="`Checkout Session ${checkoutSession.id}`" />

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
                            <ShoppingCart
                                class="size-6 text-muted-foreground"
                            />
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1
                                    class="text-xl font-semibold tracking-tight"
                                >
                                    Checkout Session
                                </h1>
                                <Badge
                                    variant="secondary"
                                    :class="
                                        checkoutSessionStatusColors[
                                            checkoutSession.status
                                        ] || ''
                                    "
                                >
                                    {{ checkoutSession.status }}
                                </Badge>
                            </div>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                <code class="text-xs break-all">{{
                                    checkoutSession.id
                                }}</code>
                            </p>
                        </div>
                    </div>

                    <CheckoutSessionActions
                        :checkout-session="checkoutSession"
                    />
                </div>

                <div class="grid grid-cols-2 divide-x border-t xl:grid-cols-3">
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Amount
                        </p>
                        <p class="mt-1 text-xl font-bold tracking-tight">
                            {{
                                formatAmount(
                                    checkoutSession.payment_intent?.amount || 0,
                                )
                            }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Status
                        </p>
                        <Badge
                            class="mt-1.5"
                            variant="secondary"
                            :class="
                                checkoutSessionStatusColors[
                                    checkoutSession.status
                                ] || ''
                            "
                        >
                            {{ checkoutSession.status }}
                        </Badge>
                    </div>
                    <div
                        v-if="checkoutSession.url"
                        class="col-span-2 px-6 py-4 sm:col-span-1"
                    >
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Checkout URL
                        </p>
                        <a
                            :href="checkoutSession.url"
                            target="_blank"
                            class="mt-1 inline-block text-sm break-all text-primary hover:underline"
                        >
                            Open checkout page
                        </a>
                    </div>
                </div>
            </div>

            <Card v-if="checkoutSession.line_items?.length">
                <CardHeader>
                    <CardTitle class="text-sm font-medium"
                        >Line Items</CardTitle
                    >
                </CardHeader>
                <CardContent>
                    <div class="divide-y">
                        <div
                            v-for="(item, i) in checkoutSession.line_items"
                            :key="i"
                            class="flex items-center gap-3 py-2"
                        >
                            <img
                                v-if="item.image"
                                :src="item.image"
                                :alt="item.name"
                                class="size-10 shrink-0 rounded-md border object-cover"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">
                                    {{ item.name }}
                                </p>
                                <p
                                    v-if="item.description"
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ item.description }}
                                </p>
                            </div>
                            <p class="shrink-0 text-sm tabular-nums">
                                {{ formatAmount(item.amount) }} ×
                                {{ item.quantity }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <RawResponse :data="checkoutSession" />
        </div>
    </AppLayout>
</template>

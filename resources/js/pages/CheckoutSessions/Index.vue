<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ExternalLink, Plus, ShoppingCart, UserCheck } from 'lucide-vue-next';
import { markRaw } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { checkoutSessionStatusColors } from '@/lib/statusColors';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Checkout Sessions', href: '/checkout-sessions' },
];

const steps = [
    {
        icon: markRaw(ShoppingCart),
        title: 'Create Session',
        description:
            'Define line items, payment methods, and success/cancel URLs.',
    },
    {
        icon: markRaw(ExternalLink),
        title: 'Redirect Customer',
        description: 'Send your customer to the PayRex-hosted checkout page.',
    },
    {
        icon: markRaw(UserCheck),
        title: 'Customer Pays & Returns',
        description:
            'Customer completes payment and is redirected to your success URL.',
    },
];
</script>

<template>
    <Head title="Checkout Sessions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex h-full flex-1 flex-col gap-6 p-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Checkout Sessions
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Create hosted checkout pages for your customers.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link href="/checkout-sessions/create">
                        <Plus class="size-4" />
                        Create Checkout Session
                    </Link>
                </Button>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card p-6">
                        <h2 class="text-sm font-semibold">
                            How Checkout Sessions Work
                        </h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Hosted checkout flow - zero frontend code required.
                        </p>
                        <div class="mt-5 space-y-4">
                            <div
                                v-for="(step, i) in steps"
                                :key="step.title"
                                class="flex gap-4 [&:not(:last-child)>div:last-child]:pb-4"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted"
                                    >
                                        <component
                                            :is="step.icon"
                                            class="size-4"
                                        />
                                    </div>
                                    <div
                                        v-if="i < steps.length - 1"
                                        class="mt-1 h-full w-px bg-border"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ step.title }}
                                    </p>
                                    <p
                                        class="mt-0.5 text-sm text-muted-foreground"
                                    >
                                        {{ step.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-6">
                        <h2 class="text-sm font-semibold">Status Lifecycle</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            A checkout session transitions through these
                            statuses.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Badge
                                v-for="(
                                    color, label
                                ) in checkoutSessionStatusColors"
                                :key="label"
                                :class="color"
                                class="font-mono text-xs"
                            >
                                {{ label }}
                            </Badge>
                        </div>
                        <p class="mt-3 text-sm text-muted-foreground">
                            You can <strong>expire</strong> an active session
                            before the customer completes payment.
                        </p>
                    </div>
                </div>

                <div>
                    <CodeBlock
                        title="Code Example"
                        code="
use LegionHQ\LaravelPayrex\Facades\Payrex;

$checkout = Payrex::checkoutSessions()->create([
    'line_items' => [
        [
            'name' => 'Pro Plan - Monthly',
            'amount' => 99900,
            'quantity' => 1,
        ],
    ],
    'payment_methods' => ['card', 'gcash'],
    'success_url' => route('orders.success'),
    'cancel_url' => route('orders.cancel'),
]);

// Redirect customer to hosted checkout
return redirect()->away($checkout->url);
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

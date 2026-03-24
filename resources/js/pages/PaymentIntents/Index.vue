<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, CreditCard, Download, Plus } from 'lucide-vue-next';
import { markRaw } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { paymentIntentStatusColors } from '@/lib/statusColors';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payment Intents', href: '/payment-intents' },
];

const steps = [
    {
        icon: markRaw(CreditCard),
        title: 'Create',
        description: 'Set amount, currency, and accepted payment methods.',
    },
    {
        icon: markRaw(ArrowRight),
        title: 'Customer Pays',
        description:
            'Customer completes payment via card, GCash, Maya, or QR Ph.',
    },
    {
        icon: markRaw(Download),
        title: 'Capture',
        description:
            'Funds are captured automatically or manually depending on your settings.',
    },
];
</script>

<template>
    <Head title="Payment Intents" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Payment Intents
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Create and manage payment intents via the PayRex API.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link href="/payment-intents/create">
                        <Plus class="size-4" />
                        Create Payment Intent
                    </Link>
                </Button>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card p-6">
                        <h2 class="text-sm font-semibold">
                            How Payment Intents Work
                        </h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            The payment intent lifecycle in three steps.
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
                            A payment intent transitions through these statuses.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Badge
                                v-for="(
                                    color, label
                                ) in paymentIntentStatusColors"
                                :key="label"
                                :class="color"
                                class="font-mono text-xs font-semibold"
                            >
                                {{ label }}
                            </Badge>
                        </div>
                        <p class="mt-3 text-sm text-muted-foreground">
                            You can <strong>cancel</strong> a payment intent
                            when its status is
                            <code>awaiting_payment_method</code>.
                        </p>
                    </div>
                </div>

                <div>
                    <CodeBlock
                        title="Code Example"
                        code="
use LegionHQ\LaravelPayrex\Facades\Payrex;

// Create a payment intent
$paymentIntent = Payrex::paymentIntents()->create([
    'amount' => 100000,  // ₱1,000.00 in centavos
    'description' => 'Order #1234',
    'payment_methods' => ['card', 'gcash', 'maya'],
]);

// Retrieve a payment intent
$paymentIntent = Payrex::paymentIntents()->retrieve('pi_...');

// Cancel a payment intent
$paymentIntent = Payrex::paymentIntents()->cancel('pi_...');

// Capture a payment intent (for manual capture)
$paymentIntent = Payrex::paymentIntents()->capture('pi_...', [
    'amount' => 50000,  // Partial capture
]);
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

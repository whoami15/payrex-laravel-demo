<script setup>
import { Head } from '@inertiajs/vue3';
import { Coins } from 'lucide-vue-next';
import CodeBlock from '@/components/CodeBlock.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    defaultCurrency: { type: String, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Default Currency' },
];
</script>

<template>
    <Head title="Default Currency" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="Default Currency"
                docs="guide/configuration"
                controller="app/Http/Controllers/Feature/DefaultCurrencyController.php"
            >
                Configure a default currency that's automatically applied to all
                create requests.
            </FeatureHeader>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card">
                        <div class="flex items-start gap-4 p-6">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                            >
                                <Coins class="size-6 text-muted-foreground" />
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold">
                                    Current Configuration
                                </h2>
                                <div class="mt-2">
                                    <Badge variant="outline" class="text-lg">
                                        {{ defaultCurrency }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <div
                            class="space-y-2 border-t px-6 py-4 text-sm text-muted-foreground"
                        >
                            <p>
                                When you create a payment intent, checkout
                                session, or customer without specifying a
                                currency, the package automatically uses the
                                configured default.
                            </p>
                            <p>
                                You can always override it per-request by
                                passing an explicit
                                <code>currency</code> parameter.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <CodeBlock
                        title="Configuration"
                        code="
# .env
PAYREX_CURRENCY=PHP

# config/payrex.php
'currency' => env('PAYREX_CURRENCY', 'PHP'),
                    "
                    />

                    <CodeBlock
                        title="Usage"
                        code="
use LegionHQ\LaravelPayrex\Facades\Payrex;

// Check the configured default
$currency = Payrex::defaultCurrency(); // 'PHP'

// Auto-applied: no currency needed
$pi = Payrex::paymentIntents()->create([
    'amount' => 100000,
    'payment_methods' => ['card'],
]);
// $pi->currency === 'PHP'

// Override per-request
$pi = Payrex::paymentIntents()->create([
    'amount' => 1000,
    'currency' => 'USD',
    'payment_methods' => ['card'],
]);
// $pi->currency === 'USD'
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

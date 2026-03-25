<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    BookOpen,
    CreditCard,
    FileText,
    Palette,
    ShoppingCart,
    Users,
    Webhook,
    Zap,
} from 'lucide-vue-next';
import { markRaw } from 'vue';
import GitHubIcon from '@/components/icons/GitHubIcon.vue';
import PayrexLogo from '@/components/PayrexLogo.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }];

const paymentFlows = [
    {
        title: 'Billing Statements',
        description:
            'One-time payment links with line items, due dates, and customer details.',
        href: '/billing-statements/create',
        icon: markRaw(FileText),
        badge: null,
    },
    {
        title: 'PayRex Elements',
        description:
            'Payment Intents with an embedded checkout form powered by the JS SDK.',
        href: '/elements',
        icon: markRaw(Palette),
        badge: 'Embedded',
    },
    {
        title: 'Checkout Sessions',
        description:
            'Redirect to a PayRex-hosted checkout page. Zero frontend code.',
        href: '/checkout-sessions/create',
        icon: markRaw(ShoppingCart),
        badge: 'Hosted',
    },
];

const resources = [
    {
        title: 'Customers',
        description: 'Manage customer records and billing details',
        href: '/customers',
        icon: markRaw(Users),
    },
    {
        title: 'Billing Statements',
        description: 'Manage payment links and line items',
        href: '/billing-statements',
        icon: markRaw(FileText),
    },
    {
        title: 'Webhooks',
        description: 'Configure endpoints for real-time event notifications',
        href: '/webhooks',
        icon: markRaw(Webhook),
    },
];

const features = [
    {
        title: 'Billable Customer',
        href: '/features/billable-customer',
        description: 'Eloquent trait for PayRex integration',
    },
    {
        title: 'Pagination',
        href: '/features/pagination',
        description: 'Cursor-based pagination',
    },
    {
        title: 'Webhook Events',
        href: '/features/webhook-events',
        description: '20 typed Laravel events',
    },
    {
        title: 'constructEvent()',
        href: '/features/construct-event',
        description: 'Custom webhook handling',
    },
    {
        title: 'Error Handling',
        href: '/features/error-handling',
        description: 'Typed exception hierarchy',
    },
    {
        title: 'Response Metadata',
        href: '/features/response-metadata',
        description: 'Inspect HTTP headers & status',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-8 p-4 md:p-6">
            <div
                class="relative overflow-hidden rounded-2xl border bg-gradient-to-br from-primary/[0.03] via-transparent to-blue-500/[0.03]"
            >
                <div
                    class="relative z-10 flex flex-col items-center gap-6 px-6 py-10 text-center md:flex-row md:gap-10 md:px-10 md:text-left"
                >
                    <div class="relative shrink-0">
                        <div
                            class="logo-glow absolute inset-0 -m-1 rounded-full opacity-30 blur-xl dark:opacity-45"
                        />
                        <PayrexLogo class="relative size-28 md:size-36" />
                    </div>
                    <div class="space-y-3">
                        <div class="space-y-1">
                            <h1
                                class="text-2xl font-bold tracking-tight md:text-3xl"
                            >
                                PayRex for Laravel
                            </h1>
                            <p
                                class="text-sm text-muted-foreground md:text-base"
                            >
                                Explore every feature of the
                                <code
                                    class="rounded bg-primary/10 px-1.5 py-0.5 text-xs font-semibold text-primary"
                                    >legionhq/laravel-payrex</code
                                >
                                package.
                            </p>
                        </div>
                        <div
                            class="flex flex-wrap justify-center gap-2 md:justify-start"
                        >
                            <Button as-child size="sm">
                                <a
                                    href="https://payrexlaravel.com"
                                    target="_blank"
                                >
                                    <BookOpen class="size-3.5" />
                                    Documentation
                                </a>
                            </Button>
                            <Button as-child variant="outline" size="sm">
                                <a
                                    href="https://github.com/whoami15/payrex-laravel"
                                    target="_blank"
                                >
                                    <GitHubIcon class="size-3.5" />
                                    Source Code
                                </a>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="mb-1 text-lg font-semibold tracking-tight">
                    Accept Payments
                </h2>
                <p class="mb-4 text-sm text-muted-foreground">
                    Collect payments with PayRex.
                </p>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="flow in paymentFlows"
                        :key="flow.title"
                        :href="flow.href"
                        prefetch="hover"
                        view-transition
                        class="group"
                    >
                        <div
                            class="relative h-full overflow-hidden rounded-xl border p-5 transition-all duration-200 hover:border-primary/30"
                        >
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-primary/[0.02] to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                            />
                            <div class="relative flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <component
                                        :is="flow.icon"
                                        class="size-5 text-foreground"
                                    />
                                    <Badge
                                        v-if="flow.badge"
                                        variant="outline"
                                        class="text-[10px]"
                                        >{{ flow.badge }}</Badge
                                    >
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold">
                                        {{ flow.title }}
                                    </h3>
                                    <p
                                        class="mt-1 text-sm leading-relaxed text-muted-foreground"
                                    >
                                        {{ flow.description }}
                                    </p>
                                </div>
                                <div
                                    class="mt-auto flex items-center gap-1 text-xs font-medium text-primary"
                                >
                                    Try it
                                    <ArrowRight
                                        class="size-3 transition-transform duration-150 group-hover:translate-x-0.5"
                                    />
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <div>
                <h2 class="mb-1 text-lg font-semibold tracking-tight">
                    Manage
                </h2>
                <p class="mb-4 text-sm text-muted-foreground">
                    CRUD operations on PayRex API resources.
                </p>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="resource in resources"
                        :key="resource.title"
                        :href="resource.href"
                        prefetch="hover"
                        view-transition
                        class="group relative overflow-hidden rounded-xl border p-4 transition-all duration-200 hover:border-primary/30"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-primary/[0.02] to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                        />
                        <div class="relative flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <component
                                    :is="resource.icon"
                                    class="size-5 text-foreground"
                                />
                                <ArrowRight
                                    class="size-4 text-muted-foreground/30 transition-all group-hover:translate-x-0.5 group-hover:text-foreground"
                                />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold">
                                    {{ resource.title }}
                                </h3>
                                <p
                                    class="mt-1 text-sm leading-relaxed text-muted-foreground"
                                >
                                    {{ resource.description }}
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <div>
                <div class="mb-4 flex items-center gap-2">
                    <Zap class="size-4 text-primary" />
                    <h2 class="text-lg font-semibold tracking-tight">
                        Explore
                    </h2>
                </div>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="feature in features"
                        :key="feature.title"
                        :href="feature.href"
                        prefetch="hover"
                        view-transition
                        class="group flex items-center gap-3 rounded-lg border p-3 transition-all duration-150 hover:bg-primary/[0.03] dark:hover:bg-primary/[0.06]"
                    >
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-sm font-medium transition-colors group-hover:text-primary"
                            >
                                {{ feature.title }}
                            </p>
                            <p class="truncate text-sm text-muted-foreground">
                                {{ feature.description }}
                            </p>
                        </div>
                        <ArrowRight
                            class="size-3.5 shrink-0 text-muted-foreground/30 transition-all group-hover:translate-x-0.5 group-hover:text-primary"
                        />
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.logo-glow {
    background-image: linear-gradient(
        -45deg,
        #6a63ef,
        #0096ff,
        #8b85f2,
        #5b54e0,
        #0096ff,
        #5b54e0,
        #8b85f2,
        #0096ff
    );
    background-size: 400% 400%;
    animation: logo-glow 10s ease infinite;
}

@keyframes logo-glow {
    0% {
        background-position: 0% 50%;
        border-radius: 75px;
        transform: rotate(0deg);
    }
    50% {
        background-position: 100% 50%;
        border-radius: 150px;
        transform: rotate(100deg);
    }
    100% {
        background-position: 0% 50%;
        border-radius: 75px;
        transform: rotate(0deg);
    }
}
</style>

<script setup>
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    CreditCard,
    FileText,
    LayoutGrid,
    Palette,
    ShoppingCart,
    Users,
    Webhook,
    UserCheck,
    AlertTriangle,
    Radio,
    Key,
    DollarSign,
    Terminal,
    ShieldCheck,
    Layers,
    PhilippinePeso,
    Info,
} from 'lucide-vue-next';
import { markRaw } from 'vue';
import AppLogo from './AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavSection from '@/components/NavSection.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

const dashboardNavItems = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: markRaw(LayoutGrid),
    },
];

const paymentsSection = {
    label: 'Payments',
    groups: [
        {
            title: 'Payment Intents',
            icon: markRaw(CreditCard),
            items: [
                { title: 'Overview', href: '/payment-intents' },
                { title: 'Create Payment', href: '/payment-intents/create' },
            ],
        },
        {
            title: 'PayRex Elements',
            icon: markRaw(Palette),
            items: [{ title: 'Custom Payment Form', href: '/elements' }],
        },
        {
            title: 'Checkout Sessions',
            icon: markRaw(ShoppingCart),
            items: [
                { title: 'Overview', href: '/checkout-sessions' },
                { title: 'Create Checkout', href: '/checkout-sessions/create' },
            ],
        },
    ],
};

const merchantSection = {
    label: 'Manage',
    groups: [
        {
            title: 'Customers',
            icon: markRaw(Users),
            items: [
                { title: 'All Customers', href: '/customers' },
                { title: 'Create Customer', href: '/customers/create' },
            ],
        },
        {
            title: 'Billing Statements',
            icon: markRaw(FileText),
            items: [
                { title: 'All Statements', href: '/billing-statements' },
                {
                    title: 'Create Statement',
                    href: '/billing-statements/create',
                },
            ],
        },
        {
            title: 'Webhooks',
            icon: markRaw(Webhook),
            items: [
                { title: 'All Endpoints', href: '/webhooks' },
                { title: 'Create Endpoint', href: '/webhooks/create' },
                { title: 'Received Events', href: '/webhooks/received' },
            ],
        },
    ],
};

const exploreSection = {
    label: 'Explore',
};

const exploreItems = [
    {
        title: 'Billable Customer',
        href: '/features/billable-customer',
        icon: markRaw(UserCheck),
    },
    {
        title: 'Artisan Commands',
        href: '/features/artisan-commands',
        icon: markRaw(Terminal),
    },
    {
        title: 'Webhook Events',
        href: '/features/webhook-events',
        icon: markRaw(Radio),
    },
    {
        title: 'constructEvent()',
        href: '/features/construct-event',
        icon: markRaw(ShieldCheck),
    },
    {
        title: 'Pagination',
        href: '/features/pagination',
        icon: markRaw(Layers),
    },
    {
        title: 'Error Handling',
        href: '/features/error-handling',
        icon: markRaw(AlertTriangle),
    },
    {
        title: 'Response Metadata',
        href: '/features/response-metadata',
        icon: markRaw(Info),
    },
    {
        title: 'Default Currency',
        href: '/features/default-currency',
        icon: markRaw(PhilippinePeso),
    },
    // {
    //     title: 'Idempotency Keys',
    //     href: '/features/idempotency',
    //     icon: markRaw(Key),
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavSection :section="paymentsSection" :items="dashboardNavItems" />
            <NavSection :section="merchantSection" />
            <NavSection :section="exploreSection" :items="exploreItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

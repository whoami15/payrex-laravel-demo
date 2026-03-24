<script setup>
import { Deferred, Head, Link, router } from '@inertiajs/vue3';
import { Eye, FileText, Plus } from 'lucide-vue-next';
import { shallowRef, watch } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import LimitSelector from '@/components/LimitSelector.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatAmount } from '@/lib/formatters';
import { billingStatementStatusColors } from '@/lib/statusColors';

const props = defineProps({
    billingStatements: { type: Object, default: undefined },
    filters: { type: Object, default: () => ({}) },
});

const limit = shallowRef(String(props.filters.limit || 10));

watch(limit, () => {
    router.get(
        '/billing-statements',
        { limit: limit.value },
        { preserveState: true, preserveScroll: true },
    );
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Billing Statements', href: '/billing-statements' },
];

const loadingMore = shallowRef(false);

function loadMore() {
    const lastItem =
        props.billingStatements.data[props.billingStatements.data.length - 1];
    if (lastItem) {
        loadingMore.value = true;
        router.get(
            '/billing-statements',
            { after: lastItem.id, limit: limit.value },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => (loadingMore.value = false),
            },
        );
    }
}
</script>

<template>
    <Head title="Billing Statements" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-4 p-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Billing Statements
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Create and manage one-time payment links.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link href="/billing-statements/create">
                        <Plus class="size-4" />
                        Create Statement
                    </Link>
                </Button>
            </div>

            <div class="ml-auto flex w-full max-w-md justify-end">
                <LimitSelector v-model="limit" />
            </div>

            <Card>
                <CardContent class="p-0">
                    <Deferred data="billingStatements">
                        <template #fallback>
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr
                                        class="border-b text-left text-sm text-muted-foreground"
                                    >
                                        <th class="px-4 py-3 font-medium">
                                            ID
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Number
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Amount
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="i in 5"
                                        :key="i"
                                        class="border-b last:border-0"
                                    >
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-28" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-20" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-16" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton
                                                class="h-5 w-14 rounded-md"
                                            />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-8" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        <EmptyState
                            v-if="billingStatements.data.length === 0"
                            :icon="FileText"
                            title="No billing statements"
                            description="Create your first billing statement to collect a payment."
                            action-label="Create Statement"
                            action-href="/billing-statements/create"
                        />
                        <div v-else class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr
                                        class="border-b text-left text-sm text-muted-foreground"
                                    >
                                        <th class="px-4 py-3 font-medium">
                                            ID
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Number
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Amount
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Status
                                        </th>
                                        <th class="w-0 px-4 py-3 font-medium" />
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="bs in billingStatements.data"
                                        :key="bs.id"
                                        class="border-b last:border-0"
                                    >
                                        <td class="px-4 py-3">
                                            <code class="text-xs">{{
                                                bs.id
                                            }}</code>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{
                                                bs.billing_statement_number ||
                                                '—'
                                            }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm font-medium"
                                        >
                                            {{ formatAmount(bs.amount || 0) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <Badge
                                                variant="secondary"
                                                :class="
                                                    billingStatementStatusColors[
                                                        bs.status
                                                    ] || ''
                                                "
                                            >
                                                {{ bs.status }}
                                            </Badge>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                as-child
                                            >
                                                <Link
                                                    :href="`/billing-statements/${bs.id}`"
                                                    prefetch="hover"
                                                >
                                                    <Eye class="size-4" />
                                                </Link>
                                            </Button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </Deferred>
                </CardContent>
            </Card>

            <div v-if="billingStatements?.has_more" class="flex justify-center">
                <Button
                    variant="outline"
                    :disabled="loadingMore"
                    @click="loadMore"
                >
                    <Spinner v-if="loadingMore" />
                    Load More
                </Button>
            </div>
        </div>
    </AppLayout>
</template>

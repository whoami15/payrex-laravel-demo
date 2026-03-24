<script setup>
import { Deferred, Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search, X } from 'lucide-vue-next';
import { shallowRef, watch } from 'vue';
import CustomerTable from './components/CustomerTable.vue';
import LimitSelector from '@/components/LimitSelector.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    customers: { type: Object, default: undefined },
    filters: { type: Object, default: () => ({}) },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/customers' },
];

const search = shallowRef(props.filters.name || props.filters.email || '');
const limit = shallowRef(String(props.filters.limit || 10));
let searchTimeout = null;

function buildQuery() {
    const data = { limit: limit.value };

    if (search.value) {
        if (search.value.includes('@')) {
            data.email = search.value;
        } else {
            data.name = search.value;
        }
    }

    return data;
}

function fetchCustomers() {
    router.get('/customers', buildQuery(), {
        preserveState: true,
        preserveScroll: true,
    });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(fetchCustomers, 400);
});

watch(limit, fetchCustomers);

function clearSearch() {
    search.value = '';
}

const loadingMore = shallowRef(false);

function loadMore() {
    const lastItem = props.customers.data[props.customers.data.length - 1];
    if (lastItem) {
        loadingMore.value = true;
        router.get(
            '/customers',
            { ...buildQuery(), after: lastItem.id },
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
    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-4 p-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Customers
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Manage your PayRex customers.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link href="/customers/create">
                        <Plus class="size-4" />
                        Create Customer
                    </Link>
                </Button>
            </div>

            <div class="mx-auto flex w-full max-w-md gap-2">
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Search by name or email..."
                        class="pr-9 pl-9"
                    />
                    <button
                        v-if="search"
                        class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        @click="clearSearch"
                    >
                        <X class="size-4" />
                    </button>
                </div>
                <LimitSelector v-model="limit" />
            </div>

            <Card>
                <CardContent class="p-0">
                    <Deferred data="customers">
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
                                            Name
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Email
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Currency
                                        </th>
                                        <th class="px-4 py-3 font-medium" />
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
                                            <Skeleton class="h-4 w-24" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-36" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-10" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-8" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        <CustomerTable :customers="customers" />
                    </Deferred>
                </CardContent>
            </Card>

            <div v-if="customers?.has_more" class="flex justify-center">
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

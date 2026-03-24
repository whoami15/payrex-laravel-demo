<script setup>
import { Deferred, Head, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Search, Users, X } from 'lucide-vue-next';
import { shallowRef, watch } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import EmptyState from '@/components/EmptyState.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import LimitSelector from '@/components/LimitSelector.vue';
import RawResponse from '@/components/RawResponse.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    customers: { type: Object, default: undefined },
    filters: { type: Object, default: () => ({}) },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Pagination' },
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
    router.get('/features/pagination', buildQuery(), {
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

function navigateTo(url) {
    if (!url) return;
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Pagination" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="Pagination"
                docs="guide/pagination"
                controller="app/Http/Controllers/Feature/PaginationController.php"
            >
                Use <code>paginate()</code> for automatic cursor-based
                pagination with Laravel's <code>CursorPaginator</code> - no
                manual cursor handling needed.
            </FeatureHeader>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row">
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
                        <CardContent class="overflow-hidden p-0">
                            <Deferred data="customers">
                                <template #fallback>
                                    <div class="overflow-x-auto">
                                        <table class="w-full whitespace-nowrap">
                                            <thead>
                                                <tr
                                                    class="border-b text-left text-sm text-muted-foreground"
                                                >
                                                    <th
                                                        class="px-4 py-3 font-medium"
                                                    >
                                                        Name
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-medium"
                                                    >
                                                        Email
                                                    </th>
                                                    <th
                                                        class="px-4 py-3 font-medium"
                                                    >
                                                        Currency
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
                                                        <Skeleton
                                                            class="h-4 w-24"
                                                        />
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <Skeleton
                                                            class="h-4 w-36"
                                                        />
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <Skeleton
                                                            class="h-4 w-10"
                                                        />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>

                                <EmptyState
                                    v-if="customers?.data?.length === 0"
                                    :icon="Users"
                                    title="No customers found"
                                    description="Try a different search term."
                                />
                                <div
                                    v-else-if="customers"
                                    class="overflow-x-auto"
                                >
                                    <table
                                        class="w-full text-sm whitespace-nowrap"
                                    >
                                        <thead>
                                            <tr
                                                class="border-b text-left text-muted-foreground"
                                            >
                                                <th
                                                    class="px-4 py-3 font-medium"
                                                >
                                                    Name
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-medium"
                                                >
                                                    Email
                                                </th>
                                                <th
                                                    class="px-4 py-3 font-medium"
                                                >
                                                    Currency
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="customer in customers.data"
                                                :key="customer.id"
                                                class="border-b last:border-0"
                                            >
                                                <td
                                                    class="px-4 py-3 font-medium"
                                                >
                                                    {{ customer.name }}
                                                </td>
                                                <td
                                                    class="px-4 py-3 text-muted-foreground"
                                                >
                                                    {{ customer.email }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    {{ customer.currency }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </Deferred>
                        </CardContent>
                    </Card>

                    <div
                        v-if="
                            customers?.prev_page_url || customers?.next_page_url
                        "
                        class="flex items-center justify-between"
                    >
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="!customers?.prev_page_url"
                            @click="navigateTo(customers?.prev_page_url)"
                        >
                            <ChevronLeft class="size-4" />
                            Previous
                        </Button>
                        <span class="text-xs text-muted-foreground">
                            {{ customers?.per_page }} per page
                        </span>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="!customers?.next_page_url"
                            @click="navigateTo(customers?.next_page_url)"
                        >
                            Next
                            <ChevronRight class="ml-1 size-4" />
                        </Button>
                    </div>

                    <Deferred data="customers">
                        <template #fallback>
                            <Skeleton class="h-40 w-full rounded-xl" />
                        </template>

                        <RawResponse v-if="customers" :data="customers" />
                    </Deferred>
                </div>

                <div class="space-y-4">
                    <CodeBlock
                        title="Backend Code"
                        code="
// One-liner - automatic cursor resolution
Payrex::customers()->paginate(perPage: 10);

// With filters
Payrex::customers()->paginate(
    perPage: 10,
    params: ['name' => 'Juan'],
);

// With Inertia::defer()
return Inertia::render('Customers', [
    'customers' => Inertia::defer(
        fn () => Payrex::customers()->paginate(10),
    ),
]);
                    "
                    />

                    <CodeBlock
                        title="Frontend (Inertia)"
                        code="
// The response is a standard CursorPaginator:
// {
//   data: [...],
//   path: 'https://example.com/customers'
//   per_page: 10,
//   next_cursor: 'eyJ...',
//   next_page_url: 'https://example.com/customers?cursor=eyJ...',
//   prev_cursor: null,
//   prev_page_url: null,
// }

// Navigate with router.visit()
function navigateTo(url) {
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
    });
}
                    "
                    />

                    <CodeBlock
                        title="Manual Cursor (Without paginate())"
                        code="
// Without paginate() - manual boilerplate:
$after = $request->input('after');
$params = ['limit' => 10];

if ($after) {
    $params['after'] = $after;
}

$customers = Payrex::customers()->list($params);

// Frontend must check has_more + extract
// last item ID for the next page cursor.

// With paginate() - all automatic:
Payrex::customers()->paginate(10);
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

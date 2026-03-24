<script setup>
import { Deferred, Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search, X } from 'lucide-vue-next';
import { shallowRef, watch } from 'vue';
import WebhookTable from './components/WebhookTable.vue';
import LimitSelector from '@/components/LimitSelector.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    webhooks: { type: Object, default: undefined },
    filters: { type: Object, default: () => ({}) },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Webhooks', href: '/webhooks' },
];

const search = shallowRef(props.filters.url || props.filters.description || '');
const limit = shallowRef(String(props.filters.limit || 10));
let searchTimeout = null;

function buildQuery() {
    const data = { limit: limit.value };

    if (search.value) {
        if (search.value.startsWith('http')) {
            data.url = search.value;
        } else {
            data.description = search.value;
        }
    }

    return data;
}

function fetchWebhooks() {
    router.get('/webhooks', buildQuery(), {
        preserveState: true,
        preserveScroll: true,
    });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(fetchWebhooks, 400);
});

watch(limit, fetchWebhooks);

function clearSearch() {
    search.value = '';
}
</script>

<template>
    <Head title="Webhook Endpoints" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-4 p-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Webhook Endpoints
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Manage your webhook endpoints for real-time event
                        notifications.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link href="/webhooks/create">
                        <Plus class="size-4" />
                        Create Endpoint
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
                        placeholder="Search by URL or description..."
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
                    <Deferred data="webhooks">
                        <template #fallback>
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr
                                        class="border-b text-left text-sm text-muted-foreground"
                                    >
                                        <th class="px-4 py-3 font-medium">
                                            URL
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Events
                                        </th>
                                        <th class="px-4 py-3 font-medium" />
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="i in 3"
                                        :key="i"
                                        class="border-b last:border-0"
                                    >
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-80" />
                                            <Skeleton class="mt-1.5 h-3 w-32" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton
                                                class="h-5 w-16 rounded-full"
                                            />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-16" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <Skeleton class="h-4 w-8" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        <WebhookTable :webhooks="webhooks" />
                    </Deferred>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

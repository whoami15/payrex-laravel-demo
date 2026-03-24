<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ChevronDown, ChevronRight, Inbox } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import EventBadge from '@/components/EventBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    events: { type: Array, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Webhooks', href: '/webhooks' },
    { title: 'Received Events' },
];

const expandedId = shallowRef(null);

function toggle(id) {
    expandedId.value = expandedId.value === id ? null : id;
}
</script>

<template>
    <Head title="Received Webhook Events" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Received Events
                </h1>
                <p class="text-sm text-muted-foreground">
                    Webhook events received and processed by this application.
                </p>
            </div>

            <Card v-if="events.length === 0">
                <CardContent class="flex flex-col items-center gap-3 py-12">
                    <Inbox class="size-10 text-muted-foreground" />
                    <p class="text-center text-sm text-muted-foreground">
                        No webhook events received yet. Trigger a payment or
                        <Link
                            href="/features/construct-event"
                            class="font-medium text-primary underline underline-offset-4 hover:text-primary/80"
                            >simulate a webhook event</Link
                        >
                        to see it here.
                    </p>
                </CardContent>
            </Card>

            <Card v-else>
                <CardHeader>
                    <CardTitle class="text-sm font-medium">
                        Recent Events
                    </CardTitle>
                    <CardDescription>
                        Showing the {{ events.length }} most recent events.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="divide-y rounded-lg border">
                        <div v-for="event in events" :key="event.id">
                            <div
                                class="flex w-full cursor-pointer items-center gap-3 px-4 py-3 text-left transition-colors select-none hover:bg-muted/50"
                                @click="toggle(event.id)"
                            >
                                <component
                                    :is="
                                        expandedId === event.id
                                            ? ChevronDown
                                            : ChevronRight
                                    "
                                    class="size-4 shrink-0 text-muted-foreground"
                                />
                                <EventBadge :event="event.event_type" />
                                <code
                                    v-if="event.resource_id"
                                    class="hidden truncate text-xs text-muted-foreground sm:block"
                                >
                                    {{ event.resource_id }}
                                </code>
                                <span
                                    class="ml-auto shrink-0 text-xs text-muted-foreground"
                                >
                                    {{
                                        new Date(
                                            event.processed_at,
                                        ).toLocaleString()
                                    }}
                                </span>
                            </div>
                            <div
                                v-if="expandedId === event.id"
                                class="border-t bg-muted/30 px-4 py-3"
                            >
                                <div
                                    class="mb-2 flex items-center gap-4 text-xs text-muted-foreground"
                                >
                                    <span>
                                        Event ID:
                                        <code>{{ event.event_id }}</code>
                                    </span>
                                    <span v-if="event.resource_id">
                                        Resource:
                                        <code>{{ event.resource_id }}</code>
                                    </span>
                                </div>
                                <CodeBlock
                                    lang="json"
                                    :code="
                                        JSON.stringify(event.payload, null, 2)
                                    "
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

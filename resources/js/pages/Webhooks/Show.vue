<script setup>
import { Head, router } from '@inertiajs/vue3';
import { Power, Trash2, Webhook } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import EventBadge from '@/components/EventBadge.vue';
import RawResponse from '@/components/RawResponse.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    webhook: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Webhooks', href: '/webhooks' },
    { title: props.webhook.id },
];

const processingAction = shallowRef(null);

function toggle() {
    processingAction.value = 'toggle';
    router
        .optimistic((currentProps) => ({
            webhook: {
                ...currentProps.webhook,
                status:
                    currentProps.webhook.status === 'enabled'
                        ? 'disabled'
                        : 'enabled',
            },
        }))
        .post(
            `/webhooks/${props.webhook.id}/toggle`,
            {},
            {
                preserveScroll: true,
                onFinish: () => (processingAction.value = null),
            },
        );
}

function destroy() {
    processingAction.value = 'delete';
    router.delete(`/webhooks/${props.webhook.id}`, {
        onFinish: () => (processingAction.value = null),
    });
}
</script>

<template>
    <Head :title="`Webhook ${webhook.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div class="rounded-xl border bg-card">
                <div
                    class="flex flex-col gap-6 p-6 xl:flex-row xl:items-start xl:justify-between"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                        >
                            <Webhook class="size-6 text-muted-foreground" />
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1
                                    class="text-xl font-semibold tracking-tight"
                                >
                                    Webhook Endpoint
                                </h1>
                                <Badge
                                    :variant="
                                        webhook.status === 'enabled'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{ webhook.status }}
                                </Badge>
                            </div>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                <code class="text-xs break-all">{{
                                    webhook.id
                                }}</code>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Button
                            variant="outline"
                            :disabled="!!processingAction"
                            @click="toggle"
                        >
                            <Spinner v-if="processingAction === 'toggle'" />
                            <Power v-else class="size-4" />
                            {{
                                webhook.status === 'enabled'
                                    ? 'Disable'
                                    : 'Enable'
                            }}
                        </Button>
                        <ConfirmDialog
                            title="Delete webhook endpoint?"
                            description="This will permanently delete this webhook endpoint."
                            confirm-label="Delete"
                            variant="destructive"
                            @confirm="destroy"
                        >
                            <Button
                                variant="destructive"
                                :disabled="!!processingAction"
                            >
                                <Spinner v-if="processingAction === 'delete'" />
                                <Trash2 v-else class="size-4" />
                                Delete
                            </Button>
                        </ConfirmDialog>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 divide-y border-t lg:grid-cols-2 lg:divide-x lg:divide-y-0"
                >
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            URL
                        </p>
                        <p class="mt-1 text-sm font-medium break-all">
                            {{ webhook.url }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Description
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            {{ webhook.description || '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle class="text-sm font-medium">
                        Subscribed Events
                        <Badge
                            v-if="webhook.events?.length"
                            variant="secondary"
                            class="ml-2"
                        >
                            {{ webhook.events.length }}
                        </Badge>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="webhook.events?.length"
                        class="flex flex-wrap gap-1.5"
                    >
                        <EventBadge
                            v-for="event in webhook.events"
                            :key="event"
                            :event="event"
                        />
                    </div>
                    <p v-else class="text-sm text-muted-foreground">
                        No events subscribed.
                    </p>
                </CardContent>
            </Card>

            <RawResponse :data="webhook" />
        </div>
    </AppLayout>
</template>

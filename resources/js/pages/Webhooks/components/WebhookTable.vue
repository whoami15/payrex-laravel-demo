<script setup>
import { Link, router } from '@inertiajs/vue3';
import { Eye, MoreHorizontal, Power, Trash2, Webhook } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import EmptyState from '@/components/EmptyState.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Spinner } from '@/components/ui/spinner';

defineProps({
    webhooks: { type: Object, required: true },
});

const deletingId = shallowRef(null);

function toggleWebhook(webhook) {
    router
        .optimistic((currentProps) => ({
            webhooks: {
                ...currentProps.webhooks,
                data: currentProps.webhooks.data.map((w) =>
                    w.id === webhook.id
                        ? {
                              ...w,
                              status:
                                  w.status === 'enabled'
                                      ? 'disabled'
                                      : 'enabled',
                          }
                        : w,
                ),
            },
        }))
        .post(`/webhooks/${webhook.id}/toggle`, {}, { preserveScroll: true });
}

function deleteWebhook(id) {
    deletingId.value = id;
    router.delete(`/webhooks/${id}`, {
        preserveScroll: true,
        onFinish: () => {
            deletingId.value = null;
        },
    });
}
</script>

<template>
    <EmptyState
        v-if="webhooks.data.length === 0"
        :icon="Webhook"
        title="No webhook endpoints"
        description="Configure an endpoint to receive real-time event notifications."
        action-label="Create Endpoint"
        action-href="/webhooks/create"
    />

    <div v-else class="overflow-x-auto">
        <table class="w-full whitespace-nowrap">
            <thead>
                <tr class="border-b text-left text-sm text-muted-foreground">
                    <th class="px-4 py-3 font-medium">URL</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Events</th>
                    <th class="w-0 px-4 py-3 font-medium" />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="webhook in webhooks.data"
                    :key="webhook.id"
                    class="border-b last:border-0"
                >
                    <td class="px-4 py-3">
                        <p class="max-w-xs truncate text-sm font-medium">
                            {{ webhook.url }}
                        </p>
                        <p
                            v-if="webhook.description"
                            class="text-xs text-muted-foreground"
                        >
                            {{ webhook.description }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <Badge
                            :variant="
                                webhook.status === 'enabled'
                                    ? 'default'
                                    : 'secondary'
                            "
                        >
                            {{ webhook.status }}
                        </Badge>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ webhook.events?.length || 0 }} events
                    </td>
                    <td class="px-4 py-3 text-right">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="sm">
                                    <Spinner v-if="deletingId === webhook.id" />
                                    <MoreHorizontal v-else class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="`/webhooks/${webhook.id}`"
                                        prefetch="hover"
                                    >
                                        <Eye class="mr-2 size-4" />
                                        View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    @click="toggleWebhook(webhook)"
                                >
                                    <Power class="mr-2 size-4" />
                                    {{
                                        webhook.status === 'enabled'
                                            ? 'Disable'
                                            : 'Enable'
                                    }}
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <ConfirmDialog
                                    title="Delete webhook endpoint?"
                                    description="This will permanently delete this webhook endpoint."
                                    confirm-label="Delete"
                                    variant="destructive"
                                    @confirm="deleteWebhook(webhook.id)"
                                >
                                    <DropdownMenuItem
                                        class="text-destructive focus:text-destructive"
                                        @select.prevent
                                    >
                                        <Trash2 class="mr-2 size-4" />
                                        Delete
                                    </DropdownMenuItem>
                                </ConfirmDialog>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

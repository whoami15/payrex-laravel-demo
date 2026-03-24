<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2, Users } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import RawResponse from '@/components/RawResponse.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatTimestamp } from '@/lib/formatters';

const props = defineProps({
    customer: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/customers' },
    { title: props.customer.name || props.customer.id },
];

const processing = shallowRef(false);

function deleteCustomer() {
    processing.value = true;
    router.delete(`/customers/${props.customer.id}`, {
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<template>
    <Head :title="customer.name || 'Customer'" />

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
                            <Users class="size-6 text-muted-foreground" />
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-xl font-semibold tracking-tight">
                                {{ customer.name }}
                            </h1>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                {{ customer.email }}
                            </p>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                <code class="text-xs">{{ customer.id }}</code>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="`/customers/${customer.id}/edit`">
                                <Pencil class="size-4" />
                                Edit
                            </Link>
                        </Button>
                        <ConfirmDialog
                            title="Delete customer?"
                            description="This will permanently delete this customer from PayRex. This action cannot be undone."
                            confirm-label="Delete"
                            variant="destructive"
                            @confirm="deleteCustomer"
                        >
                            <Button
                                variant="destructive"
                                :disabled="processing"
                            >
                                <Spinner v-if="processing" />
                                <Trash2 v-else class="size-4" />
                                Delete
                            </Button>
                        </ConfirmDialog>
                    </div>
                </div>

                <div class="grid grid-cols-2 divide-x border-t xl:grid-cols-4">
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Phone
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            {{ customer.billing?.phone || '—' }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Currency
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            {{ customer.currency || '—' }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Statement Prefix
                        </p>
                        <p class="mt-1 text-sm font-medium">
                            <code
                                v-if="customer.billing_statement_prefix"
                                class="text-xs"
                                >{{ customer.billing_statement_prefix }}</code
                            >
                            <span v-else>—</span>
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Mode
                        </p>
                        <Badge variant="secondary" class="mt-1">
                            {{ customer.livemode ? 'Live' : 'Test' }}
                        </Badge>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-medium"
                            >Billing Address</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <template v-if="customer.billing?.address">
                            <div class="space-y-1 text-sm">
                                <p class="font-medium">
                                    {{ customer.billing.address.line1 || '—' }}
                                </p>
                                <p v-if="customer.billing.address.line2">
                                    {{ customer.billing.address.line2 }}
                                </p>
                                <p class="text-muted-foreground">
                                    {{
                                        [
                                            customer.billing.address.city,
                                            customer.billing.address.state,
                                            customer.billing.address
                                                .postal_code,
                                        ]
                                            .filter(Boolean)
                                            .join(', ')
                                    }}
                                </p>
                                <p
                                    v-if="customer.billing.address.country"
                                    class="text-muted-foreground"
                                >
                                    {{ customer.billing.address.country }}
                                </p>
                            </div>
                        </template>
                        <p v-else class="text-sm text-muted-foreground">
                            No billing address on file.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-medium"
                            >Timestamps</CardTitle
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-muted-foreground">
                                    Created
                                </p>
                                <p class="text-sm font-medium">
                                    {{ formatTimestamp(customer.created_at) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">
                                    Updated
                                </p>
                                <p class="text-sm font-medium">
                                    {{ formatTimestamp(customer.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <RawResponse :data="customer" />
        </div>
    </AppLayout>
</template>

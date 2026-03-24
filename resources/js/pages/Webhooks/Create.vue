<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import EventBadge from '@/components/EventBadge.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { toPhpArray } from '@/lib/formatters';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Webhooks', href: '/webhooks' },
    { title: 'Create' },
];

const eventOptions = [
    'payment_intent.succeeded',
    'payment_intent.amount_capturable',
    'cash_balance.funds_available',
    'checkout_session.expired',
    'payout.deposited',
    'refund.created',
    'refund.updated',
    'billing_statement.created',
    'billing_statement.updated',
    'billing_statement.deleted',
    'billing_statement.finalized',
    'billing_statement.sent',
    'billing_statement.marked_uncollectible',
    'billing_statement.voided',
    'billing_statement.paid',
    'billing_statement.will_be_due',
    'billing_statement.overdue',
    'billing_statement_line_item.created',
    'billing_statement_line_item.updated',
    'billing_statement_line_item.deleted',
];

const form = useForm({
    url: '',
    description: '',
    events: [],
});

function selectAll() {
    form.events = [...eventOptions];
}

function deselectAll() {
    form.events = [];
}

function submit() {
    form.post('/webhooks');
}

const liveCode = computed(() => {
    const params = {};

    if (form.url) params.url = form.url;
    if (form.description) params.description = form.description;
    if (form.events.length) params.events = form.events;

    return `use LegionHQ\\LaravelPayrex\\Facades\\Payrex;\n\n$webhook = Payrex::webhooks()->create(${toPhpArray(params)});`;
});
</script>

<template>
    <Head title="Create Webhook Endpoint" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Create Webhook Endpoint
                </h1>
                <p class="text-sm text-muted-foreground">
                    Register a URL to receive real-time event notifications from
                    PayRex.
                </p>
            </div>

            <div class="grid gap-6 xl:grid-cols-5">
                <form
                    class="min-w-0 divide-y rounded-xl border bg-card xl:col-span-3"
                    @submit.prevent="submit"
                >
                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Endpoint</p>
                            <p class="text-sm text-muted-foreground">
                                The URL that will receive webhook events.
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div class="space-y-2">
                                <Label for="url">URL</Label>
                                <Input
                                    id="url"
                                    v-model="form.url"
                                    type="url"
                                    required
                                    placeholder="https://your-app.com/webhooks/payrex"
                                />
                                <InputError :message="form.errors.url" />
                            </div>
                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <Input
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    placeholder="Production webhook"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Events</p>
                            <p class="text-sm text-muted-foreground">
                                Choose which events to listen to.
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">
                                    {{ form.events.length }} of
                                    {{ eventOptions.length }} selected
                                </span>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="
                                        form.events.length ===
                                        eventOptions.length
                                            ? deselectAll()
                                            : selectAll()
                                    "
                                >
                                    {{
                                        form.events.length ===
                                        eventOptions.length
                                            ? 'Deselect All'
                                            : 'Select All'
                                    }}
                                </Button>
                            </div>
                            <div
                                class="max-h-72 space-y-1 overflow-y-auto rounded-lg border p-2"
                            >
                                <label
                                    v-for="event in eventOptions"
                                    :key="event"
                                    class="flex cursor-pointer items-center gap-2.5 rounded-md px-2 py-1.5 hover:bg-muted/50"
                                >
                                    <input
                                        type="checkbox"
                                        :value="event"
                                        v-model="form.events"
                                        class="size-4 shrink-0 rounded-[5px] border border-input/60 accent-primary"
                                    />
                                    <EventBadge :event="event" />
                                </label>
                            </div>
                            <InputError :message="form.errors.events" />
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div />
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            Create Endpoint
                        </Button>
                    </div>
                </form>

                <div class="xl:col-span-2">
                    <div class="xl:sticky xl:top-4">
                        <CodeBlock title="API Call Preview" :code="liveCode" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

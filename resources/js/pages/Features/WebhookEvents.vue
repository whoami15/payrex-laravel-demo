<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import EventBadge from '@/components/EventBadge.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    eventTypes: { type: Array, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Webhook Events' },
];

const grouped = computed(() => {
    const groups = {};
    for (const event of props.eventTypes) {
        const resource = event.value.split('.')[0];
        if (!groups[resource]) groups[resource] = [];
        groups[resource].push(event);
    }
    return groups;
});

const resourceLabels = {
    payment_intent: 'Payment Intents',
    cash_balance: 'Cash Balance',
    checkout_session: 'Checkout Sessions',
    payout: 'Payouts',
    refund: 'Refunds',
    billing_statement: 'Billing Statements',
    billing_statement_line_item: 'Line Items',
};
</script>

<template>
    <Head title="Webhook Events" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="Webhook Events"
                docs="guide/webhooks"
                controller="app/Http/Controllers/Feature/WebhookEventsController.php"
            >
                The package dispatches typed Laravel events for each webhook,
                making it easy to react to PayRex events in your application.
            </FeatureHeader>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium">
                                    Available Event Types
                                </CardTitle>
                                <Badge variant="secondary">
                                    {{ eventTypes.length }} events
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-5">
                            <div
                                v-for="(events, resource) in grouped"
                                :key="resource"
                            >
                                <p
                                    class="mb-2 text-xs font-medium tracking-wider text-muted-foreground uppercase"
                                >
                                    {{ resourceLabels[resource] || resource }}
                                </p>
                                <div class="space-y-1">
                                    <div
                                        v-for="event in events"
                                        :key="event.value"
                                        class="flex items-center justify-between gap-2 rounded-md px-2 py-1.5"
                                    >
                                        <EventBadge :event="event.value" />
                                        <code
                                            class="hidden truncate text-xs text-muted-foreground sm:block"
                                        >
                                            {{ event.event_class }}
                                        </code>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-4">
                    <CodeBlock
                        title="Listening to Events"
                        code="
// In EventServiceProvider or anywhere
use LegionHQ\LaravelPayrex\Events\{
    PaymentIntentSucceeded,
    WebhookReceived,
};

// Listen to a specific event
Event::listen(
    PaymentIntentSucceeded::class,
    function (PaymentIntentSucceeded $event) {
        /** @var PaymentIntent $paymentIntent */
        $paymentIntent = $event->data();

        Order::where('payment_intent_id', $paymentIntent->id)
            ->update(['status' => 'paid']);
    }
);

// Catch-all for any webhook
Event::listen(
    WebhookReceived::class,
    function (WebhookReceived $event) {
        Log::info('Webhook received', [
            'type' => $event->eventType()?->value,
            'livemode' => $event->isLiveMode(),
        ]);
    }
);
                    "
                    />

                    <CodeBlock
                        title="Webhook Setup"
                        code="
# .env
PAYREX_WEBHOOK_ENABLED=true
PAYREX_WEBHOOK_SECRET=your_webhook_secret

// The package auto-registers:
// POST /payrex/webhook

# Or manual verification:
$event = Payrex::constructEvent(
    $payload,
    $signatureHeader,
    $secret,
);
                    "
                    />

                    <CodeBlock
                        title="Idempotent Handling"
                        code="
// Webhooks may be delivered more than once.
// Use the event ID to deduplicate:
Event::listen(
    PaymentIntentSucceeded::class,
    function (PaymentIntentSucceeded $event) {
        $eventId = $event->payload['id'];

        // Skip if already processed
        if (DB::table('processed_webhook_events')
            ->where('event_id', $eventId)->exists()) {
            return;
        }

        // Process the event
        /** @var PaymentIntent $paymentIntent */
        $paymentIntent = $event->data();
        Order::where('payment_intent_id', $paymentIntent->id)
            ->update(['status' => 'paid']);

        // Record that we processed it
        DB::table('processed_webhook_events')->insert([
            'event_id' => $eventId,
            'processed_at' => now(),
        ]);
    }
);
                    "
                    />

                    <CodeBlock
                        title="Test Webhooks Locally"
                        lang="bash"
                        code="
# Dispatch a synthetic webhook event
php artisan payrex:webhook-test payment_intent.succeeded

# List all webhook endpoints
php artisan payrex:webhook-list
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, useHttp } from '@inertiajs/vue3';
import { KeyRound, ShieldCheck } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    fixtures: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'constructEvent()' },
];

const result = shallowRef(null);
const eventTypes = Object.keys(props.fixtures);
const selectedEventType = shallowRef(eventTypes[0]);

function randomId(prefix) {
    const chars =
        'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let id = '';
    for (let i = 0; i < 24; i++) {
        id += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return `${prefix}_${id}`;
}

function buildPayload(type) {
    const fixture = JSON.parse(JSON.stringify(props.fixtures[type]));
    fixture.id = randomId('evt');
    return JSON.stringify(fixture, null, 2);
}

const verifyHttp = useHttp({
    payload: buildPayload(selectedEventType.value),
    signature: 't=1234567890,te=invalid_sig,li=invalid_sig',
    secret: 'whsec_test_secret',
});

const signatureHttp = useHttp({
    payload: '',
    secret: '',
});

function selectEventType(type) {
    selectedEventType.value = type;
    verifyHttp.payload = buildPayload(type);
    verifyHttp.signature = 't=1234567890,te=invalid_sig,li=invalid_sig';
    result.value = null;
}

function verify() {
    result.value = null;

    verifyHttp.post('/features/construct-event/verify', {
        onSuccess: (data) => {
            result.value = data;
        },
        onError: () => {
            result.value = { error: 'Request failed.' };
        },
    });
}

function generateSignature() {
    signatureHttp
        .transform(() => ({
            payload: verifyHttp.payload,
            secret: verifyHttp.secret,
        }))
        .post('/features/construct-event/generate-signature', {
            onSuccess: (data) => {
                verifyHttp.signature = data.signature;
            },
        });
}
</script>

<template>
    <Head title="constructEvent()" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="constructEvent()"
                docs="guide/webhooks"
                controller="app/Http/Controllers/Feature/ConstructEventController.php"
            >
                Manually verify webhook signatures and parse events without
                using the built-in webhook route. Useful when you need full
                control over webhook handling.
            </FeatureHeader>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card">
                        <div class="flex items-start gap-4 p-6">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                            >
                                <ShieldCheck
                                    class="size-6 text-muted-foreground"
                                />
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold">Live Demo</h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Select an event type, generate a valid
                                    signature, then verify. Each attempt uses a
                                    unique event ID.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3 border-t px-6 py-4">
                            <div class="space-y-2">
                                <Label>Event Type</Label>
                                <Select
                                    :model-value="selectedEventType"
                                    @update:model-value="selectEventType"
                                >
                                    <SelectTrigger class="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="type in eventTypes"
                                            :key="type"
                                            :value="type"
                                        >
                                            {{ type }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Webhook Payload (JSON)</Label>
                                <textarea
                                    v-model="verifyHttp.payload"
                                    rows="12"
                                    class="w-full rounded-lg border border-input bg-transparent px-3 py-2 font-mono text-xs shadow-xs transition-all duration-150 focus-visible:border-primary/50 focus-visible:ring-[3px] focus-visible:ring-primary/20"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Signature Header</Label>
                                <div class="flex gap-2">
                                    <Input
                                        v-model="verifyHttp.signature"
                                        class="font-mono text-xs"
                                    />
                                    <Button
                                        variant="outline"
                                        :disabled="signatureHttp.processing"
                                        @click="generateSignature"
                                    >
                                        <KeyRound
                                            v-if="!signatureHttp.processing"
                                            class="size-3.5"
                                        />
                                        <Spinner v-else class="size-3.5" />
                                        Generate
                                    </Button>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    Click "Generate" to compute a valid
                                    HMAC-SHA256 signature, or leave the default
                                    invalid signature to see error handling.
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label>Webhook Secret</Label>
                                <Input
                                    v-model="verifyHttp.secret"
                                    class="font-mono text-xs"
                                />
                                <p class="text-sm text-muted-foreground">
                                    The secret used to sign and verify the
                                    payload. In production, this is your
                                    <code>PAYREX_WEBHOOK_SECRET</code> from
                                    <code>.env</code>.
                                </p>
                            </div>
                            <Button
                                :disabled="verifyHttp.processing"
                                @click="verify"
                            >
                                <Spinner v-if="verifyHttp.processing" />
                                Verify Signature
                            </Button>
                        </div>
                    </div>

                    <div
                        v-if="result"
                        class="rounded-xl border p-4"
                        :class="
                            result.success
                                ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/50'
                                : 'border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-950/50'
                        "
                    >
                        <p
                            class="mb-2 text-sm font-semibold"
                            :class="
                                result.success
                                    ? 'text-emerald-800 dark:text-emerald-200'
                                    : 'text-red-800 dark:text-red-200'
                            "
                        >
                            {{
                                result.success
                                    ? 'Verified'
                                    : 'Verification Failed'
                            }}
                        </p>
                        <pre
                            class="overflow-auto text-xs"
                            :class="
                                result.success
                                    ? 'text-emerald-700 dark:text-emerald-300'
                                    : 'text-red-700 dark:text-red-300'
                            "
                            >{{ JSON.stringify(result, null, 2) }}</pre
                        >
                    </div>
                </div>

                <div class="space-y-4">
                    <CodeBlock
                        title="Basic Usage"
                        code="
use LegionHQ\LaravelPayrex\Facades\Payrex;
use LegionHQ\LaravelPayrex\Exceptions\WebhookVerificationException;

// In your own webhook controller
public function handleWebhook(Request $request): Response
{
    $payload = $request->getContent();
    $signature = $request->header('Payrex-Signature');

    try {
        $event = Payrex::constructEvent(
            $payload,
            $signature,
            config('payrex.webhook.secret'),
        );

        $type = $event->eventType(); // WebhookEventType enum

        /** @var PaymentIntent $data */
        $data = $event->data();

        return response('OK', 200);
    } catch (WebhookVerificationException $e) {
        return response('Invalid signature', 400);
    }
}
                    "
                    />

                    <CodeBlock
                        title="Multi-Tenant Usage"
                        code="
// Each tenant has their own PayRex webhook secret
$tenant = Tenant::current();

try {
    $event = Payrex::constructEvent(
        $payload,
        $signature,
        $tenant->payrex_webhook_secret, // per-tenant secret
    );

    // Process for this specific tenant
    /** @var PaymentIntent $paymentIntent */
    $paymentIntent = $event->data();
} catch (WebhookVerificationException $e) {
    Log::warning('Invalid webhook for tenant', [
        'tenant_id' => $tenant->id,
    ]);
}
                    "
                    />

                    <CodeBlock
                        title="Override payrexClient() for Multi-Tenant"
                        code="
use LegionHQ\LaravelPayrex\Concerns\HasPayrexCustomer;
use LegionHQ\LaravelPayrex\PayrexClient;

class User extends Authenticatable
{
    use HasPayrexCustomer;

    // Each user/tenant uses their own API key
    protected function payrexClient(): PayrexClient
    {
        return new PayrexClient(
            secretKey: $this->team->payrex_secret_key,
            currency: $this->team->currency ?? 'PHP',
        );
    }
}
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

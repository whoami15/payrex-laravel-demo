<script setup>
import { Head, useHttp } from '@inertiajs/vue3';
import { KeyRound, RefreshCw } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Idempotency Keys' },
];

const http = useHttp({ idempotency_key: '' });
const result = shallowRef(null);

function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0;
        return (c === 'x' ? r : (r & 0x3) | 0x8).toString(16);
    });
}

const idempotencyKey = shallowRef(generateUUID());

function generateKey() {
    idempotencyKey.value = generateUUID();
}

function createWithKey() {
    result.value = null;
    http.idempotency_key = idempotencyKey.value;

    http.post('/features/idempotency/create', {
        onSuccess: (data) => {
            result.value = data;
        },
        onError: () => {
            result.value = { error: 'Request failed.' };
        },
    });
}
</script>

<template>
    <Head title="Idempotency Keys" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="Idempotency Keys"
                docs="guide/error-handling#idempotency-keys"
                controller="app/Http/Controllers/Feature/IdempotencyController.php"
            >
                Pass idempotency keys to create and action methods to prevent
                duplicate operations. The package sends the header - the PayRex
                API will honor it once support is added.
            </FeatureHeader>

            <div
                class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/50"
            >
                <div class="text-sm">
                    <span
                        class="font-medium text-amber-800 dark:text-amber-200"
                    >
                        Not yet supported by PayRex.
                    </span>
                    <span class="text-amber-700 dark:text-amber-300">
                        The PayRex API does not honor the
                        <code class="bg-amber-100! dark:bg-amber-900!"
                            >Idempotency-Key</code
                        >
                        header yet.
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card">
                        <div class="flex items-start gap-4 p-6">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                            >
                                <KeyRound
                                    class="size-6 text-muted-foreground"
                                />
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold">Live Demo</h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    The key is sent as a header but has no
                                    effect on the API yet. This demo shows how
                                    the package API works.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3 border-t px-6 py-4">
                            <div class="space-y-2">
                                <Label>Idempotency Key</Label>
                                <div class="flex gap-2">
                                    <Input
                                        v-model="idempotencyKey"
                                        readonly
                                        class="font-mono text-xs"
                                    />
                                    <Button
                                        variant="outline"
                                        @click="generateKey"
                                    >
                                        <RefreshCw class="size-4" />
                                        New
                                    </Button>
                                </div>
                            </div>
                            <Button
                                :disabled="http.processing"
                                @click="createWithKey"
                            >
                                <Spinner v-if="http.processing" />
                                Create Customer
                            </Button>
                        </div>
                    </div>

                    <CodeBlock
                        v-if="result"
                        lang="json"
                        title="Result"
                        :code="JSON.stringify(result, null, 2)"
                    />
                </div>

                <div class="space-y-4">
                    <CodeBlock
                        title="Usage"
                        code="
use Illuminate\Support\Str;
use LegionHQ\LaravelPayrex\Facades\Payrex;

$idempotencyKey = Str::uuid()->toString();

// Pass as second argument to create methods
$paymentIntent = Payrex::paymentIntents()->create(
    [
        'amount' => 100000,
        'payment_methods' => ['card'],
    ],
    $idempotencyKey  // prevents duplicate charges
);

// Works on all create and action methods:
Payrex::customers()->create($params, $key);
Payrex::refunds()->create($params, $key);
Payrex::checkoutSessions()->create($params, $key);
Payrex::paymentIntents()->cancel($id, $key);
Payrex::paymentIntents()->capture($id, $params, $key);
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

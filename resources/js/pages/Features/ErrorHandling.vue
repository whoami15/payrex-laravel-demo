<script setup>
import { Head, useHttp } from '@inertiajs/vue3';
import { AlertTriangle } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Error Handling' },
];

const http = useHttp({ type: '' });
const result = shallowRef(null);

function triggerError(type) {
    result.value = null;
    http.type = type;

    http.post('/features/error-handling/trigger', {
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
    <Head title="Error Handling" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="Error Handling"
                docs="guide/error-handling"
                controller="app/Http/Controllers/Feature/ErrorHandlingController.php"
            >
                The package provides typed exceptions for every API error,
                making it easy to handle specific failure cases.
            </FeatureHeader>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card">
                        <div class="flex items-start gap-4 p-6">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                            >
                                <AlertTriangle
                                    class="size-6 text-muted-foreground"
                                />
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold">
                                    Trigger Errors
                                </h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Click to trigger real API errors and see how
                                    typed exceptions catch them.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 border-t px-6 py-4">
                            <Button
                                size="sm"
                                variant="destructive"
                                :disabled="http.processing"
                                @click="triggerError('not_found')"
                            >
                                <Spinner
                                    v-if="
                                        http.processing &&
                                        http.type === 'not_found'
                                    "
                                />
                                404 Not Found
                            </Button>
                            <Button
                                size="sm"
                                variant="destructive"
                                :disabled="http.processing"
                                @click="triggerError('invalid_request')"
                            >
                                <Spinner
                                    v-if="
                                        http.processing &&
                                        http.type === 'invalid_request'
                                    "
                                />
                                400 Invalid Request
                            </Button>
                        </div>
                    </div>

                    <div
                        v-if="result?.error"
                        class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-950/50"
                    >
                        <p
                            class="mb-2 text-sm font-semibold text-red-800 dark:text-red-200"
                        >
                            Caught Exception
                        </p>
                        <pre
                            class="overflow-auto text-xs text-red-700 dark:text-red-300"
                            >{{ JSON.stringify(result.error, null, 2) }}</pre
                        >
                    </div>
                </div>

                <div class="space-y-4">
                    <CodeBlock
                        title="Exception Hierarchy"
                        lang="plaintext"
                        code="
PayrexException (base)
├── PayrexApiException (HTTP errors)
│   ├── AuthenticationException (401)
│   ├── InvalidRequestException (400)
│   ├── RateLimitException (429)
│   └── ResourceNotFoundException (404)
└── WebhookVerificationException
                    "
                    />

                    <CodeBlock
                        title="Usage Pattern"
                        code="
use LegionHQ\LaravelPayrex\Exceptions\{
    PayrexApiException,
    ResourceNotFoundException,
    InvalidRequestException,
    AuthenticationException,
    RateLimitException,
};

try {
    $customer = Payrex::customers()
        ->retrieve('cus_nonexistent');
} catch (ResourceNotFoundException $e) {
    // Handle 404 specifically
    Log::warning('Customer not found', [
        'status' => $e->statusCode, // 404
        'errors' => $e->errors,
    ]);
} catch (AuthenticationException $e) {
    // Invalid API key
} catch (RateLimitException $e) {
    // Too many requests - back off
} catch (InvalidRequestException $e) {
    // Bad request params
    // $e->errors has field-level details
} catch (PayrexApiException $e) {
    // Catch-all for other API errors
}
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

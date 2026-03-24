<script setup>
import { Head, useHttp } from '@inertiajs/vue3';
import { FileSearch } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Response Metadata' },
];

const http = useHttp({});
const result = shallowRef(null);

function fetchMetadata() {
    result.value = null;

    http.post('/features/response-metadata/fetch', {
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
    <Head title="Response Metadata" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="API Response Metadata"
                docs="guide/error-handling#response-metadata"
                controller="app/Http/Controllers/Feature/ResponseMetadataController.php"
            >
                Access HTTP response headers and status codes from the last API
                call using <code>getLastResponse()</code>.
            </FeatureHeader>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card">
                        <div class="flex items-start gap-4 p-6">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                            >
                                <FileSearch
                                    class="size-6 text-muted-foreground"
                                />
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold">Live Demo</h2>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Make an API call and inspect the response
                                    metadata.
                                </p>
                            </div>
                        </div>

                        <div class="border-t px-6 py-4">
                            <Button
                                :disabled="http.processing"
                                @click="fetchMetadata"
                            >
                                <Spinner v-if="http.processing" />
                                Fetch & Inspect
                            </Button>
                        </div>
                    </div>

                    <CodeBlock
                        v-if="result"
                        lang="json"
                        title="Response Metadata"
                        :code="JSON.stringify(result, null, 2)"
                    />
                </div>

                <div class="space-y-4">
                    <CodeBlock
                        title="Usage"
                        code="
use LegionHQ\LaravelPayrex\Facades\Payrex;

// Make any API call
$customers = Payrex::customers()->list();

// Then inspect the last response
$metadata = Payrex::getLastResponse();

$metadata->statusCode;  // 200
$metadata->headers;     // ['content-type' => '...', ...]

// Case-insensitive header access
$metadata->header('Content-Type');
$metadata->header('x-request-id');
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

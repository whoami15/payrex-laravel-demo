<script setup>
import { Head } from '@inertiajs/vue3';
import { Terminal } from 'lucide-vue-next';
import CodeBlock from '@/components/CodeBlock.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Artisan Commands' },
];

const commands = [
    {
        name: 'payrex:webhook-create',
        description:
            'Interactively create a new webhook endpoint. Prompts for URL, events, and description.',
        example: 'php artisan payrex:webhook-create',
    },
    {
        name: 'payrex:webhook-list',
        description: 'List all webhook endpoints registered with PayRex.',
        example: 'php artisan payrex:webhook-list',
    },
    {
        name: 'payrex:webhook-update {id}',
        description:
            'Update an existing webhook endpoint URL, events, or description.',
        example: 'php artisan payrex:webhook-update we_abc123',
    },
    {
        name: 'payrex:webhook-toggle {id}',
        description:
            'Enable or disable a webhook endpoint. Automatically detects current status and flips it.',
        example: 'php artisan payrex:webhook-toggle we_abc123',
    },
    {
        name: 'payrex:webhook-delete {id}',
        description:
            'Delete a webhook endpoint. Asks for confirmation before deleting.',
        example: 'php artisan payrex:webhook-delete we_abc123',
    },
    {
        name: 'payrex:webhook-test {type?}',
        description:
            'Dispatch a synthetic webhook event locally for testing your event listeners.',
        example: 'php artisan payrex:webhook-test payment_intent.succeeded',
    },
];
</script>

<template>
    <Head title="Artisan Commands" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="Artisan Commands"
                docs="guide/artisan-commands"
            >
                The package provides artisan commands to manage webhook
                endpoints and test events directly from the command line.
            </FeatureHeader>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-muted"
                        >
                            <Terminal class="size-5 text-muted-foreground" />
                        </div>
                        <div>
                            <CardTitle class="text-sm font-medium">
                                Available Commands
                            </CardTitle>
                            <p class="text-sm text-muted-foreground">
                                {{ commands.length }} commands for webhook
                                management and testing.
                            </p>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        v-for="cmd in commands"
                        :key="cmd.name"
                        class="space-y-2"
                    >
                        <div>
                            <code class="text-sm font-semibold">{{
                                cmd.name
                            }}</code>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                {{ cmd.description }}
                            </p>
                        </div>
                        <CodeBlock lang="bash" :code="cmd.example" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

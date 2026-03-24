<script setup>
import { Code, CreditCard, Palette, Webhook } from 'lucide-vue-next';
import { markRaw } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';

const steps = [
    {
        icon: markRaw(Code),
        title: 'Create Payment Intent',
        description:
            'Your backend calls Payrex::paymentIntents()->create() and returns the client_secret.',
    },
    {
        icon: markRaw(Palette),
        title: 'Initialize & Customize',
        description:
            'Pass your brand color, choose layout, and pre-fill billing details.',
    },
    {
        icon: markRaw(CreditCard),
        title: 'Mount & Pay',
        description:
            'The embedded form renders payment methods. Customer completes payment.',
    },
    {
        icon: markRaw(Webhook),
        title: 'Verify via Webhook',
        description:
            "Listen for payment_intent.succeeded - don't rely on the redirect alone.",
    },
];
</script>

<template>
    <div class="space-y-4">
        <div class="rounded-xl border bg-card p-6">
            <h2 class="text-sm font-semibold">How It Works</h2>
            <p class="mt-1 text-sm text-muted-foreground">
                The PayRex Elements integration flow.
            </p>
            <div class="mt-5 space-y-4">
                <div
                    v-for="(step, i) in steps"
                    :key="step.title"
                    class="flex gap-4 last:pb-0 [&:not(:last-child)>div:last-child]:pb-4"
                >
                    <div class="flex flex-col items-center">
                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted"
                        >
                            <component
                                :is="step.icon"
                                class="size-4 text-muted-foreground"
                            />
                        </div>
                        <div
                            v-if="i < steps.length - 1"
                            class="mt-1 h-full w-px bg-border"
                        />
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ step.title }}</p>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            {{ step.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <CodeBlock
            lang="javascript"
            title="Customization Code"
            code="
// 1. Brand color via style.variables
const elements = payrex.elements({
    clientSecret: data.client_secret,
    style: {
        variables: {
            primaryColor: '#6A63EF',
        },
    },
});

// 2. Pre-fill billing + control field visibility
const paymentElement = elements.create('payment', {
    layout: 'accordion',
    defaultValues: {
        billingDetails: {
            name: 'Juan Dela Cruz',
            email: 'juan@example.com',
            phone: '+639170000000',
            address: {
                line1: '123 Main St',
                country: 'PH',
                city: 'Manila',
                state: 'NCR',
                postalCode: '1000',
            },
        },
    },
    fields: {
        billingDetails: {
            email: 'always',  // or 'auto'
            name: 'always',
            phone: 'auto',
            address: {
                line1: 'auto',
                city: 'auto',
                postalCode: 'auto',
                state: 'auto',
                country: 'auto',
            },
        },
    },
});
            "
        />
    </div>
</template>

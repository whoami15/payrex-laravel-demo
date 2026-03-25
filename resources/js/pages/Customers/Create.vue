<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { toPhpArray } from '@/lib/formatters';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/customers' },
    { title: 'Create' },
];

const form = useForm({
    name: '',
    email: '',
    phone: '',
    line1: '',
    line2: '',
    city: '',
    state: '',
    postal_code: '',
    country: 'PH',
});

function submit() {
    form.post('/customers');
}

const liveCode = computed(() => {
    const params = {};

    if (form.name) params.name = form.name;
    if (form.email) params.email = form.email;

    const billing = {};
    if (form.phone) billing.phone = form.phone;

    const address = {};
    if (form.line1) address.line1 = form.line1;
    if (form.line2) address.line2 = form.line2;
    if (form.city) address.city = form.city;
    if (form.state) address.state = form.state;
    if (form.postal_code) address.postal_code = form.postal_code;
    if (Object.keys(address).length) {
        address.country = 'PH';
        billing.address = address;
    }

    if (Object.keys(billing).length) {
        params.billing_details = billing;
    }

    return `use LegionHQ\\LaravelPayrex\\Facades\\Payrex;\n\n$customer = Payrex::customers()->create(${toPhpArray(params)});`;
});
</script>

<template>
    <Head title="Create Customer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Create Customer
                </h1>
                <p class="text-sm text-muted-foreground">
                    Add a new customer to PayRex.
                </p>
            </div>

            <div class="grid gap-6 xl:grid-cols-5">
                <form
                    class="min-w-0 divide-y rounded-xl border bg-card xl:col-span-3"
                    @submit.prevent="submit"
                >
                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Basic Info</p>
                            <p class="text-sm text-muted-foreground">
                                Name and email are required.
                            </p>
                        </div>
                        <div class="min-w-0 space-y-3">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Juan dela Cruz"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="juan@example.com"
                                />
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="space-y-2">
                                <Label for="phone">Phone</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="+639123456789"
                                />
                                <InputError :message="form.errors.phone" />
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Billing Address</p>
                            <p class="text-sm text-muted-foreground">
                                Optional. Used for billing statements.
                            </p>
                        </div>
                        <div class="min-w-0 space-y-3">
                            <div class="space-y-2">
                                <Label for="line1">Address Line 1</Label>
                                <Input
                                    id="line1"
                                    v-model="form.line1"
                                    type="text"
                                    placeholder="123 Rizal Street"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label for="line2">Address Line 2</Label>
                                <Input
                                    id="line2"
                                    v-model="form.line2"
                                    type="text"
                                    placeholder="Barangay San Antonio"
                                />
                            </div>
                            <div class="grid gap-3 lg:grid-cols-3">
                                <div class="space-y-2">
                                    <Label for="city">City</Label>
                                    <Input
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        placeholder="Makati"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="state">State / Province</Label>
                                    <Input
                                        id="state"
                                        v-model="form.state"
                                        type="text"
                                        placeholder="Metro Manila"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="postal_code">Postal Code</Label>
                                    <Input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        type="text"
                                        placeholder="1200"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div />
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            Create Customer
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

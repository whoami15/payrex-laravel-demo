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

const props = defineProps({
    customer: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/customers' },
    { title: props.customer.name || props.customer.id },
    { title: 'Edit' },
];

const form = useForm({
    name: props.customer.name || '',
    email: props.customer.email || '',
    phone: props.customer.billing?.phone || '',
    line1: props.customer.billing?.address?.line1 || '',
    line2: props.customer.billing?.address?.line2 || '',
    city: props.customer.billing?.address?.city || '',
    state: props.customer.billing?.address?.state || '',
    postal_code: props.customer.billing?.address?.postal_code || '',
    country: props.customer.billing?.address?.country || 'PH',
});

function submit() {
    form.put(`/customers/${props.customer.id}`, {
        onSuccess: () => form.defaults(),
    });
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
        address.country = form.country || 'PH';
        billing.address = address;
    }

    if (Object.keys(billing).length) {
        params.billing_details = billing;
    }

    const php = toPhpArray(params).replace(/\n/g, '\n    ');

    return `use LegionHQ\\LaravelPayrex\\Facades\\Payrex;\n\n$customer = Payrex::customers()->update(\n    '${props.customer.id}',\n    ${php},\n);`;
});
</script>

<template>
    <Head :title="`Edit ${customer.name || 'Customer'}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Edit Customer
                </h1>
                <p class="text-sm text-muted-foreground">
                    Update {{ customer.name }}'s details.
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
                                Name, email, and phone.
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
                                />
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="space-y-2">
                                <Label for="phone">Phone</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="+639171234567"
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
                                <InputError :message="form.errors.line1" />
                            </div>
                            <div class="space-y-2">
                                <Label for="line2">Address Line 2</Label>
                                <Input
                                    id="line2"
                                    v-model="form.line2"
                                    type="text"
                                    placeholder="Barangay San Antonio"
                                />
                                <InputError :message="form.errors.line2" />
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
                                    <InputError :message="form.errors.city" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="state">State / Province</Label>
                                    <Input
                                        id="state"
                                        v-model="form.state"
                                        type="text"
                                        placeholder="Metro Manila"
                                    />
                                    <InputError :message="form.errors.state" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="postal_code">Postal Code</Label>
                                    <Input
                                        id="postal_code"
                                        v-model="form.postal_code"
                                        type="text"
                                        placeholder="1200"
                                    />
                                    <InputError
                                        :message="form.errors.postal_code"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <Button
                            type="submit"
                            :disabled="form.processing || !form.isDirty"
                            class="sm:col-start-2"
                        >
                            <Spinner v-if="form.processing" />
                            Save Changes
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

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import InputError from '@/components/InputError.vue';
import ToggleGrid from '@/components/ToggleGrid.vue';
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
import { toPhpArray } from '@/lib/formatters';
import { paymentMethodOptions } from '@/lib/paymentMethods';

const props = defineProps({
    products: { type: Array, required: true },
    customers: { type: Array, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payment Intents', href: '/payment-intents' },
    { title: 'Create' },
];

const form = useForm({
    amount: '',
    description: '',
    payment_methods: ['card', 'gcash'],
    capture_type: 'automatic',
    user_id: '',
    statement_descriptor: '',
    metadata: {},
});

function selectProduct(product) {
    form.amount = (product.price / 100).toFixed(2);
    form.description = product.name;
}

function togglePaymentMethod(method) {
    form.payment_methods = form.payment_methods.includes(method)
        ? form.payment_methods.filter((m) => m !== method)
        : [...form.payment_methods, method];
}

const selectedCustomer = computed(() => {
    if (!form.user_id) return null;
    return props.customers.find((c) => c.id === parseInt(form.user_id));
});

function submit() {
    form.post('/payment-intents');
}

const liveCode = computed(() => {
    const params = {};

    if (form.amount) params.amount = Math.round(parseFloat(form.amount) * 100);
    if (form.description) params.description = form.description;
    if (form.payment_methods.length)
        params.payment_methods = form.payment_methods;
    if (form.capture_type === 'manual') {
        params.payment_method_options = { card: { capture_type: 'manual' } };
    }
    if (form.statement_descriptor)
        params.statement_descriptor = form.statement_descriptor;
    if (selectedCustomer.value) {
        params.customer_id =
            selectedCustomer.value.payrex_customer_id ||
            '$user->payrexCustomerId()';
    }

    let code = toPhpArray(params);
    code = code.replace(
        /'\$user->payrexCustomerId\(\)'/g,
        '$user->payrexCustomerId()',
    );

    return `use LegionHQ\\LaravelPayrex\\Facades\\Payrex;\n\n$paymentIntent = Payrex::paymentIntents()->create(${code});`;
});
</script>

<template>
    <Head title="Create Payment Intent" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Create Payment Intent
                </h1>
                <p class="text-sm text-muted-foreground">
                    Create a server-side payment intent.
                </p>
            </div>

            <div
                class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700 dark:border-blue-900/50 dark:bg-blue-950/50 dark:text-blue-300"
            >
                This creates a payment intent via the API. To complete an actual
                payment, use
                <Link
                    href="/elements"
                    class="font-medium underline underline-offset-4"
                    >PayRex Elements</Link
                >
                or
                <Link
                    href="/checkout-sessions/create"
                    class="font-medium underline underline-offset-4"
                    >Checkout Sessions</Link
                >.
            </div>

            <div class="grid gap-6 xl:grid-cols-5">
                <form
                    class="min-w-0 divide-y rounded-xl border bg-card xl:col-span-3"
                    @submit.prevent="submit"
                >
                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Amount</p>
                            <p class="text-sm text-muted-foreground">
                                How much to charge.
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    v-for="product in products"
                                    :key="product.id"
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    class="font-medium"
                                    @click="selectProduct(product)"
                                >
                                    {{ product.name }} - ₱{{
                                        (product.price / 100).toLocaleString()
                                    }}
                                </Button>
                            </div>
                            <div class="mt-6 grid gap-3 lg:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="amount">Amount (₱)</Label>
                                    <Input
                                        id="amount"
                                        v-model="form.amount"
                                        type="number"
                                        min="1"
                                        step="0.01"
                                        required
                                        placeholder="1000.00"
                                    />
                                    <InputError :message="form.errors.amount" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="description">Description</Label>
                                    <Input
                                        id="description"
                                        v-model="form.description"
                                        type="text"
                                        required
                                        placeholder="Order #1234"
                                    />
                                    <InputError
                                        :message="form.errors.description"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Payment Methods</p>
                            <p class="text-sm text-muted-foreground">
                                Accepted payment methods.
                            </p>
                        </div>
                        <div>
                            <ToggleGrid
                                :options="paymentMethodOptions"
                                :selected="form.payment_methods"
                                @toggle="togglePaymentMethod"
                            />
                            <InputError
                                :message="form.errors.payment_methods"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Options</p>
                            <p class="text-sm text-muted-foreground">
                                Customer, capture, and statement descriptor.
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div class="space-y-2">
                                <Label for="user_id">Customer (optional)</Label>
                                <Select id="user_id" v-model="form.user_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="No customer"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="c in customers"
                                            :key="c.id"
                                            :value="String(c.id)"
                                        >
                                            {{ c.name }} ({{ c.email }}){{
                                                c.payrex_customer_id
                                                    ? ''
                                                    : ' — new'
                                            }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="grid gap-3 lg:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="capture_type"
                                        >Capture Type</Label
                                    >
                                    <Select
                                        id="capture_type"
                                        v-model="form.capture_type"
                                    >
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="Automatic"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="automatic"
                                                >Automatic</SelectItem
                                            >
                                            <SelectItem value="manual"
                                                >Manual (hold then
                                                capture)</SelectItem
                                            >
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="space-y-2">
                                    <Label for="statement_descriptor"
                                        >Statement Descriptor</Label
                                    >
                                    <Input
                                        id="statement_descriptor"
                                        v-model="form.statement_descriptor"
                                        type="text"
                                        maxlength="22"
                                        placeholder="MYSTORE"
                                    />
                                    <p class="text-sm text-muted-foreground">
                                        Appears on customer's bank statement
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div />
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            Create Payment Intent
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

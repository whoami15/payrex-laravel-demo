<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import LineItemList from './components/LineItemList.vue';
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
    { title: 'Checkout Sessions', href: '/checkout-sessions' },
    { title: 'Create' },
];

const form = useForm({
    line_items: [],
    description: '',
    payment_methods: ['card', 'gcash'],
    user_id: '',
    submit_type: '',
});

function addProduct(product) {
    const existing = form.line_items.find((item) => item.name === product.name);
    if (existing) {
        existing.quantity++;
        return;
    }

    form.line_items.push({
        name: product.name,
        amount: (product.price / 100).toFixed(2),
        quantity: 1,
        description: product.description,
        image: product.image || '',
    });
}

function removeLineItem(index) {
    form.line_items.splice(index, 1);
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
    form.post('/checkout-sessions');
}

const liveCode = computed(() => {
    const params = {};

    if (form.line_items.length) {
        params.line_items = form.line_items.map((item) => ({
            name: item.name,
            amount: Math.round(parseFloat(item.amount) * 100),
            quantity: item.quantity,
        }));
    }

    if (form.payment_methods.length) {
        params.payment_methods = form.payment_methods;
    }

    params.success_url = "route('checkout.success')";
    params.cancel_url = "route('checkout.cancel')";

    if (form.description) params.description = form.description;
    if (form.submit_type) params.submit_type = form.submit_type;
    if (selectedCustomer.value) {
        params.customer_id =
            selectedCustomer.value.payrex_customer_id ||
            '$user->payrexCustomerId()';
    }

    let code = toPhpArray(params);

    code = code.replace(
        /'route\('checkout\.success'\)'/g,
        "route('checkout.success')",
    );
    code = code.replace(
        /'route\('checkout\.cancel'\)'/g,
        "route('checkout.cancel')",
    );
    code = code.replace(
        /'\$user->payrexCustomerId\(\)'/g,
        '$user->payrexCustomerId()',
    );

    return `use LegionHQ\\LaravelPayrex\\Facades\\Payrex;\n\n$checkout = Payrex::checkoutSessions()->create(${code});\n\nreturn redirect()->away($checkout->url);`;
});
</script>

<template>
    <Head title="Create Checkout Session" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Create Checkout Session
                </h1>
                <p class="text-sm text-muted-foreground">
                    Redirect customers to a PayRex-hosted payment page.
                </p>
            </div>

            <div class="grid gap-6 xl:grid-cols-5">
                <form
                    class="min-w-0 divide-y rounded-xl border bg-card xl:col-span-3"
                    @submit.prevent="submit"
                >
                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Line Items</p>
                            <p class="text-sm text-muted-foreground">
                                Products to include in the checkout.
                            </p>
                        </div>
                        <div class="min-w-0 space-y-3">
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    v-for="product in products"
                                    :key="product.id"
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    class="font-medium"
                                    @click="addProduct(product)"
                                >
                                    {{ product.name }}
                                </Button>
                            </div>
                            <LineItemList
                                :line-items="form.line_items"
                                @remove="removeLineItem"
                            />
                            <InputError :message="form.errors.line_items" />
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Details</p>
                            <p class="text-sm text-muted-foreground">
                                Optional settings for the session.
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <Input
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    placeholder="Order description"
                                />
                            </div>
                            <div class="grid gap-3 lg:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="submit_type">Button Text</Label>
                                    <Select
                                        id="submit_type"
                                        v-model="form.submit_type"
                                    >
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="pay (default)"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="pay">
                                                pay
                                            </SelectItem>
                                            <SelectItem value="donate">
                                                donate
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="space-y-2">
                                    <Label for="user_id">Customer</Label>
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
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Payment Methods</p>
                            <p class="text-sm text-muted-foreground">
                                Methods available at checkout.
                            </p>
                        </div>
                        <div>
                            <ToggleGrid
                                :options="paymentMethodOptions"
                                :selected="form.payment_methods"
                                @toggle="togglePaymentMethod"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div />
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            Create Checkout Session
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

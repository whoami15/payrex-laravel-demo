<script setup>
import { useHttp } from '@inertiajs/vue3';
import { CreditCard } from 'lucide-vue-next';
import { computed, markRaw, shallowRef, watch } from 'vue';
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
import { formatPesos } from '@/lib/formatters';
import { paymentMethodOptions } from '@/lib/paymentMethods';

const props = defineProps({
    products: { type: Array, required: true },
    customers: { type: Array, required: true },
    publicKey: { type: String, required: true },
});

const emit = defineEmits(['payment-ready']);

const brandColor = shallowRef('#6A63EF');
const prefillBilling = shallowRef(false);
const showAllBillingFields = shallowRef(false);

const http = useHttp({
    amount: '',
    description: '',
    payment_methods: ['card', 'gcash'],
    user_id: '',
    capture_type: 'automatic',
    statement_descriptor: '',
});

const errorMessage = shallowRef('');

function toggleMethod(method) {
    http.payment_methods = http.payment_methods.includes(method)
        ? http.payment_methods.filter((m) => m !== method)
        : [...http.payment_methods, method];
}

function selectProduct(product) {
    http.amount = (product.price / 100).toFixed(2);
    http.description = product.name;
}

const selectedCustomer = computed(() => {
    if (!http.user_id) return null;
    return props.customers.find((c) => c.id === parseInt(http.user_id));
});

watch(selectedCustomer, (customer) => {
    if (!customer) prefillBilling.value = false;
});

function createPaymentIntent() {
    if (!http.amount || !http.description || http.payment_methods.length === 0)
        return;

    errorMessage.value = '';

    http.transform((data) => ({
        ...data,
        amount: parseFloat(data.amount),
        user_id: data.user_id || undefined,
    })).post('/elements/create-intent', {
        onSuccess: (data) => {
            const payrexInstance = window.Payrex(props.publicKey);
            const elementsInstance = payrexInstance.elements({
                clientSecret: data.client_secret,
                style: {
                    variables: {
                        primaryColor: brandColor.value,
                    },
                },
            });

            const paymentElementOptions = {
                layout: 'accordion',
            };

            if (prefillBilling.value && selectedCustomer.value) {
                const c = selectedCustomer.value;
                paymentElementOptions.defaultValues = {
                    billingDetails: {
                        name: c.name,
                        email: c.email,
                        phone: c.phone || undefined,
                        address: {
                            line1: c.address_line1 || undefined,
                            line2: c.address_line2 || undefined,
                            city: c.city || undefined,
                            state: c.state || undefined,
                            postalCode: c.postal_code || undefined,
                            country: c.country || undefined,
                        },
                    },
                };
            }

            if (showAllBillingFields.value) {
                paymentElementOptions.fields = {
                    billingDetails: {
                        email: 'always',
                        name: 'always',
                        phone: 'always',
                        address: {
                            line1: 'always',
                            line2: 'always',
                            city: 'always',
                            postalCode: 'always',
                            state: 'always',
                            country: 'always',
                        },
                    },
                };
            }

            const paymentElement = elementsInstance.create(
                'payment',
                paymentElementOptions,
            );

            emit('payment-ready', {
                payrexInstance: markRaw(payrexInstance),
                elementsInstance: markRaw(elementsInstance),
                paymentElement: markRaw(paymentElement),
                returnUrl: data.return_url,
                formattedAmount: formatPesos(http.amount),
                description: http.description,
                brandColor: brandColor.value,
            });
        },
        onError: (errors) => {
            errorMessage.value =
                errors.message || 'Failed to create payment intent.';
        },
    });
}

const canSubmit = computed(() => {
    return (
        !http.processing &&
        http.amount &&
        http.description &&
        http.payment_methods.length > 0
    );
});
</script>

<template>
    <div class="divide-y rounded-xl border bg-card">
        <div class="p-5">
            <p
                class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
            >
                Quick Fill
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                <Button
                    v-for="product in products"
                    :key="product.id"
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
        </div>

        <div class="space-y-3 p-5">
            <p
                class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
            >
                Payment
            </p>
            <div class="grid gap-3 lg:grid-cols-2">
                <div class="space-y-2">
                    <Label for="amount">Amount (₱)</Label>
                    <Input
                        id="amount"
                        v-model="http.amount"
                        type="number"
                        min="1"
                        step="0.01"
                        required
                        placeholder="1000.00"
                    />
                </div>
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Input
                        id="description"
                        v-model="http.description"
                        type="text"
                        required
                        placeholder="Order #1234"
                    />
                </div>
            </div>
            <div class="space-y-2">
                <Label>Payment Methods</Label>
                <ToggleGrid
                    :options="paymentMethodOptions"
                    :selected="http.payment_methods"
                    @toggle="toggleMethod"
                />
            </div>
            <div class="space-y-2">
                <Label for="user_id">Customer</Label>
                <Select id="user_id" v-model="http.user_id">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="No customer" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="c in customers"
                            :key="c.id"
                            :value="String(c.id)"
                        >
                            {{ c.name }} ({{ c.email }}){{
                                c.payrex_customer_id ? '' : ' — new'
                            }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-3 lg:grid-cols-2">
                <div class="space-y-2">
                    <Label for="capture_type">Capture Type</Label>
                    <Select id="capture_type" v-model="http.capture_type">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Automatic" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="automatic">Automatic</SelectItem>
                            <SelectItem value="manual"
                                >Manual (hold then capture)</SelectItem
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
                        v-model="http.statement_descriptor"
                        type="text"
                        maxlength="22"
                        placeholder="MYSTORE"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-3 p-5">
            <p
                class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
            >
                Customize
            </p>
            <div class="space-y-2">
                <Label for="brandColor">Brand Color</Label>
                <div class="flex items-center gap-2">
                    <input
                        id="brandColor"
                        v-model="brandColor"
                        type="color"
                        class="h-9 w-12 cursor-pointer rounded-lg border border-input p-1"
                    />
                    <Input
                        v-model="brandColor"
                        type="text"
                        class="font-mono text-xs"
                    />
                </div>
                <p class="text-sm text-muted-foreground">
                    Applied via <code>style.variables.primaryColor</code>.
                </p>
            </div>
            <div class="space-y-2">
                <label
                    class="flex items-center gap-2 text-sm"
                    :class="
                        selectedCustomer
                            ? 'cursor-pointer'
                            : 'cursor-not-allowed opacity-50'
                    "
                >
                    <input
                        type="checkbox"
                        v-model="prefillBilling"
                        :disabled="!selectedCustomer"
                        class="size-4 rounded accent-primary"
                    />
                    Pre-fill billing details from selected customer
                </label>
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        v-model="showAllBillingFields"
                        class="size-4 rounded accent-primary"
                    />
                    Show all billing fields
                </label>
            </div>
        </div>

        <div class="p-5">
            <div
                v-if="errorMessage"
                class="mb-3 rounded-lg bg-destructive/10 p-3 text-sm text-destructive"
            >
                {{ errorMessage }}
            </div>

            <Button
                class="w-full"
                :disabled="!canSubmit"
                @click="createPaymentIntent"
            >
                <Spinner v-if="http.processing" />
                Proceed to Payment
            </Button>
        </div>
    </div>
</template>

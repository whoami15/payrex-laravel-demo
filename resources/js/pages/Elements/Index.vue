<script setup>
import { Head } from '@inertiajs/vue3';
import { AlertCircle } from 'lucide-vue-next';
import { nextTick, shallowRef } from 'vue';
import OrderForm from './components/OrderForm.vue';
import PaymentForm from './components/PaymentForm.vue';
import ElementsGuide from './ElementsGuide.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    products: { type: Array, required: true },
    customers: { type: Array, required: true },
    publicKey: { type: String, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'PayRex Elements', href: '/elements' },
];

const step = shallowRef('form');
const paymentData = shallowRef(null);

async function onPaymentReady(data) {
    paymentData.value = data;
    step.value = 'payment';

    await nextTick();
    data.paymentElement.mount('#payment-element');
}

function onCancel() {
    step.value = 'form';
    paymentData.value = null;
}
</script>

<template>
    <Head title="PayRex Elements" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    PayRex Elements
                </h1>
                <p class="text-sm text-muted-foreground">
                    Build a custom payment form using PayRex's embedded UI
                    components. Your customer pays without leaving your site.
                </p>
            </div>

            <div
                v-if="!publicKey"
                class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/50"
            >
                <AlertCircle
                    class="size-5 shrink-0 text-amber-600 dark:text-amber-400"
                />
                <div class="text-sm">
                    <span
                        class="font-medium text-amber-800 dark:text-amber-200"
                    >
                        Public key not configured.
                    </span>
                    <span class="text-amber-700 dark:text-amber-300">
                        Add
                        <code class="bg-amber-100! dark:bg-amber-900!"
                            >PAYREX_PUBLIC_KEY</code
                        >
                        to
                        <code class="bg-amber-100! dark:bg-amber-900!"
                            >.env</code
                        >.
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <OrderForm
                        v-if="step === 'form'"
                        :products="products"
                        :customers="customers"
                        :public-key="publicKey"
                        @payment-ready="onPaymentReady"
                    />

                    <PaymentForm
                        v-if="step === 'payment' && paymentData"
                        :payrex-instance="paymentData.payrexInstance"
                        :elements-instance="paymentData.elementsInstance"
                        :return-url="paymentData.returnUrl"
                        :formatted-amount="paymentData.formattedAmount"
                        :description="paymentData.description"
                        :brand-color="paymentData.brandColor"
                        @cancel="onCancel"
                    />
                </div>

                <ElementsGuide />
            </div>
        </div>
    </AppLayout>
</template>

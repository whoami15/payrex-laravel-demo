<script setup>
import { Form, Head } from '@inertiajs/vue3';
import { RotateCcw } from 'lucide-vue-next';
import { computed, shallowRef } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
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
import { formatAmount, toPhpArray } from '@/lib/formatters';
import { paymentStatusColors } from '@/lib/statusColors';

const props = defineProps({
    payment: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payment', href: `/payments/${props.payment.id}` },
    { title: 'Refund' },
];

const refundReasons = [
    { value: 'requested_by_customer', label: 'Requested by customer' },
    { value: 'fraudulent', label: 'Fraudulent' },
    { value: 'product_out_of_stock', label: 'Product out of stock' },
    { value: 'service_not_provided', label: 'Service not provided' },
    { value: 'product_was_damaged', label: 'Product was damaged' },
    { value: 'service_misaligned', label: 'Service misaligned' },
    { value: 'wrong_product_received', label: 'Wrong product received' },
    { value: 'others', label: 'Others' },
];

const maxRefundableCentavos =
    props.payment.amount - (props.payment.amount_refunded || 0);
const maxRefundable = (maxRefundableCentavos / 100).toFixed(2);

const reason = shallowRef('requested_by_customer');
const amount = shallowRef(maxRefundable);
const remarks = shallowRef('');

const liveCode = computed(() => {
    const params = {
        payment_id: props.payment.id,
        amount: Math.round(parseFloat(amount.value || 0) * 100),
        reason: reason.value,
    };

    if (remarks.value) params.remarks = remarks.value;

    return `use LegionHQ\\LaravelPayrex\\Facades\\Payrex;\n\n$refund = Payrex::refunds()->create(${toPhpArray(params)});`;
});
</script>

<template>
    <Head title="Issue Refund" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Issue Refund
                </h1>
                <p class="text-sm text-muted-foreground">
                    Refund a payment back to the customer.
                </p>
            </div>

            <div class="grid gap-6 xl:grid-cols-5">
                <div
                    class="min-w-0 divide-y rounded-xl border bg-card xl:col-span-3"
                >
                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Payment</p>
                            <p class="text-sm text-muted-foreground">
                                The payment to refund.
                            </p>
                        </div>
                        <div class="min-w-0 rounded-xl border bg-card">
                            <div class="flex items-start gap-4 p-4">
                                <div
                                    class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-muted"
                                >
                                    <RotateCcw
                                        class="size-5 text-muted-foreground"
                                    />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div
                                        class="flex items-baseline justify-between gap-2"
                                    >
                                        <p
                                            class="text-lg font-bold tracking-tight"
                                        >
                                            {{ formatAmount(payment.amount) }}
                                        </p>
                                        <Badge
                                            variant="secondary"
                                            :class="
                                                paymentStatusColors[
                                                    payment.status
                                                ]
                                            "
                                        >
                                            {{ payment.status }}
                                        </Badge>
                                    </div>
                                    <code
                                        class="text-xs break-all text-muted-foreground"
                                        >{{ payment.id }}</code
                                    >
                                    <p
                                        v-if="payment.amount_refunded"
                                        class="mt-1 text-sm text-muted-foreground"
                                    >
                                        Already refunded:
                                        {{
                                            formatAmount(
                                                payment.amount_refunded,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Form
                        method="post"
                        action="/refunds"
                        v-slot="{ errors, processing }"
                    >
                        <input
                            type="hidden"
                            name="payment_id"
                            :value="payment.id"
                        />
                        <input type="hidden" name="reason" :value="reason" />

                        <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                            <div>
                                <p class="text-sm font-medium">
                                    Refund Details
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Max
                                    {{ formatAmount(maxRefundableCentavos) }}.
                                </p>
                            </div>
                            <div class="min-w-0 space-y-3">
                                <div class="space-y-2">
                                    <Label for="amount">Amount (₱)</Label>
                                    <Input
                                        id="amount"
                                        name="amount"
                                        v-model="amount"
                                        type="number"
                                        min="1"
                                        step="0.01"
                                        :max="maxRefundable"
                                        required
                                    />
                                    <InputError :message="errors.amount" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="reason">Reason</Label>
                                    <Select id="reason" v-model="reason">
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="Select a reason"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="r in refundReasons"
                                                :key="r.value"
                                                :value="r.value"
                                            >
                                                {{ r.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="errors.reason" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="remarks"
                                        >Remarks (optional)</Label
                                    >
                                    <Input
                                        id="remarks"
                                        name="remarks"
                                        v-model="remarks"
                                        type="text"
                                        placeholder="Additional details"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                            <div />
                            <Button type="submit" :disabled="processing">
                                <Spinner v-if="processing" />
                                Issue Refund
                            </Button>
                        </div>
                    </Form>
                </div>

                <div class="xl:col-span-2">
                    <div class="xl:sticky xl:top-4">
                        <CodeBlock title="API Call Preview" :code="liveCode" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

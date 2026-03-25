<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Minus, Plus, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import InputError from '@/components/InputError.vue';
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
import { formatPesos, toPhpArray } from '@/lib/formatters';

const props = defineProps({
    customers: { type: Array, required: true },
    products: { type: Array, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Billing Statements', href: '/billing-statements' },
    { title: 'Create' },
];

const form = useForm({
    user_id: '',
    description: '',
    line_items: [],
});

function addProduct(product) {
    const existing = form.line_items.find(
        (item) => item.description === product.name,
    );
    if (existing) {
        existing.quantity++;
        return;
    }

    form.line_items.push({
        description: product.name,
        unit_price: (product.price / 100).toFixed(2),
        quantity: 1,
    });
}

function removeLineItem(index) {
    form.line_items.splice(index, 1);
}

const reversedItems = computed(() =>
    form.line_items.map((item, index) => ({ item, index })).reverse(),
);

function submit() {
    form.post('/billing-statements');
}

const selectedCustomer = computed(() => {
    if (!form.user_id) return null;
    return props.customers.find((c) => c.id === parseInt(form.user_id));
});

const liveCode = computed(() => {
    const params = {};

    if (selectedCustomer.value) {
        params.customer_id =
            selectedCustomer.value.payrex_customer_id ||
            '$user->payrexCustomerId()';
    }

    if (form.description) params.description = form.description;

    let code = `use LegionHQ\\LaravelPayrex\\Facades\\Payrex;\n\n`;
    code += `$billingStatement = Payrex::billingStatements()->create(${toPhpArray(params)});\n`;

    if (form.line_items.length) {
        code += '\n// Add line items\n';
        for (const item of form.line_items) {
            const liParams = {
                billing_statement_id: '$billingStatement->id',
                description: item.description,
                unit_price: Math.round(parseFloat(item.unit_price) * 100),
                quantity: item.quantity,
            };
            let li = toPhpArray(liParams);
            li = li.replace("'$billingStatement->id'", '$billingStatement->id');
            code += `Payrex::billingStatementLineItems()->create(${li});\n`;
        }
    }

    return code;
});
</script>

<template>
    <Head title="Create Billing Statement" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Create Billing Statement
                </h1>
                <p class="text-sm text-muted-foreground">
                    Create a draft billing statement with line items.
                </p>
            </div>

            <div class="grid gap-6 xl:grid-cols-5">
                <form
                    class="min-w-0 divide-y rounded-xl border bg-card xl:col-span-3"
                    @submit.prevent="submit"
                >
                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Customer</p>
                            <p class="text-sm text-muted-foreground">
                                Who to bill.
                            </p>
                        </div>
                        <div class="min-w-0 space-y-3">
                            <div class="space-y-2">
                                <Label for="user_id">Customer</Label>
                                <Select id="user_id" v-model="form.user_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select a customer..."
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="customer in customers"
                                            :key="customer.id"
                                            :value="String(customer.id)"
                                        >
                                            {{ customer.name }} ({{
                                                customer.email
                                            }}){{
                                                customer.payrex_customer_id
                                                    ? ''
                                                    : ' — new'
                                            }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.user_id" />
                            </div>
                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <Input
                                    id="description"
                                    v-model="form.description"
                                    type="text"
                                    placeholder="Monthly subscription"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div>
                            <p class="text-sm font-medium">Line Items</p>
                            <p class="text-sm text-muted-foreground">
                                Services to include on the statement.
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

                            <p
                                v-if="form.line_items.length === 0"
                                class="rounded-lg border border-dashed p-5 text-center text-sm text-muted-foreground"
                            >
                                Select services above to add line items.
                            </p>

                            <div
                                v-if="form.line_items.length > 0"
                                class="relative overflow-hidden rounded-lg border"
                            >
                                <div
                                    class="max-h-52 divide-y overflow-y-auto"
                                    :class="{
                                        'pb-8': form.line_items.length > 2,
                                    }"
                                >
                                    <TransitionGroup name="line-item">
                                        <div
                                            v-for="{
                                                item,
                                                index,
                                            } in reversedItems"
                                            :key="
                                                item.description + '-' + index
                                            "
                                            class="flex items-center gap-3 p-3"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <p
                                                    class="truncate text-sm font-medium"
                                                >
                                                    {{ item.description }}
                                                </p>
                                                <p
                                                    class="text-xs text-muted-foreground"
                                                >
                                                    {{
                                                        formatPesos(
                                                            item.unit_price,
                                                        )
                                                    }}
                                                    each
                                                </p>
                                            </div>

                                            <div
                                                class="flex shrink-0 items-center gap-1"
                                            >
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="icon"
                                                    class="size-7"
                                                    :disabled="
                                                        item.quantity <= 1
                                                    "
                                                    @click="item.quantity--"
                                                >
                                                    <Minus class="size-3" />
                                                </Button>
                                                <span
                                                    class="w-8 text-center text-sm font-medium"
                                                >
                                                    {{ item.quantity }}
                                                </span>
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="icon"
                                                    class="size-7"
                                                    @click="item.quantity++"
                                                >
                                                    <Plus class="size-3" />
                                                </Button>
                                            </div>

                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                class="size-7 shrink-0"
                                                @click="removeLineItem(index)"
                                            >
                                                <Trash2
                                                    class="size-3 text-destructive"
                                                />
                                            </Button>
                                        </div>
                                    </TransitionGroup>
                                </div>
                                <div
                                    v-if="form.line_items.length > 2"
                                    class="pointer-events-none absolute inset-x-0 bottom-0 flex h-8 items-center justify-center border-t bg-background/80 text-xs text-muted-foreground backdrop-blur-sm"
                                >
                                    Scroll for more items
                                </div>
                            </div>
                            <InputError :message="form.errors.line_items" />
                        </div>
                    </div>

                    <div class="grid gap-4 p-5 sm:grid-cols-[200px_1fr]">
                        <div />
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" />
                            Create Billing Statement
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

<style scoped>
.line-item-move,
.line-item-enter-active {
    transition: all 0.25s cubic-bezier(0.23, 1, 0.32, 1);
}

.line-item-leave-active {
    transition: all 0.2s ease-in;
    position: absolute;
    width: 100%;
}

.line-item-enter-from {
    opacity: 0;
    transform: translateY(-12px);
}

.line-item-leave-to {
    opacity: 0;
    transform: translateX(12px);
}
</style>

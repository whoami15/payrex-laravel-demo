<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, shallowRef } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { formatAmount } from '@/lib/formatters';

const props = defineProps({
    billingStatementId: { type: String, required: true },
    lineItems: { type: Array, default: () => [] },
    isDraft: { type: Boolean, required: true },
    products: { type: Array, default: () => [] },
});

const total = computed(() =>
    props.lineItems.reduce(
        (sum, item) => sum + item.unit_price * item.quantity,
        0,
    ),
);

const addItemForm = useForm({
    description: '',
    unit_price: '',
    quantity: 1,
});

function quickAdd(product) {
    if (addItemForm.description === product.name) {
        addItemForm.quantity++;
        return;
    }

    addItemForm.description = product.name;
    addItemForm.unit_price = (product.price / 100).toFixed(2);
    addItemForm.quantity = 1;
}

function addLineItem() {
    addItemForm.post(
        `/billing-statements/${props.billingStatementId}/line-items`,
        {
            preserveScroll: true,
            onSuccess: () => addItemForm.reset(),
        },
    );
}

const editingItem = shallowRef(null);
const editItemForm = useForm({
    description: '',
    unit_price: '',
    quantity: 1,
});

function startEdit(item) {
    editingItem.value = item.id;
    editItemForm.description = item.description;
    editItemForm.unit_price = (item.unit_price / 100).toFixed(2);
    editItemForm.quantity = item.quantity;
}

function cancelEdit() {
    editingItem.value = null;
    editItemForm.reset();
}

function saveEdit(itemId) {
    editItemForm.put(
        `/billing-statements/${props.billingStatementId}/line-items/${itemId}`,
        {
            preserveScroll: true,
            onSuccess: () => {
                editingItem.value = null;
                editItemForm.reset();
            },
        },
    );
}

function deleteLineItem(itemId) {
    router.delete(
        `/billing-statements/${props.billingStatementId}/line-items/${itemId}`,
        {
            preserveScroll: true,
        },
    );
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-sm font-medium">Line Items</CardTitle>
        </CardHeader>
        <CardContent>
            <div v-if="lineItems.length">
                <div
                    class="grid grid-cols-[1fr_auto_auto_auto] items-center gap-4 border-b pb-2 text-xs text-muted-foreground sm:grid-cols-[1fr_80px_60px_100px]"
                >
                    <span>Description</span>
                    <span class="text-right">Unit price</span>
                    <span class="text-right">Qty</span>
                    <span class="text-right">Amount</span>
                </div>

                <div v-for="item in lineItems" :key="item.id">
                    <div
                        v-if="isDraft && editingItem === item.id"
                        class="space-y-3 border-b py-4"
                    >
                        <div class="grid gap-3 lg:grid-cols-3">
                            <div class="space-y-2">
                                <Label>Description</Label>
                                <Input
                                    v-model="editItemForm.description"
                                    required
                                />
                                <InputError
                                    :message="editItemForm.errors.description"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Unit Price (₱)</Label>
                                <Input
                                    v-model="editItemForm.unit_price"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    required
                                />
                                <InputError
                                    :message="editItemForm.errors.unit_price"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Quantity</Label>
                                <Input
                                    v-model="editItemForm.quantity"
                                    type="number"
                                    min="1"
                                    required
                                />
                                <InputError
                                    :message="editItemForm.errors.quantity"
                                />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Button
                                size="sm"
                                :disabled="editItemForm.processing"
                                @click="saveEdit(item.id)"
                            >
                                <Spinner v-if="editItemForm.processing" />
                                Save
                            </Button>
                            <Button
                                size="sm"
                                variant="ghost"
                                @click="cancelEdit"
                            >
                                Cancel
                            </Button>
                        </div>
                    </div>

                    <div
                        v-else
                        class="grid grid-cols-[1fr_auto_auto_auto] items-center gap-4 border-b py-3 sm:grid-cols-[1fr_80px_60px_100px]"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-sm">{{ item.description }}</span>
                            <div
                                v-if="isDraft"
                                class="flex shrink-0 items-center gap-0.5"
                            >
                                <button
                                    class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
                                    @click="startEdit(item)"
                                >
                                    <Pencil class="size-3" />
                                </button>
                                <ConfirmDialog
                                    title="Delete line item?"
                                    description="This will remove the line item from the billing statement."
                                    confirm-label="Delete"
                                    variant="destructive"
                                    @confirm="deleteLineItem(item.id)"
                                >
                                    <button
                                        class="rounded p-1 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    >
                                        <Trash2 class="size-3" />
                                    </button>
                                </ConfirmDialog>
                            </div>
                        </div>
                        <span class="text-right text-sm tabular-nums">
                            {{ formatAmount(item.unit_price) }}
                        </span>
                        <span class="text-right text-sm tabular-nums">
                            {{ item.quantity }}
                        </span>
                        <span
                            class="text-right text-sm font-medium tabular-nums"
                        >
                            {{ formatAmount(item.unit_price * item.quantity) }}
                        </span>
                    </div>
                </div>

                <div
                    class="grid grid-cols-[1fr_auto_auto_auto] items-center gap-4 pt-3 sm:grid-cols-[1fr_80px_60px_100px]"
                >
                    <span class="text-sm font-semibold">Total</span>
                    <span />
                    <span />
                    <span class="text-right text-sm font-semibold tabular-nums">
                        {{ formatAmount(total) }}
                    </span>
                </div>
            </div>

            <p
                v-else
                class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground"
            >
                No line items yet. Add one below.
            </p>

            <div v-if="isDraft" class="mt-4 space-y-3 border-t pt-4">
                <div v-if="products.length" class="space-y-2">
                    <p class="text-sm text-muted-foreground">Quick add</p>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="product in products"
                            :key="product.id"
                            variant="outline"
                            size="sm"
                            class="font-medium"
                            @click="quickAdd(product)"
                        >
                            {{ product.name }}
                        </Button>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 lg:grid-cols-3">
                    <div class="space-y-2">
                        <Label>Description</Label>
                        <Input
                            v-model="addItemForm.description"
                            required
                            placeholder="Service or product"
                        />
                        <InputError :message="addItemForm.errors.description" />
                    </div>
                    <div class="space-y-2">
                        <Label>Unit Price (₱)</Label>
                        <Input
                            v-model="addItemForm.unit_price"
                            type="number"
                            min="0.01"
                            step="0.01"
                            required
                            placeholder="1000.00"
                        />
                        <InputError :message="addItemForm.errors.unit_price" />
                    </div>
                    <div class="space-y-2">
                        <Label>Quantity</Label>
                        <Input
                            v-model="addItemForm.quantity"
                            type="number"
                            min="1"
                            required
                            placeholder="1"
                        />
                        <InputError :message="addItemForm.errors.quantity" />
                    </div>
                </div>
                <Button
                    size="sm"
                    :disabled="addItemForm.processing"
                    @click="addLineItem"
                >
                    <Spinner v-if="addItemForm.processing" />
                    <Plus v-else class="size-3.5" />
                    Add Item
                </Button>
            </div>
        </CardContent>
    </Card>
</template>

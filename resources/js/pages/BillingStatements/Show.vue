<script setup>
import { Head, router } from '@inertiajs/vue3';
import {
    AlertTriangle,
    CheckCircle,
    ExternalLink,
    FilePenLine,
    FileText,
    Pencil,
    Send,
    XCircle,
} from 'lucide-vue-next';
import { computed, shallowRef } from 'vue';
import LineItemManager from './components/LineItemManager.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DatePicker from '@/components/DatePicker.vue';
import RawResponse from '@/components/RawResponse.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatAmount, formatTimestamp } from '@/lib/formatters';
import { billingStatementStatusColors } from '@/lib/statusColors';

const props = defineProps({
    billingStatement: { type: Object, required: true },
    products: { type: Array, default: () => [] },
});

const bs = computed(() => props.billingStatement);
const isDraft = computed(() => bs.value.status === 'draft');

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Billing Statements', href: '/billing-statements' },
    {
        title:
            props.billingStatement.billing_statement_number ||
            props.billingStatement.id,
    },
];

const processingAction = shallowRef(null);

const showDueDateDialog = shallowRef(false);
const dueDate = shallowRef(
    props.billingStatement.due_at
        ? new Date(props.billingStatement.due_at * 1000)
              .toISOString()
              .split('T')[0]
        : '',
);
const savingDueDate = shallowRef(false);

function saveDueDate() {
    savingDueDate.value = true;
    router.put(
        `/billing-statements/${props.billingStatement.id}`,
        { due_at: dueDate.value },
        {
            preserveScroll: true,
            onFinish: () => {
                savingDueDate.value = false;
                showDueDateDialog.value = false;
            },
        },
    );
}

function action(path) {
    processingAction.value = path;
    router.post(
        `/billing-statements/${bs.value.id}/${path}`,
        {},
        {
            preserveScroll: true,
            onFinish: () => (processingAction.value = null),
        },
    );
}
</script>

<template>
    <Head
        :title="`Billing Statement ${bs.billing_statement_number || bs.id}`"
    />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <div class="rounded-xl border bg-card">
                <div
                    class="flex flex-col gap-6 p-6 xl:flex-row xl:items-start xl:justify-between"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                        >
                            <FileText class="size-6 text-muted-foreground" />
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-xl font-semibold tracking-tight">
                                {{
                                    bs.billing_statement_number ||
                                    'Billing Statement'
                                }}
                            </h1>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                <code class="break-all">{{ bs.id }}</code>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-if="isDraft"
                            :disabled="!!processingAction"
                            @click="action('finalize')"
                        >
                            <Spinner v-if="processingAction === 'finalize'" />
                            <CheckCircle v-else class="size-4" />
                            Finalize
                        </Button>
                        <Button
                            v-if="bs.status === 'open'"
                            :disabled="!!processingAction"
                            @click="action('send')"
                        >
                            <Spinner v-if="processingAction === 'send'" />
                            <Send v-else class="size-4" />
                            Send
                        </Button>
                        <Button
                            v-if="bs.billing_statement_url && !isDraft"
                            variant="outline"
                            as-child
                        >
                            <a :href="bs.billing_statement_url" target="_blank">
                                <ExternalLink class="size-4" />
                                Open Statement
                            </a>
                        </Button>
                        <ConfirmDialog
                            v-if="['open', 'overdue'].includes(bs.status)"
                            title="Void billing statement?"
                            description="This will void the billing statement. The customer will no longer be able to pay."
                            confirm-label="Void"
                            variant="destructive"
                            @confirm="action('void')"
                        >
                            <Button
                                variant="outline"
                                :disabled="!!processingAction"
                            >
                                <Spinner v-if="processingAction === 'void'" />
                                <XCircle v-else class="size-4" />
                                Void
                            </Button>
                        </ConfirmDialog>
                        <ConfirmDialog
                            v-if="['open', 'overdue'].includes(bs.status)"
                            title="Mark as uncollectible?"
                            description="This will mark the billing statement as uncollectible."
                            confirm-label="Mark Uncollectible"
                            variant="destructive"
                            @confirm="action('mark-uncollectible')"
                        >
                            <Button
                                variant="outline"
                                :disabled="!!processingAction"
                            >
                                <Spinner
                                    v-if="
                                        processingAction ===
                                        'mark-uncollectible'
                                    "
                                />
                                <AlertTriangle v-else class="size-4" />
                                Uncollectible
                            </Button>
                        </ConfirmDialog>
                    </div>
                </div>

                <div class="grid grid-cols-2 divide-x border-t xl:grid-cols-4">
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Amount
                        </p>
                        <p class="mt-1 text-xl font-bold tracking-tight">
                            {{ formatAmount(bs.amount || 0) }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Status
                        </p>
                        <Badge
                            class="mt-1.5"
                            variant="secondary"
                            :class="
                                billingStatementStatusColors[bs.status] || ''
                            "
                        >
                            {{ bs.status }}
                        </Badge>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Due Date
                        </p>
                        <button
                            v-if="isDraft"
                            class="mt-1 flex items-center gap-1 text-sm font-medium text-primary underline-offset-4 hover:underline"
                            @click="showDueDateDialog = true"
                        >
                            {{
                                bs.due_at
                                    ? formatTimestamp(bs.due_at)
                                    : 'Set due date'
                            }}
                            <FilePenLine class="size-4 shrink-0" />
                        </button>
                        <p v-else class="mt-1 text-sm font-medium">
                            {{
                                bs.due_at
                                    ? formatTimestamp(bs.due_at)
                                    : 'Not set'
                            }}
                        </p>
                    </div>
                    <div class="px-6 py-4">
                        <p
                            class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                        >
                            Customer
                        </p>
                        <template v-if="bs.customer">
                            <p class="mt-1 text-sm font-medium">
                                {{
                                    typeof bs.customer === 'string'
                                        ? bs.customer
                                        : bs.customer.name
                                }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ bs.customer.email }}
                            </p>
                        </template>
                        <p v-else class="mt-1 text-sm text-muted-foreground">
                            —
                        </p>
                    </div>
                </div>
            </div>

            <div
                v-if="isDraft"
                class="flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/50 dark:bg-blue-950/50"
            >
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    This statement is a <strong>draft</strong>. Set the due date
                    and manage line items below, then click
                    <strong>Finalize</strong> to open it for payment. The
                    minimum amount to finalize is <strong>₱20.00</strong>.
                </div>
            </div>

            <LineItemManager
                :billing-statement-id="bs.id"
                :line-items="bs.line_items || []"
                :is-draft="isDraft"
                :products="products"
            />

            <RawResponse :data="bs" />

            <Dialog v-model:open="showDueDateDialog">
                <DialogContent class="sm:max-w-sm">
                    <DialogHeader>
                        <DialogTitle>Set Due Date</DialogTitle>
                        <DialogDescription>
                            Choose when this billing statement is due.
                        </DialogDescription>
                    </DialogHeader>
                    <DatePicker
                        v-model="dueDate"
                        placeholder="Select due date"
                    />
                    <DialogFooter>
                        <Button
                            variant="outline"
                            @click="showDueDateDialog = false"
                        >
                            Cancel
                        </Button>
                        <Button :disabled="savingDueDate" @click="saveDueDate">
                            <Spinner v-if="savingDueDate" />
                            Save
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>

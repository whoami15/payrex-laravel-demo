<script setup>
import { Head, useHttp } from '@inertiajs/vue3';
import { Link2, Search, Trash2, Users } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import CodeBlock from '@/components/CodeBlock.vue';
import FeatureHeader from '@/components/FeatureHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feature Showcase' },
    { title: 'Billable Customer' },
];

const http = useHttp({});
const result = shallowRef(null);
const currentCustomerId = shallowRef(props.user.payrex_customer_id);

function callAction(action) {
    result.value = null;

    http.post(`/features/billable-customer/${action}`, {
        onSuccess: (data) => {
            result.value = data;
            if (data.payrex_customer_id !== undefined) {
                currentCustomerId.value = data.payrex_customer_id;
            }
        },
        onError: () => {
            result.value = { error: 'Request failed.' };
        },
    });
}
</script>

<template>
    <Head title="Billable Customer" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="page-enter flex flex-1 flex-col gap-6 p-4">
            <FeatureHeader
                title="Billable Customer"
                docs="guide/billable-customer"
                controller="app/Http/Controllers/Feature/BillableCustomerController.php"
            >
                Add PayRex customer management directly to your Eloquent models
                with the <code>HasPayrexCustomer</code> trait.
            </FeatureHeader>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div class="rounded-xl border bg-card">
                        <div class="flex items-start gap-4 p-6">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted"
                            >
                                <Users class="size-6 text-muted-foreground" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold">
                                    {{ user.name }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ user.email }}
                                </p>
                                <div class="mt-2">
                                    <Badge
                                        v-if="currentCustomerId"
                                        variant="outline"
                                    >
                                        <code class="text-xs">{{
                                            currentCustomerId
                                        }}</code>
                                    </Badge>
                                    <Badge v-else variant="secondary"
                                        >Not linked</Badge
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="border-t px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <Button
                                        size="sm"
                                        :disabled="
                                            http.processing ||
                                            !!currentCustomerId
                                        "
                                        @click="callAction('create')"
                                    >
                                        <Spinner v-if="http.processing" />
                                        <Link2 v-else class="size-4" />
                                        Link to PayRex
                                    </Button>
                                    <span class="text-sm text-muted-foreground">
                                        Create a PayRex customer from this user
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        :disabled="
                                            http.processing ||
                                            !currentCustomerId
                                        "
                                        @click="callAction('retrieve')"
                                    >
                                        <Search class="size-4" />
                                        Fetch Customer
                                    </Button>
                                    <span class="text-sm text-muted-foreground">
                                        Fetch the full record from PayRex
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <Button
                                        size="sm"
                                        variant="destructive"
                                        :disabled="
                                            http.processing ||
                                            !currentCustomerId
                                        "
                                        @click="callAction('delete')"
                                    >
                                        <Trash2 class="size-4" />
                                        Unlink
                                    </Button>
                                    <span class="text-sm text-muted-foreground">
                                        Delete from PayRex and clear the stored
                                        ID
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <CodeBlock
                        v-if="result"
                        lang="json"
                        title="Result"
                        :code="JSON.stringify(result, null, 2)"
                    />
                </div>

                <div class="space-y-4">
                    <div class="rounded-xl border bg-card p-6">
                        <h2 class="text-sm font-semibold">How It Works</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Link your Eloquent model to a PayRex customer in
                            three steps.
                        </p>
                        <div class="mt-5 space-y-4">
                            <div
                                class="flex gap-4 [&:not(:last-child)>div:last-child]:pb-4"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted"
                                    >
                                        <span class="text-xs font-bold">1</span>
                                    </div>
                                    <div class="mt-1 h-full w-px bg-border" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">
                                        Add the trait
                                    </p>
                                    <p
                                        class="mt-0.5 text-sm text-muted-foreground"
                                    >
                                        Add <code>HasPayrexCustomer</code> to
                                        your User model.
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex gap-4 [&:not(:last-child)>div:last-child]:pb-4"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted"
                                    >
                                        <span class="text-xs font-bold">2</span>
                                    </div>
                                    <div class="mt-1 h-full w-px bg-border" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">
                                        Run the migration
                                    </p>
                                    <p
                                        class="mt-0.5 text-sm text-muted-foreground"
                                    >
                                        Publish and run the migration to add
                                        <code>payrex_customer_id</code> to your
                                        users table.
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex gap-4 [&:not(:last-child)>div:last-child]:pb-4"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted"
                                    >
                                        <span class="text-xs font-bold">3</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">
                                        Use the methods
                                    </p>
                                    <p
                                        class="mt-0.5 text-sm text-muted-foreground"
                                    >
                                        Call
                                        <code>createAsPayrexCustomer()</code> to
                                        create,
                                        <code>asPayrexCustomer()</code> to
                                        retrieve, and more.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <CodeBlock
                        title="Setup"
                        lang="bash"
                        code="
# 1. Publish and run the migration
php artisan vendor:publish --tag='payrex-migrations'
php artisan migrate

# 2. Add the trait to your User model
# use LegionHQ\LaravelPayrex\Concerns\HasPayrexCustomer;
# class User extends Authenticatable { use HasPayrexCustomer; }
                    "
                    />

                    <CodeBlock
                        title="Usage"
                        code="
// Create a customer in PayRex and store the ID
$customer = $user->createAsPayrexCustomer();
$user->payrexCustomerId(); // 'cus_xxxxx'

// Retrieve the full customer from PayRex
$customer = $user->asPayrexCustomer();

// Update the customer in PayRex
$customer = $user->updatePayrexCustomer([
    'name' => 'Juan Dela Cruz Jr.',
]);

// Delete from PayRex and clear the stored ID
$user->deleteAsPayrexCustomer();

// Check before creating (prevents LogicException)
if (! $user->hasPayrexCustomerId()) {
    $user->createAsPayrexCustomer();
}
                    "
                    />

                    <CodeBlock
                        title="Customization"
                        code="
// Override the name/email sent to PayRex
public function payrexCustomerName(): ?string
{
    return $this->full_name;
}

public function payrexCustomerEmail(): ?string
{
    return $this->billing_email ?? $this->email;
}

// Use a different column name
public function payrexCustomerIdColumn(): string
{
    return 'prx_customer_id';
}

// Multi-tenant: override the client per model
protected function payrexClient(): PayrexClient
{
    return new PayrexClient(
        secretKey: $this->team->payrex_secret_key,
        currency: $this->team->currency ?? 'PHP',
    );
}
                    "
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

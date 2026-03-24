<script setup>
import { Link, router } from '@inertiajs/vue3';
import { Eye, MoreHorizontal, Pencil, Trash2, Users } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import EmptyState from '@/components/EmptyState.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Spinner } from '@/components/ui/spinner';

defineProps({
    customers: { type: Object, required: true },
});

const deletingId = shallowRef(null);

function deleteCustomer(id) {
    deletingId.value = id;
    router.delete(`/customers/${id}`, {
        preserveScroll: true,
        onFinish: () => {
            deletingId.value = null;
        },
    });
}
</script>

<template>
    <EmptyState
        v-if="customers.data.length === 0"
        :icon="Users"
        title="No customers found"
        description="Create your first customer to get started."
        action-label="Create Customer"
        action-href="/customers/create"
    />

    <div v-else class="overflow-x-auto">
        <table class="w-full whitespace-nowrap">
            <thead>
                <tr class="border-b text-left text-sm text-muted-foreground">
                    <th class="px-4 py-3 font-medium">ID</th>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Currency</th>
                    <th class="w-0 px-4 py-3 font-medium" />
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="customer in customers.data"
                    :key="customer.id"
                    class="border-b last:border-0"
                >
                    <td class="px-4 py-3">
                        <code class="text-xs">{{ customer.id }}</code>
                    </td>
                    <td class="px-4 py-3 text-sm font-medium">
                        {{ customer.name }}
                    </td>
                    <td class="px-4 py-3 text-sm">{{ customer.email }}</td>
                    <td class="px-4 py-3 text-sm">{{ customer.currency }}</td>
                    <td class="px-4 py-3 text-right">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="sm">
                                    <Spinner
                                        v-if="deletingId === customer.id"
                                    />
                                    <MoreHorizontal v-else class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="`/customers/${customer.id}`"
                                        prefetch="hover"
                                    >
                                        <Eye class="mr-2 size-4" />
                                        View
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="`/customers/${customer.id}/edit`"
                                        prefetch="hover"
                                    >
                                        <Pencil class="mr-2 size-4" />
                                        Edit
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <ConfirmDialog
                                    title="Delete customer?"
                                    description="This will permanently delete this customer from PayRex. This action cannot be undone."
                                    confirm-label="Delete"
                                    variant="destructive"
                                    @confirm="deleteCustomer(customer.id)"
                                >
                                    <DropdownMenuItem
                                        class="text-destructive focus:text-destructive"
                                        @select.prevent
                                    >
                                        <Trash2 class="mr-2 size-4" />
                                        Delete
                                    </DropdownMenuItem>
                                </ConfirmDialog>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

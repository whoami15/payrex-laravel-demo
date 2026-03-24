<script setup>
import { router } from '@inertiajs/vue3';
import { ExternalLink, XCircle } from 'lucide-vue-next';
import { shallowRef } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

const props = defineProps({
    checkoutSession: { type: Object, required: true },
});

const processing = shallowRef(false);

function expire() {
    processing.value = true;
    router.post(
        `/checkout-sessions/${props.checkoutSession.id}/expire`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                processing.value = false;
            },
        },
    );
}
</script>

<template>
    <div
        class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center"
    >
        <Button
            v-if="checkoutSession.url && checkoutSession.status === 'active'"
            as-child
        >
            <a :href="checkoutSession.url" target="_blank">
                <ExternalLink class="size-4" />
                Open Checkout
            </a>
        </Button>
        <ConfirmDialog
            v-if="checkoutSession.status === 'active'"
            title="Expire checkout session?"
            description="This will expire the checkout session. The customer will no longer be able to complete payment."
            confirm-label="Expire"
            variant="destructive"
            @confirm="expire"
        >
            <Button variant="destructive" :disabled="processing">
                <Spinner v-if="processing" />
                <XCircle v-else class="size-4" />
                Expire
            </Button>
        </ConfirmDialog>
    </div>
</template>

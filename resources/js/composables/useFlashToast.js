import { router } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { toast } from 'vue-sonner';

export function useFlashToast() {
    let removeListener = null;

    onMounted(() => {
        removeListener = router.on('flash', (event) => {
            const flash = event.detail.flash;
            const message = flash.message;

            if (!message) {
                return;
            }

            const type = flash.type || 'success';

            if (type === 'error') {
                toast.error(message);
            } else if (type === 'warning') {
                toast.warning(message);
            } else {
                toast.success(message);
            }
        });
    });

    onUnmounted(() => {
        removeListener?.();
    });
}

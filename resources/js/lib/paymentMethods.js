import { CreditCard, QrCode, Smartphone, Wallet } from 'lucide-vue-next';
import { markRaw } from 'vue';

/**
 * Payment method options for form selectors.
 */
export const paymentMethodOptions = [
    { value: 'card', label: 'Card', icon: markRaw(CreditCard) },
    { value: 'gcash', label: 'GCash', icon: markRaw(Smartphone) },
    { value: 'maya', label: 'Maya', icon: markRaw(Wallet) },
    { value: 'qrph', label: 'QR PH', icon: markRaw(QrCode) },
];

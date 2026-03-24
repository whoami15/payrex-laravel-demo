/**
 * Badge color class mappings for PayRex resource statuses.
 * Used with the Badge component's :class binding.
 */

const colors = {
    gray: 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800/50 dark:text-neutral-300',
    blue: 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
    green: 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    red: 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300',
    amber: 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
    violet: 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
};

export const paymentIntentStatusColors = {
    awaiting_payment_method: colors.gray,
    awaiting_next_action: colors.amber,
    awaiting_capture: colors.violet,
    processing: colors.blue,
    succeeded: colors.green,
    canceled: colors.red,
};

export const paymentStatusColors = {
    paid: colors.green,
    failed: colors.red,
};

export const checkoutSessionStatusColors = {
    active: colors.blue,
    completed: colors.green,
    expired: colors.amber,
};

export const billingStatementStatusColors = {
    draft: colors.gray,
    open: colors.blue,
    paid: colors.green,
    void: colors.red,
    uncollectible: colors.amber,
    overdue: colors.amber,
};

<?php

declare(strict_types=1);

use App\Http\Controllers\BillingStatementController;
use App\Http\Controllers\CheckoutSessionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ElementsController;
use App\Http\Controllers\Feature;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentIntentController;
use App\Http\Controllers\PaymentSuccessController;
use App\Http\Controllers\ReceivedWebhookController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\WebhookManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('customers', CustomerController::class);

    Route::resource('payment-intents', PaymentIntentController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('payment-intents/{paymentIntent}/cancel', [PaymentIntentController::class, 'cancel'])->name('payment-intents.cancel');
    Route::post('payment-intents/{paymentIntent}/capture', [PaymentIntentController::class, 'capture'])->name('payment-intents.capture');

    Route::get('elements', [ElementsController::class, 'index'])->name('elements.index');
    Route::post('elements/create-intent', [ElementsController::class, 'createIntent'])->name('elements.create-intent');
    Route::get('elements/success', PaymentSuccessController::class)->name('elements.success');

    Route::resource('checkout-sessions', CheckoutSessionController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('checkout-sessions/{checkoutSession}/expire', [CheckoutSessionController::class, 'expire'])->name('checkout-sessions.expire');

    Route::resource('billing-statements', BillingStatementController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('billing-statements/{billingStatement}/line-items', [BillingStatementController::class, 'addLineItem'])->name('billing-statements.line-items.store');
    Route::put('billing-statements/{billingStatement}/line-items/{lineItem}', [BillingStatementController::class, 'updateLineItem'])->name('billing-statements.line-items.update');
    Route::delete('billing-statements/{billingStatement}/line-items/{lineItem}', [BillingStatementController::class, 'deleteLineItem'])->name('billing-statements.line-items.destroy');
    Route::post('billing-statements/{billingStatement}/finalize', [BillingStatementController::class, 'finalize'])->name('billing-statements.finalize');
    Route::post('billing-statements/{billingStatement}/send', [BillingStatementController::class, 'send'])->name('billing-statements.send');
    Route::post('billing-statements/{billingStatement}/void', [BillingStatementController::class, 'void'])->name('billing-statements.void');
    Route::post('billing-statements/{billingStatement}/mark-uncollectible', [BillingStatementController::class, 'markUncollectible'])->name('billing-statements.mark-uncollectible');

    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('payments/{payment}/refund', [RefundController::class, 'create'])->name('refunds.create');
    Route::post('refunds', [RefundController::class, 'store'])->name('refunds.store');

    Route::get('webhooks', [WebhookManagementController::class, 'index'])->name('webhooks.index');
    Route::get('webhooks/create', [WebhookManagementController::class, 'create'])->name('webhooks.create');
    Route::get('webhooks/received', [ReceivedWebhookController::class, 'index'])->name('webhooks.received');
    Route::post('webhooks', [WebhookManagementController::class, 'store'])->name('webhooks.store');
    Route::get('webhooks/{webhook}', [WebhookManagementController::class, 'show'])->name('webhooks.show');
    Route::post('webhooks/{webhook}/toggle', [WebhookManagementController::class, 'toggle'])->name('webhooks.toggle');
    Route::delete('webhooks/{webhook}', [WebhookManagementController::class, 'destroy'])->name('webhooks.destroy');

    Route::prefix('features')->name('features.')->group(function () {
        Route::get('billable-customer', [Feature\BillableCustomerController::class, 'index'])->name('billable-customer');
        Route::post('billable-customer/create', [Feature\BillableCustomerController::class, 'createCustomer'])->name('billable-customer.create');
        Route::post('billable-customer/retrieve', [Feature\BillableCustomerController::class, 'retrieveCustomer'])->name('billable-customer.retrieve');
        Route::post('billable-customer/delete', [Feature\BillableCustomerController::class, 'deleteCustomer'])->name('billable-customer.delete');

        Route::get('pagination', [Feature\PaginationController::class, 'index'])->name('pagination');

        Route::get('error-handling', [Feature\ErrorHandlingController::class, 'index'])->name('error-handling');
        Route::post('error-handling/trigger', [Feature\ErrorHandlingController::class, 'trigger'])->name('error-handling.trigger');

        Route::get('webhook-events', [Feature\WebhookEventsController::class, 'index'])->name('webhook-events');

        Route::get('idempotency', [Feature\IdempotencyController::class, 'index'])->name('idempotency');
        Route::post('idempotency/create', [Feature\IdempotencyController::class, 'createWithKey'])->name('idempotency.create');

        Route::get('response-metadata', [Feature\ResponseMetadataController::class, 'index'])->name('response-metadata');
        Route::post('response-metadata/fetch', [Feature\ResponseMetadataController::class, 'fetch'])->name('response-metadata.fetch');

        Route::get('default-currency', [Feature\DefaultCurrencyController::class, 'index'])->name('default-currency');

        Route::get('construct-event', [Feature\ConstructEventController::class, 'index'])->name('construct-event');
        Route::post('construct-event/verify', [Feature\ConstructEventController::class, 'verify'])->name('construct-event.verify');
        Route::post('construct-event/generate-signature', [Feature\ConstructEventController::class, 'generateSignature'])->name('construct-event.generate-signature');

        Route::get('artisan-commands', [Feature\ArtisanCommandsController::class, 'index'])->name('artisan-commands');
    });
});

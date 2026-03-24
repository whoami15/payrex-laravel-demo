<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\ProcessedWebhookEvent;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LegionHQ\LaravelPayrex\Data\PaymentIntent;
use LegionHQ\LaravelPayrex\Events\PaymentIntentSucceeded;

class HandlePaymentIntentSucceeded
{
    public function handle(PaymentIntentSucceeded $event): void
    {
        $eventId = $event->payload['id'];

        try {
            DB::transaction(function () use ($eventId, $event): void {
                ProcessedWebhookEvent::query()->create([
                    'event_id' => $eventId,
                    'event_type' => $event->payload['type'] ?? 'payment_intent.succeeded',
                    'resource_id' => $event->payload['data']['id'] ?? null,
                    'payload' => $event->payload,
                    'processed_at' => now(),
                ]);

                /** @var PaymentIntent $paymentIntent */
                $paymentIntent = $event->data();

                Log::info('Payment intent succeeded', [
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            Log::info('Skipping duplicate webhook event', ['event_id' => $eventId]);
        }
    }
}

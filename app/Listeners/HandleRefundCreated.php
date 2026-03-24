<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\ProcessedWebhookEvent;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LegionHQ\LaravelPayrex\Data\Refund;
use LegionHQ\LaravelPayrex\Events\RefundCreated;

class HandleRefundCreated
{
    public function handle(RefundCreated $event): void
    {
        $eventId = $event->payload['id'];

        try {
            DB::transaction(function () use ($eventId, $event): void {
                ProcessedWebhookEvent::query()->create([
                    'event_id' => $eventId,
                    'event_type' => $event->payload['type'] ?? 'refund.created',
                    'resource_id' => $event->payload['data']['id'] ?? null,
                    'payload' => $event->payload,
                    'processed_at' => now(),
                ]);

                /** @var Refund $refund */
                $refund = $event->data();

                Log::info('Refund created', [
                    'refund_id' => $refund->id,
                    'payment_id' => $refund->paymentId,
                    'amount' => $refund->amount,
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            Log::info('Skipping duplicate webhook event', ['event_id' => $eventId]);
        }
    }
}

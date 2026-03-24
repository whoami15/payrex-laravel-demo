<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\ProcessedWebhookEvent;
use Illuminate\Support\Facades\Log;
use LegionHQ\LaravelPayrex\Events\WebhookReceived;

class LogWebhookReceived
{
    public function handle(WebhookReceived $event): void
    {
        $eventId = $event->payload['id'] ?? null;

        Log::info('PayRex webhook received', [
            'type' => $event->eventType()?->value,
            'livemode' => $event->isLiveMode(),
            'event_id' => $eventId,
        ]);

        if (! $eventId) {
            return;
        }

        ProcessedWebhookEvent::query()->firstOrCreate(
            ['event_id' => $eventId],
            [
                'event_type' => $event->eventType()->value ?? 'unknown',
                'resource_id' => $event->payload['data']['id'] ?? null,
                'payload' => $event->payload,
                'processed_at' => now(),
            ],
        );
    }
}

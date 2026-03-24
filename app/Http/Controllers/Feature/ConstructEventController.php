<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use App\Models\ProcessedWebhookEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\WebhookVerificationException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class ConstructEventController
{
    public function index(): Response
    {
        $fixtures = Cache::rememberForever('webhook_fixtures', fn () => collect(File::files(resource_path('fixtures/webhooks')))
            ->mapWithKeys(function ($file) {
                $type = Str::before($file->getFilename(), '.json');

                return [$type => json_decode($file->getContents(), true)];
            })
            ->sortKeys()
            ->all()
        );

        return Inertia::render('Features/ConstructEvent', [
            'fixtures' => $fixtures,
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $payload = $request->input('payload', '{}');
        $signature = $request->input('signature', '');
        $secret = $request->input('secret', '');

        try {
            $event = Payrex::constructEvent($payload, $signature, $secret);

            $eventId = $event->payload['id'] ?? null;
            $record = null;

            if ($eventId) {
                $record = ProcessedWebhookEvent::query()->firstOrCreate(
                    ['event_id' => $eventId],
                    [
                        'event_type' => $event->eventType()->value ?? 'unknown',
                        'resource_id' => $event->data()->id ?? null,
                        'payload' => $event->payload,
                        'processed_at' => now(),
                    ],
                );
            }

            return response()->json([
                'success' => true,
                'event_type' => $event->eventType()?->value,
                'event_id' => $eventId,
                'is_live_mode' => $event->isLiveMode(),
                'data_type' => class_basename($event->data()),
                'idempotency' => $record?->wasRecentlyCreated === false ? 'skipped (duplicate)' : 'processed',
            ]);
        } catch (WebhookVerificationException $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
                'exception_type' => 'WebhookVerificationException',
            ]);
        }
    }

    public function generateSignature(Request $request): JsonResponse
    {
        $payload = $request->input('payload', '{}');
        $secret = $request->input('secret', '');
        $timestamp = (string) time();

        $hmac = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        return response()->json([
            'signature' => 't='.$timestamp.',te='.$hmac,
        ]);
    }
}

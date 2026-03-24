<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Enums\WebhookEventType;

class WebhookEventsController
{
    public function index(): Response
    {
        $events = collect(WebhookEventType::cases())->map(fn (WebhookEventType $type) => [
            'value' => $type->value,
            'event_class' => class_basename($type->eventClass()),
        ])->values()->all();

        return Inertia::render('Features/WebhookEvents', [
            'eventTypes' => $events,
        ]);
    }
}

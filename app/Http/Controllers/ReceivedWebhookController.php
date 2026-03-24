<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ProcessedWebhookEvent;
use Inertia\Inertia;
use Inertia\Response;

class ReceivedWebhookController
{
    public function index(): Response
    {
        return Inertia::render('Webhooks/ReceivedEvents', [
            'events' => ProcessedWebhookEvent::query()
                ->orderByDesc('processed_at')
                ->limit(50)
                ->get(),
        ]);
    }
}

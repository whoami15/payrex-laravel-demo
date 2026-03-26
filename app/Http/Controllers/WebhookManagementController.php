<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreWebhookEndpointRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Enums\WebhookEndpointStatus;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class WebhookManagementController
{
    public function index(Request $request): Response
    {
        $url = $request->string('url')->toString() ?: null;
        $description = $request->string('description')->toString() ?: null;
        $limit = $request->integer('limit', 10);
        $limit = min(max($limit, 1), 100);

        return Inertia::render('Webhooks/Index', [
            'webhooks' => Inertia::defer(fn () => Payrex::webhooks()->list(array_filter([
                'limit' => $limit,
                'url' => $url,
                'description' => $description,
            ]))),
            'filters' => [
                'url' => $url,
                'description' => $description,
                'limit' => $limit,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Webhooks/Create');
    }

    public function store(StoreWebhookEndpointRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $webhook = Payrex::webhooks()->create([
                'url' => $validated['url'],
                'events' => $validated['events'],
                'description' => $validated['description'] ?? null,
            ]);

            Inertia::flash('message', 'Webhook endpoint created. Copy the secret key below - it will not be shown again.');

            return redirect()->route('webhooks.show', $webhook->id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function show(string $id): Response|RedirectResponse
    {
        try {
            $webhook = Payrex::webhooks()->retrieve($id);

            return Inertia::render('Webhooks/Show', [
                'webhook' => $webhook->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('webhooks.index');
        }
    }

    public function toggle(string $id): RedirectResponse
    {
        try {
            $webhook = Payrex::webhooks()->retrieve($id);

            if ($webhook->status === WebhookEndpointStatus::Enabled) {
                Payrex::webhooks()->disable($id);
                $message = 'Webhook endpoint disabled.';
            } else {
                Payrex::webhooks()->enable($id);
                $message = 'Webhook endpoint enabled.';
            }

            Inertia::flash('message', $message);

            return back();
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        Inertia::flash('message', 'Nope! This webhook is working hard. Let it be.')->flash('type', 'warning');

        return back();

        try {
            Payrex::webhooks()->delete($id);

            Inertia::flash('message', 'Webhook endpoint deleted.');

            return redirect()->route('webhooks.index');
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }
}

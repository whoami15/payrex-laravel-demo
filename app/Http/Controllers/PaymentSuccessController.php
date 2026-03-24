<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class PaymentSuccessController
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $clientSecret = $request->query('payment_intent_client_secret', '');
        $paymentIntentId = Str::before($clientSecret, '_secret_') ?: null;

        if (! $paymentIntentId) {
            Inertia::flash('message', 'Invalid payment session.')->flash('type', 'error');

            return redirect()->route('elements.index');
        }

        try {
            $paymentIntent = Payrex::paymentIntents()->retrieve($paymentIntentId);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('elements.index');
        }

        return Inertia::render('Elements/Success', [
            'paymentIntent' => $paymentIntent->toArray(),
            'status' => $paymentIntent->status->value,
        ]);
    }
}

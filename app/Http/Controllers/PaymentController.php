<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class PaymentController
{
    public function show(string $id): Response|RedirectResponse
    {
        try {
            $payment = Payrex::payments()->retrieve($id);

            return Inertia::render('Payments/Show', [
                'payment' => $payment->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('dashboard');
        }
    }
}

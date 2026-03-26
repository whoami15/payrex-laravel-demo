<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRefundRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class RefundController
{
    public function create(string $id): Response|RedirectResponse
    {
        try {
            $payment = Payrex::payments()->retrieve($id);

            return Inertia::render('Refunds/Create', [
                'payment' => $payment->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('payment-intents.index');
        }
    }

    public function store(StoreRefundRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $params = [
                'payment_id' => $validated['payment_id'],
                'amount' => (int) round($validated['amount'] * 100),
                'reason' => $validated['reason'],
            ];

            if ($request->filled('remarks')) {
                $params['remarks'] = $validated['remarks'];
            }

            Payrex::refunds()->create($params);

            Inertia::flash('message', 'Refund created.');

            return redirect()->route('payments.show', $validated['payment_id']);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }
}

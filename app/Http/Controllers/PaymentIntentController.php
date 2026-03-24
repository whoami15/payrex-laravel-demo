<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Enums\PaymentMethod;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class PaymentIntentController
{
    public function index(): Response
    {
        return Inertia::render('PaymentIntents/Index');
    }

    public function create(Request $request): Response
    {
        return Inertia::render('PaymentIntents/Create', [
            'products' => Product::query()->where('category', 'product')->inRandomOrder()->get(),
            'customers' => User::query()->where('id', '!=', $request->user()->id)->get(['id', 'name', 'email', 'payrex_customer_id']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['string', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'capture_type' => ['nullable', 'string', Rule::in(['automatic', 'manual'])],
            'user_id' => ['nullable', 'exists:users,id'],
            'statement_descriptor' => ['nullable', 'string', 'max:22'],
            'metadata' => ['nullable', 'array'],
        ]);

        try {
            $params = [
                'amount' => (int) round($validated['amount'] * 100),
                'description' => $validated['description'],
                'payment_methods' => $validated['payment_methods'],
            ];

            if ($request->filled('user_id')) {
                $user = User::query()->findOrFail($validated['user_id']);

                if (! $user->hasPayrexCustomerId()) {
                    $user->createAsPayrexCustomer();
                }

                $params['customer_id'] = $user->payrexCustomerId();
            }

            if ($request->filled('statement_descriptor')) {
                $params['statement_descriptor'] = $validated['statement_descriptor'];
            }

            if ($request->filled('metadata')) {
                $params['metadata'] = $validated['metadata'];
            }

            // Manual capture requires payment_method_options
            if (($validated['capture_type'] ?? 'automatic') === 'manual') {
                $params['payment_method_options'] = [
                    'card' => ['capture_type' => 'manual'],
                ];
            }

            $paymentIntent = Payrex::paymentIntents()->create($params);

            Inertia::flash('message', 'Payment intent created.');

            return redirect()->route('payment-intents.show', $paymentIntent->id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function show(string $id): Response|RedirectResponse
    {
        try {
            $paymentIntent = Payrex::paymentIntents()->retrieve($id);

            return Inertia::render('PaymentIntents/Show', [
                'paymentIntent' => $paymentIntent->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('payment-intents.index');
        }
    }

    public function cancel(string $id): RedirectResponse
    {
        try {
            Payrex::paymentIntents()->cancel($id);

            Inertia::flash('message', 'Payment intent canceled.');

            return redirect()->route('payment-intents.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function capture(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        try {
            $params = $request->filled('amount')
                ? ['amount' => (int) round($validated['amount'] * 100)]
                : [];

            Payrex::paymentIntents()->capture($id, $params);

            Inertia::flash('message', 'Payment intent captured.');

            return redirect()->route('payment-intents.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }
}

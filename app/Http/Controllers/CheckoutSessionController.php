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

class CheckoutSessionController
{
    public function index(): Response
    {
        return Inertia::render('CheckoutSessions/Index');
    }

    public function create(Request $request): Response
    {
        return Inertia::render('CheckoutSessions/Create', [
            'products' => Product::query()->where('category', 'product')->inRandomOrder()->get(),
            'customers' => User::query()->where('id', '!=', $request->user()->id)->get(['id', 'name', 'email', 'payrex_customer_id']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.name' => ['required', 'string'],
            'line_items.*.amount' => ['required', 'numeric', 'min:1'],
            'line_items.*.quantity' => ['required', 'integer', 'min:1'],
            'line_items.*.description' => ['nullable', 'string'],
            'line_items.*.image' => ['nullable', 'url'],
            'description' => ['nullable', 'string', 'max:255'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['string', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'submit_type' => ['nullable', 'string', Rule::in(['pay', 'donate'])],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        try {
            $params = [
                'line_items' => array_map(fn (array $item) => array_filter([
                    ...$item,
                    'amount' => (int) round($item['amount'] * 100),
                ], fn (mixed $value) => $value !== '' && $value !== null), $validated['line_items']),
                'payment_methods' => $validated['payment_methods'],
                'success_url' => route('checkout-sessions.index'),
                'cancel_url' => route('checkout-sessions.index'),
            ];

            if ($request->filled('description')) {
                $params['description'] = $validated['description'];
            }

            if ($request->filled('submit_type')) {
                $params['submit_type'] = $validated['submit_type'];
            }

            if ($request->filled('user_id')) {
                $user = User::query()->findOrFail($validated['user_id']);

                if (! $user->hasPayrexCustomerId()) {
                    $user->createAsPayrexCustomer();
                }

                $params['customer_id'] = $user->payrexCustomerId();
            }

            $checkoutSession = Payrex::checkoutSessions()->create($params);

            Inertia::flash('message', 'Checkout session created.');

            return redirect()->route('checkout-sessions.show', $checkoutSession->id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function show(string $id): Response|RedirectResponse
    {
        try {
            $checkoutSession = Payrex::checkoutSessions()->retrieve($id);

            return Inertia::render('CheckoutSessions/Show', [
                'checkoutSession' => $checkoutSession->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('checkout-sessions.index');
        }
    }

    public function expire(string $id): RedirectResponse
    {
        try {
            Payrex::checkoutSessions()->expire($id);

            Inertia::flash('message', 'Checkout session expired.');

            return redirect()->route('checkout-sessions.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }
}

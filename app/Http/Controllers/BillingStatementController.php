<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Enums\BillingStatementStatus;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class BillingStatementController
{
    public function index(Request $request): Response
    {
        $after = $request->string('after')->toString() ?: null;
        $limit = $request->integer('limit', 10);
        $limit = min(max($limit, 1), 100);

        return Inertia::render('BillingStatements/Index', [
            'billingStatements' => Inertia::defer(fn () => Payrex::billingStatements()->list(array_filter([
                'limit' => $limit,
                'after' => $after,
            ]))),
            'filters' => [
                'limit' => $limit,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('BillingStatements/Create', [
            'customers' => User::query()->where('id', '!=', $request->user()->id)->get(['id', 'name', 'email', 'payrex_customer_id']),
            'products' => Product::query()->where('category', 'service')->inRandomOrder()->limit(5)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'line_items' => ['required', 'array', 'min:1'],
            'line_items.*.description' => ['required', 'string'],
            'line_items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
            'line_items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        if (! $user->hasPayrexCustomerId()) {
            $user->createAsPayrexCustomer();
        }

        try {
            $params = [
                'customer_id' => $user->payrexCustomerId(),
            ];

            if ($request->filled('description')) {
                $params['description'] = $validated['description'];
            }

            $billingStatement = Payrex::billingStatements()->create($params);

            $failedItems = 0;
            foreach ($validated['line_items'] as $lineItem) {
                try {
                    Payrex::billingStatementLineItems()->create([
                        'billing_statement_id' => $billingStatement->id,
                        'description' => $lineItem['description'],
                        'unit_price' => (int) round($lineItem['unit_price'] * 100),
                        'quantity' => $lineItem['quantity'],
                    ]);
                } catch (PayrexApiException $exception) {
                    Log::warning('Failed to create billing statement line item', [
                        'billing_statement_id' => $billingStatement->id,
                        'error' => $exception->getMessage(),
                    ]);
                    $failedItems++;
                }
            }

            $message = 'Billing statement created as draft.';
            if ($failedItems > 0) {
                $message .= " However, {$failedItems} line item(s) failed to create.";

                Inertia::flash('message', $message)->flash('type', 'warning');

                return redirect()->route('billing-statements.show', $billingStatement->id);
            }

            Inertia::flash('message', $message);

            return redirect()->route('billing-statements.show', $billingStatement->id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function show(string $id): Response|RedirectResponse
    {
        try {
            $billingStatement = Payrex::billingStatements()->retrieve($id);

            return Inertia::render('BillingStatements/Show', [
                'billingStatement' => $billingStatement->toArray(),
                'products' => $billingStatement->status === BillingStatementStatus::Draft
                    ? Product::query()->where('category', 'service')->inRandomOrder()->limit(5)->get()
                    : [],
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('billing-statements.index');
        }
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'due_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $billingStatement = Payrex::billingStatements()->retrieve($id);

            $params = [
                'payment_settings' => $billingStatement['payment_settings'] ?? [],
            ];

            if ($request->filled('due_at')) {
                $params['due_at'] = $request->date('due_at')->timestamp;
            }

            if ($request->filled('description')) {
                $params['description'] = $validated['description'];
            }

            Payrex::billingStatements()->update($id, $params);

            Inertia::flash('message', 'Billing statement updated.');

            return redirect()->route('billing-statements.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function addLineItem(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0.01'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            Payrex::billingStatementLineItems()->create([
                'billing_statement_id' => $id,
                'description' => $validated['description'],
                'unit_price' => (int) round($validated['unit_price'] * 100),
                'quantity' => $validated['quantity'],
            ]);

            Inertia::flash('message', 'Line item added.');

            return back();
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function updateLineItem(Request $request, string $_billingStatementId, string $lineItemId): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0.01'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            Payrex::billingStatementLineItems()->update($lineItemId, [
                'description' => $validated['description'],
                'unit_price' => (int) round($validated['unit_price'] * 100),
                'quantity' => $validated['quantity'],
            ]);

            Inertia::flash('message', 'Line item updated.');

            return back();
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function deleteLineItem(string $_billingStatementId, string $lineItemId): RedirectResponse
    {
        try {
            Payrex::billingStatementLineItems()->delete($lineItemId);

            Inertia::flash('message', 'Line item deleted.');

            return back();
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function finalize(string $id): RedirectResponse
    {
        try {
            Payrex::billingStatements()->finalize($id);

            Inertia::flash('message', 'Billing statement finalized.');

            return redirect()->route('billing-statements.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function send(string $id): RedirectResponse
    {
        try {
            Payrex::billingStatements()->send($id);

            Inertia::flash('message', 'Billing statement sent.');

            return redirect()->route('billing-statements.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function void(string $id): RedirectResponse
    {
        try {
            Payrex::billingStatements()->void($id);

            Inertia::flash('message', 'Billing statement voided.');

            return redirect()->route('billing-statements.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function markUncollectible(string $id): RedirectResponse
    {
        try {
            Payrex::billingStatements()->markUncollectible($id);

            Inertia::flash('message', 'Billing statement marked as uncollectible.');

            return redirect()->route('billing-statements.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }
}

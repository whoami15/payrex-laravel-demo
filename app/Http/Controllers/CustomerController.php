<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class CustomerController
{
    public function index(Request $request): Response
    {
        $after = $request->string('after')->toString() ?: null;
        $name = $request->string('name')->toString() ?: null;
        $email = $request->string('email')->toString() ?: null;
        $limit = $request->integer('limit', 10);
        $limit = min(max($limit, 1), 100);

        return Inertia::render('Customers/Index', [
            'customers' => Inertia::defer(fn () => Payrex::customers()->list(array_filter([
                'limit' => $limit,
                'after' => $after,
                'name' => $name,
                'email' => $email,
            ]))),
            'filters' => [
                'name' => $name,
                'email' => $email,
                'limit' => $limit,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'line1' => ['nullable', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:2'],
        ]);

        try {
            $params = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            $address = array_filter([
                'line1' => $validated['line1'] ?? null,
                'line2' => $validated['line2'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? null,
            ]);

            $billing = array_filter([
                'phone' => $validated['phone'] ?? null,
                'address' => $address ?: null,
            ]);

            if ($billing) {
                $params['billing_details'] = $billing;
            }

            Payrex::customers()->create($params);

            Inertia::flash('message', 'Customer created.');

            return redirect()->route('customers.index');
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function show(string $id): Response|RedirectResponse
    {
        try {
            $customer = Payrex::customers()->retrieve($id);

            return Inertia::render('Customers/Show', [
                'customer' => $customer->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('customers.index');
        }
    }

    public function edit(string $id): Response|RedirectResponse
    {
        try {
            $customer = Payrex::customers()->retrieve($id);

            return Inertia::render('Customers/Edit', [
                'customer' => $customer->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return redirect()->route('customers.index');
        }
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'line1' => ['nullable', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:2'],
        ]);

        try {
            $params = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            $billing = array_filter([
                'phone' => $validated['phone'] ?? null,
                'address' => array_filter([
                    'line1' => $validated['line1'] ?? null,
                    'line2' => $validated['line2'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? null,
                    'postal_code' => $validated['postal_code'] ?? null,
                    'country' => $validated['country'] ?? null,
                ]),
            ]);

            if (! empty($billing)) {
                $params['billing_details'] = $billing;
            }

            Payrex::customers()->update($id, $params);

            Inertia::flash('message', 'Customer updated.');

            return redirect()->route('customers.show', $id);
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            Payrex::customers()->delete($id);

            Inertia::flash('message', 'Customer deleted.');

            return redirect()->route('customers.index');
        } catch (PayrexApiException $exception) {
            Inertia::flash('message', $exception->getMessage())->flash('type', 'error');

            return back();
        }
    }
}

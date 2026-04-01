<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateElementsPaymentIntentRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Enums\CaptureType;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class ElementsController
{
    public function index(Request $request): Response
    {
        return Inertia::render('Elements/Index', [
            'products' => Product::query()->where('category', 'product')->inRandomOrder()->get(),
            'customers' => User::query()->where('id', '!=', $request->user()->id)->get(['id', 'name', 'email', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country', 'payrex_customer_id']),
            'publicKey' => config('payrex.public_key'),
        ]);
    }

    public function createIntent(CreateElementsPaymentIntentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $params = [
                'amount' => (int) round($validated['amount'] * 100),
                'description' => $validated['description'],
                'payment_methods' => $validated['payment_methods'],
            ];

            if ($request->filled('capture_type') && $validated['capture_type'] === CaptureType::Manual->value) {
                $params['payment_method_options'] = [
                    'card' => ['capture_type' => CaptureType::Manual->value],
                ];
            }

            if ($request->filled('statement_descriptor')) {
                $params['statement_descriptor'] = $validated['statement_descriptor'];
            }

            if ($request->filled('user_id')) {
                $user = User::query()->findOrFail($validated['user_id']);
                if (! $user->hasPayrexCustomerId()) {
                    $user->createAsPayrexCustomer();
                }
                $params['customer_id'] = $user->payrexCustomerId();
            }

            $paymentIntent = Payrex::paymentIntents()->create($params);

            return response()->json([
                'client_secret' => $paymentIntent->clientSecret,
                'return_url' => route('elements.success'),
            ]);
        } catch (PayrexApiException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class IdempotencyController
{
    public function index(): Response
    {
        return Inertia::render('Features/Idempotency');
    }

    public function createWithKey(Request $request): JsonResponse
    {
        $idempotencyKey = $request->input('idempotency_key', Str::uuid()->toString());

        try {
            $customer = Payrex::customers()->create([
                'name' => 'Idempotency Test '.now()->format('H:i:s'),
                'email' => 'idempotency-'.Str::random(6).'@example.com',
            ], $idempotencyKey);

            return response()->json([
                'success' => true,
                'customer' => $customer->toArray(),
                'idempotency_key' => $idempotencyKey,
            ]);
        } catch (PayrexApiException $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
                'idempotency_key' => $idempotencyKey,
            ], 422);
        }
    }
}

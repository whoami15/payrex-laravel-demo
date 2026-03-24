<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;

class BillableCustomerController
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Features/BillableCustomer', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'payrex_customer_id' => $user->payrexCustomerId(),
            ],
        ]);
    }

    public function createCustomer(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            $customer = $user->createAsPayrexCustomer();

            return response()->json([
                'success' => true,
                'customer' => $customer->toArray(),
                'payrex_customer_id' => $user->fresh()->payrexCustomerId(),
            ]);
        } catch (PayrexApiException $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
            ], 422);
        }
    }

    public function retrieveCustomer(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            $customer = $user->asPayrexCustomer();

            return response()->json([
                'success' => true,
                'customer' => $customer->toArray(),
            ]);
        } catch (PayrexApiException $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
            ], 422);
        }
    }

    public function deleteCustomer(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            $result = $user->deleteAsPayrexCustomer();

            return response()->json([
                'success' => true,
                'result' => $result->toArray(),
                'payrex_customer_id' => $user->fresh()->payrexCustomerId(),
            ]);
        } catch (PayrexApiException $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
            ], 422);
        }
    }
}

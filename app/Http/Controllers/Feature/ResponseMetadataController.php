<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class ResponseMetadataController
{
    public function index(): Response
    {
        return Inertia::render('Features/ResponseMetadata');
    }

    public function fetch(): JsonResponse
    {
        try {
            $customers = Payrex::customers()->list(['limit' => 100]);
            $metadata = Payrex::getLastResponse();

            return response()->json([
                'success' => true,
                'metadata' => [
                    'status_code' => $metadata?->statusCode,
                    'headers' => $metadata?->headers,
                ],
                'customers_count' => $customers->count(),
            ]);
        } catch (PayrexApiException $exception) {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage(),
            ], 422);
        }
    }
}

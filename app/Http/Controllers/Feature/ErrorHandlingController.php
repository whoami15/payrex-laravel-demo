<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Exceptions\AuthenticationException;
use LegionHQ\LaravelPayrex\Exceptions\InvalidRequestException;
use LegionHQ\LaravelPayrex\Exceptions\PayrexApiException;
use LegionHQ\LaravelPayrex\Exceptions\ResourceNotFoundException;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class ErrorHandlingController
{
    public function index(): Response
    {
        return Inertia::render('Features/ErrorHandling');
    }

    public function trigger(Request $request): JsonResponse
    {
        $type = $request->input('type', 'not_found');

        try {
            match ($type) {
                'not_found' => Payrex::customers()->retrieve('cus_nonexistent_id'),
                'invalid_request' => Payrex::paymentIntents()->create([
                    'amount' => -1,
                    'payment_methods' => ['card'],
                ]),
                default => Payrex::customers()->retrieve('cus_nonexistent_id'),
            };

            return response()->json(['error' => null]);
        } catch (AuthenticationException $exception) {
            return response()->json([
                'error' => [
                    'type' => 'AuthenticationException',
                    'message' => $exception->getMessage(),
                    'status_code' => $exception->statusCode,
                ],
            ]);
        } catch (ResourceNotFoundException $exception) {
            return response()->json([
                'error' => [
                    'type' => 'ResourceNotFoundException',
                    'message' => $exception->getMessage(),
                    'status_code' => $exception->statusCode,
                ],
            ]);
        } catch (InvalidRequestException $exception) {
            return response()->json([
                'error' => [
                    'type' => 'InvalidRequestException',
                    'message' => $exception->getMessage(),
                    'status_code' => $exception->statusCode,
                    'errors' => $exception->errors,
                ],
            ]);
        } catch (PayrexApiException $exception) {
            return response()->json([
                'error' => [
                    'type' => class_basename($exception),
                    'message' => $exception->getMessage(),
                    'status_code' => $exception->statusCode,
                ],
            ]);
        }
    }
}

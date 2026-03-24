<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class PaginationController
{
    public function index(Request $request): Response
    {
        $limit = min(max($request->integer('limit', 10), 1), 100);
        $name = $request->string('name')->toString() ?: null;
        $email = $request->string('email')->toString() ?: null;

        return Inertia::render('Features/Pagination', [
            'customers' => Inertia::defer(fn () => Payrex::customers()->paginate(
                perPage: $limit,
                params: array_filter([
                    'name' => $name,
                    'email' => $email,
                ]),
            )->withQueryString()),
            'filters' => [
                'name' => $name,
                'email' => $email,
                'limit' => $limit,
            ],
        ]);
    }
}

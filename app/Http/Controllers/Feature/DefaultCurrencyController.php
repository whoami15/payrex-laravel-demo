<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Inertia\Inertia;
use Inertia\Response;
use LegionHQ\LaravelPayrex\Facades\Payrex;

class DefaultCurrencyController
{
    public function index(): Response
    {
        return Inertia::render('Features/DefaultCurrency', [
            'defaultCurrency' => Payrex::defaultCurrency(),
        ]);
    }
}

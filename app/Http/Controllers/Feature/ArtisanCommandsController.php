<?php

declare(strict_types=1);

namespace App\Http\Controllers\Feature;

use Inertia\Inertia;
use Inertia\Response;

class ArtisanCommandsController
{
    public function index(): Response
    {
        return Inertia::render('Features/ArtisanCommands');
    }
}

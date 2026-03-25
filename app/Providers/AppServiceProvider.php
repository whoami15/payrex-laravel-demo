<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\HandlePaymentIntentSucceeded;
use App\Listeners\HandleRefundCreated;
use App\Listeners\LogWebhookReceived;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\ExceptionResponse;
use Inertia\Inertia;
use LegionHQ\LaravelPayrex\Events\PaymentIntentSucceeded;
use LegionHQ\LaravelPayrex\Events\RefundCreated;
use LegionHQ\LaravelPayrex\Events\WebhookReceived;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureInertia();
        $this->configureWebhookListeners();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        JsonResource::withoutWrapping();

        Date::use(CarbonImmutable::class);

        // DB::prohibitDestructiveCommands(
        //     app()->isProduction(),
        // );

        Model::automaticallyEagerLoadRelationships();
        Model::shouldBeStrict();

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }

    /**
     * Configure rate limiting for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('global', fn () => Limit::perMinute(120)->by(request()->ip()));
    }

    /**
     * Register PayRex webhook event listeners with idempotent handling.
     */
    protected function configureWebhookListeners(): void
    {
        Event::listen(WebhookReceived::class, LogWebhookReceived::class);
        Event::listen(PaymentIntentSucceeded::class, HandlePaymentIntentSucceeded::class);
        Event::listen(RefundCreated::class, HandleRefundCreated::class);
    }

    /**
     * Configure Inertia error handling.
     */
    protected function configureInertia(): void
    {
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            if (in_array($response->statusCode(), [403, 404, 419, 429, 500, 503])) {
                return $response->render('ErrorPage', [
                    'status' => $response->statusCode(),
                ])->withSharedData();
            }
        });
    }
}

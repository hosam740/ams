<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\Contract;
use App\Observers\ContractObserver;
use App\Services\Payments\PaymentGeneration\PaymentGeneratorInterface;
use App\Services\Payments\PaymentGeneration\SimplePaymentGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * This is where we bind interfaces to implementations.
     * When any class asks for PaymentGeneratorInterface,
     * Laravel will give it SimplePaymentGenerator.
     */
    public function register(): void
    {
        //
        $this->app->bind(
            PaymentGeneratorInterface::class,
            SimplePaymentGenerator::class
        );
    }

    /**
     * Bootstrap any application services.
     * 
     * This runs after all services are registered.
     * We register observers here so they listen to model events.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Contract::observe(ContractObserver::class);
    }
}

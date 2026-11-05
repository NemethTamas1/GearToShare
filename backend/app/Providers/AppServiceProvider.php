<?php

namespace App\Providers;

use App\Services\Payments\FakePaymentGateway;
use App\Services\Payments\BarionPaymentGateway;
use App\Services\Payments\PaymentGateway;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, function () {
            return config('services.payments.driver') === 'barion'
                ? new BarionPaymentGateway(
                    config('services.barion.base_url'),
                    config('services.barion.pos_key'),
                    config('services.barion.payee')
                )
                : new FakePaymentGateway();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

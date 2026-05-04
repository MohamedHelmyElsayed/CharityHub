<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\StripeGateway;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind PaymentGatewayInterface to PaymobGateway
        $this->app->bind(PaymentGatewayInterface::class, \App\Services\PaymobGateway::class);
    }

    public function boot(): void
    {
        Event::listen(
            \App\Events\DonationReceived::class,
            \App\Listeners\SendDonationConfirmation::class,
        );

        Event::listen(
            \App\Events\DonationReceived::class,
            \App\Listeners\GenerateDonationCertificate::class,
        );
    }
}

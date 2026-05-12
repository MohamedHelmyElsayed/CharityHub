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
        // Bind PaymentGatewayInterface to FailoverPaymentGateway
        $this->app->bind(\App\Contracts\PaymentGatewayInterface::class, \App\Services\FailoverPaymentGateway::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\URL::forceScheme('https');
        Event::listen(
            \App\Events\DonationReceived::class,
            \App\Listeners\SendDonationConfirmation::class,
        );

        Event::listen(
            \App\Events\DonationReceived::class,
            \App\Listeners\GenerateDonationCertificate::class,
        );

        Event::listen(
            \App\Events\DonationReceived::class,
            \App\Listeners\UpdateCampaignProgress::class,
        );

        Event::listen(
            \App\Events\DonationReceived::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\PaymentFailed::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\RefundIssued::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\RefundIssued::class,
            \App\Listeners\UpdateCampaignProgress::class,
        );

        Event::listen(
            \App\Events\SubscriptionRenewed::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\SubscriptionRenewed::class,
            \App\Listeners\GenerateDonationCertificate::class,
        );

        Event::listen(
            \App\Events\SubscriptionRenewed::class,
            \App\Listeners\UpdateCampaignProgress::class,
        );

        Event::listen(
            \App\Events\SubscriptionCreated::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\SubscriptionCancelled::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\RenewalFailed::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\WebhookReceived::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        Event::listen(
            \App\Events\WebhookFailed::class,
            \App\Listeners\LogFinancialTransaction::class,
        );

        // ── Volunteer Management Events ──────────────────────────────────────
        Event::listen(
            \App\Events\VolunteerApproved::class,
            \App\Listeners\SendVolunteerApprovalNotification::class,
        );

        Event::listen(
            \App\Events\VolunteerRejected::class,
            [\App\Listeners\SendVolunteerNotification::class, 'handleVolunteerRejected'],
        );

        Event::listen(
            \App\Events\ShiftApproved::class,
            [\App\Listeners\SendVolunteerNotification::class, 'handleShiftApproved'],
        );

        Event::listen(
            \App\Events\ShiftRejected::class,
            [\App\Listeners\SendVolunteerNotification::class, 'handleShiftRejected'],
        );

        Event::listen(
            \App\Events\VolunteerCheckedOut::class,
            \App\Listeners\ProcessAttendanceHours::class,
        );

        Event::listen(
            \App\Events\HoursApproved::class,
            [\App\Listeners\SendVolunteerNotification::class, 'handleHoursApproved'],
        );
    }
}

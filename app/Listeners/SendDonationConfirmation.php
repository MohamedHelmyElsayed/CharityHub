<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDonationConfirmation implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DonationReceived $event): void
    {
        $donation = $event->donation;
        $user = $donation->user;

        \Illuminate\Support\Facades\Log::info("Sending donation confirmation email to: {$user->email} for donation ID: {$donation->id}");
        
        // Mail::to($user->email)->send(new DonationConfirmationMail($donation));
    }
}

<?php

namespace App\Listeners;

use App\Events\DonationReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateDonationCertificate implements ShouldQueue
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
        
        \Illuminate\Support\Facades\Log::info("Generating donation certificate for donation ID: {$donation->id}");
        
        // PDF generation logic would go here
        // $pdf = Pdf::loadView('pdf.certificate', compact('donation'));
        // Storage::put("certificates/donation_{$donation->id}.pdf", $pdf->output());
    }
}

<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly Donation $donation
    ) {}

    public function handle(): void
    {
        $donation = $this->donation->fresh(['donor', 'campaign']);

        if (!$donation || !$donation->campaign) {
            return;
        }

        $donorName = $donation->anonymous
            ? 'Anonymous Donor'
            : ($donation->donor?->name ?? $donation->user?->name ?? 'Valued Donor');

        // Check if certificate already exists
        $existing = Certificate::where('donation_id', $donation->id)->first();
        if ($existing && $existing->certificate_path && Storage::exists($existing->certificate_path)) {
            return;
        }

        // Generate QR code data URL for the verification link
        $verifyUrl = route('verify.certificate', $donation->certificate_uuid);
        $qrCodeData = $this->generateQrCodeBase64($verifyUrl);

        // Generate PDF using DomPDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.certificate', [
            'donation' => $donation,
            'donorName' => $donorName,
            'campaign' => $donation->campaign,
            'verifyUrl' => $verifyUrl,
            'qrCodeData' => $qrCodeData,
            'generatedAt' => now(),
        ]);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'certificates/' . $donation->certificate_uuid . '.pdf';
        Storage::put($filename, $pdf->output());

        // Create or update Certificate record
        Certificate::updateOrCreate(
            ['donation_id' => $donation->id],
            [
                'uuid' => $donation->certificate_uuid,
                'donor_id' => $donation->donor_id,
                'donor_name' => $donorName,
                'amount' => $donation->amount,
                'campaign_title' => $donation->campaign->title,
                'certificate_path' => $filename,
                'status' => 'generated',
            ]
        );

        $donation->update([
            'certificate_path' => $filename,
            'certificate_generated_at' => now(),
        ]);

        // Log in financial audit
        \App\Models\FinancialLog::create([
            'donor_id' => $donation->donor_id,
            'donation_id' => $donation->id,
            'transaction_type' => 'certificate_generated',
            'status' => 'success',
            'metadata' => ['certificate_uuid' => $donation->certificate_uuid],
        ]);

        // Dispatch email job
        DonorEmailJob::dispatch($donation)->onQueue('emails');
    }

    private function generateQrCodeBase64(string $url): string
    {
        // Simple QR code placeholder - in production, use the simple-qrcode package
        // when ext-gd is available. Returns an SVG data URI as fallback.
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            try {
                $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(150)
                    ->generate($url);
                return 'data:image/svg+xml;base64,' . base64_encode($svg);
            } catch (\Throwable $e) {
                // fallback below
            }
        }

        // SVG fallback - a simple QR placeholder
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
             . '<rect width="100" height="100" fill="white"/>'
             . '<rect x="10" y="10" width="30" height="30" fill="black"/>'
             . '<rect x="60" y="10" width="30" height="30" fill="black"/>'
             . '<rect x="10" y="60" width="30" height="30" fill="black"/>'
             . '<rect x="15" y="15" width="20" height="20" fill="white"/>'
             . '<rect x="65" y="15" width="20" height="20" fill="white"/>'
             . '<rect x="15" y="65" width="20" height="20" fill="white"/>'
             . '</svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

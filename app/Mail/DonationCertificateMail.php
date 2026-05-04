<?php

namespace App\Mail;

use App\Models\Certificate;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DonationCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Donation $donation,
        public readonly ?Certificate $certificate
    ) {}

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Thank You for Your Donation — Your Certificate is Ready',
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.donation-certificate',
            with: [
                'donation' => $this->donation,
                'campaign' => $this->donation->campaign,
                'donorName' => $this->donation->donor?->name ?? 'Valued Donor',
                'verifyUrl' => route('verify.certificate', $this->donation->certificate_uuid),
                'certificate' => $this->certificate,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->certificate?->certificate_path) {
            $path = storage_path('app/' . $this->certificate->certificate_path);
            if (file_exists($path)) {
                $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($path)
                    ->as('CharityHub-Donation-Certificate.pdf')
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }
}

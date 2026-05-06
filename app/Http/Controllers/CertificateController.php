<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Public certificate verification endpoint.
     * GET /verify/{uuid}
     */
    public function verify(string $uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)
            ->with(['donation.campaign'])
            ->first();

        if (!$certificate) {
            abort(404, 'Certificate not found.');
        }

        return view('pages.verify-certificate', [
            'certificate' => $certificate,
            'maskedName' => $certificate->masked_donor_name,
            'campaign' => $certificate->donation?->campaign,
            'status' => $certificate->status,
            'amount' => $certificate->amount,
            'issuedAt' => $certificate->created_at,
        ]);
    }

    /**
     * Download certificate PDF.
     * GET /certificates/{uuid}/download
     */
    public function download(string $uuid)
    {
        // Fix for browser translation extensions replacing hyphens with spaces
        $uuid = str_replace(' ', '-', urldecode($uuid));

        $certificate = Certificate::where('uuid', $uuid)->firstOrFail();

        if (!$certificate->certificate_path || !\Illuminate\Support\Facades\Storage::exists($certificate->certificate_path)) {
            abort(404, 'Certificate PDF not yet generated or file not found.');
        }

        return \Illuminate\Support\Facades\Storage::download($certificate->certificate_path, 'CharityHub-Certificate-' . $uuid . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

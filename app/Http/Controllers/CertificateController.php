<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Show general certificate verification search page.
     * GET /verify
     */
    public function index(Request $request)
    {
        if ($request->has('uuid')) {
            return redirect()->route('verify.certificate', $request->uuid);
        }
        return view('pages.verify-certificate-search');
    }

    /**
     * Public certificate verification endpoint.
     * GET /verify/{uuid}
     */
    public function verify(string $uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)
            ->with(['donation.campaign'])
            ->first();

        // If certificate record is missing, try to generate it
        if (!$certificate) {
            $donation = \App\Models\Donation::where('certificate_uuid', $uuid)
                ->where('status', 'completed')
                ->first();

            if ($donation) {
                \App\Jobs\CertificateGenerationJob::dispatchSync($donation);
                $certificate = Certificate::where('uuid', $uuid)
                    ->with(['donation.campaign'])
                    ->first();
            }
        }

        if (!$certificate) {
            return redirect()->route('verify.index')->with('error', 'The certificate UUID you provided is invalid or could not be found in our auditing system.');
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

        $certificate = Certificate::where('uuid', $uuid)->first();

        // If certificate record is missing, try to find the donation and generate it
        if (!$certificate) {
            $donation = \App\Models\Donation::where('certificate_uuid', $uuid)
                ->where('status', 'completed')
                ->firstOrFail();

            // Run generation synchronously
            \App\Jobs\CertificateGenerationJob::dispatchSync($donation);
            
            $certificate = Certificate::where('uuid', $uuid)->firstOrFail();
        }

        if (!$certificate->certificate_path || !\Illuminate\Support\Facades\Storage::exists($certificate->certificate_path)) {
            // Try one more time to generate if file is missing
            $donation = \App\Models\Donation::where('certificate_uuid', $uuid)->firstOrFail();
            \App\Jobs\CertificateGenerationJob::dispatchSync($donation);
            $certificate->refresh();
            
            if (!\Illuminate\Support\Facades\Storage::exists($certificate->certificate_path)) {
                abort(404, 'Certificate PDF not yet generated or file not found.');
            }
        }

        return \Illuminate\Support\Facades\Storage::download($certificate->certificate_path, 'CharityHub-Certificate-' . $uuid . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}

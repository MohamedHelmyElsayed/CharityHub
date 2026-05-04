<?php

namespace App\Jobs;

use App\Models\ImpactReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImpactReportPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly ImpactReport $report
    ) {}

    public function handle(): void
    {
        $report = $this->report->fresh(['campaign', 'locations', 'photos']);
        if (!$report) return;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.impact-report', [
            'report' => $report,
            'campaign' => $report->campaign,
            'locations' => $report->locations,
            'photos' => $report->photos,
            'generatedAt' => now(),
        ]);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'reports/impact_report_' . $report->id . '_' . now()->format('Ymd_His') . '.pdf';
        Storage::put($filename, $pdf->output());

        $report->update([
            'pdf_path' => $filename,
            'pdf_generated_at' => now(),
        ]);
    }
}

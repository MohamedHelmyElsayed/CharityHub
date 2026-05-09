<?php

namespace App\Http\Controllers;

use App\Models\ImpactReport;
use Illuminate\Http\Request;

class ImpactReportController extends Controller
{
    public function index()
    {
        $reports = ImpactReport::published()
            ->with(['campaign', 'photos', 'locations'])
            ->orderByDesc('created_at')
            ->paginate(6);

        return view('pages.impact-reports', compact('reports'));
    }

    public function show(ImpactReport $report)
    {
        if ($report->status !== 'published') {
            abort(404);
        }

        $report->load(['campaign', 'photos', 'locations']);

        return view('pages.impact-report', compact('report'));
    }

    public function downloadPdf(ImpactReport $report)
    {
        if ($report->status !== 'published') {
            abort(404);
        }

        $report->load(['campaign', 'locations']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.impact-report-pdf', compact('report'));
        
        return $pdf->download('Impact-Report-'.$report->slug.'.pdf');
    }
}

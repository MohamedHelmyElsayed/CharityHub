<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImpactReport;
use Illuminate\Http\Request;

class ImpactReportController extends Controller
{
    public function index()
    {
        $reports = ImpactReport::with('campaign')
            ->orderByDesc('created_at')
            ->paginate(15);
            
        return view('admin.impact-reports', compact('reports'));
    }

    public function destroy(ImpactReport $report)
    {
        $report->delete();
        return redirect()->route('custom_admin.impact-reports.index')->with('success', 'Impact report deleted successfully.');
    }
}

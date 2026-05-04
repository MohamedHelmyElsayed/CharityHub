<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class DashboardController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $stats = $this->reportService->getSystemOverview();
        $recentDonations = \App\Models\Donation::with(['user', 'campaign'])->latest()->take(5)->get();
        return view('admin.dashboard', compact('stats', 'recentDonations'));
    }
}

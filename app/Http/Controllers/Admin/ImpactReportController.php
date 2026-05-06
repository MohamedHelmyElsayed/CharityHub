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

    public function create()
    {
        $campaigns = \App\Models\Campaign::active()->get();
        return view('admin.impact-report-form', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'title' => 'required|string|max:255',
            'outcomes_narrative' => 'required|string',
            'beneficiary_count' => 'required|integer|min:0',
            'funds_used' => 'required|numeric|min:0',
            'report_period' => 'required|string',
            'status' => 'required|in:draft,published',
            'locations' => 'nullable|array',
            'locations.*.name' => 'required|string|max:255',
            'locations.*.description' => 'nullable|string',
            'locations.*.beneficiaries' => 'required|integer|min:0',
            'locations.*.latitude' => 'nullable|numeric',
            'locations.*.longitude' => 'nullable|numeric',
        ]);

        $report = ImpactReport::create($validated);

        if ($request->has('locations')) {
            foreach ($request->locations as $locData) {
                $report->locations()->create($locData);
            }
        }

        return redirect()->route('custom_admin.impact-reports.index')->with('success', 'Impact report created successfully.');
    }

    public function edit($id)
    {
        $report = ImpactReport::with('locations')->findOrFail($id);
        $campaigns = \App\Models\Campaign::active()->get();
        return view('admin.impact-report-form', compact('report', 'campaigns'));
    }

    public function update(Request $request, $id)
    {
        $report = ImpactReport::findOrFail($id);
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'title' => 'required|string|max:255',
            'outcomes_narrative' => 'required|string',
            'beneficiary_count' => 'required|integer|min:0',
            'funds_used' => 'required|numeric|min:0',
            'report_period' => 'required|string',
            'status' => 'required|in:draft,published',
            'locations' => 'nullable|array',
            'locations.*.name' => 'required|string|max:255',
            'locations.*.description' => 'nullable|string',
            'locations.*.beneficiaries' => 'required|integer|min:0',
            'locations.*.latitude' => 'nullable|numeric',
            'locations.*.longitude' => 'nullable|numeric',
        ]);

        $report->update($validated);

        // Sync locations: simpler to delete and recreate for this admin UI
        $report->locations()->delete();
        if ($request->has('locations')) {
            foreach ($request->locations as $locData) {
                $report->locations()->create($locData);
            }
        }

        return redirect()->route('custom_admin.impact-reports.index')->with('success', 'Impact report updated successfully.');
    }

    public function destroy($id)
    {
        $report = ImpactReport::findOrFail($id);
        $report->delete();
        return redirect()->route('custom_admin.impact-reports.index')->with('success', 'Impact report deleted successfully.');
    }
}

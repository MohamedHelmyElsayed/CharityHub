<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CampaignService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index()
    {
        $campaigns = $this->campaignService->getAllCampaigns();
        return view('admin.campaigns', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.campaigns-form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
            'image' => 'nullable|image|max:2048',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ]);

        $this->campaignService->createCampaign($data);

        return redirect()->route('custom_admin.campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function edit($id)
    {
        $campaign = $this->campaignService->getCampaignById($id);
        return view('admin.campaigns-form', compact('campaign'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date',
            'status' => 'required|in:active,completed,paused',
            'image' => 'nullable|image|max:2048',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ]);

        $this->campaignService->updateCampaign($id, $data);

        return redirect()->route('custom_admin.campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy($id)
    {
        $this->campaignService->deleteCampaign($id);
        return redirect()->route('custom_admin.campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}

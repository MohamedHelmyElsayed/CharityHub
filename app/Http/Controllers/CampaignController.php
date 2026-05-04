<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CampaignService;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index()
    {
        $campaigns = $this->campaignService->getAllCampaigns('active');
        return view('pages.campaigns', compact('campaigns'));
    }

    public function show($id)
    {
        $campaign = $this->campaignService->getCampaignById($id);
        return view('pages.campaign-details', compact('campaign'));
    }
}

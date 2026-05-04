<?php

namespace App\Services;

use App\Models\Campaign;
use Illuminate\Support\Facades\Storage;

class CampaignService
{
    public function getAllCampaigns($status = null)
    {
        $query = Campaign::query();
        if ($status) {
            $query->where('status', $status);
        }
        return $query->latest()->get();
    }

    public function getCampaignById($id)
    {
        return Campaign::findOrFail($id);
    }

    public function createCampaign(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('campaigns', 'public');
        }

        return Campaign::create($data);
    }

    public function updateCampaign($id, array $data)
    {
        $campaign = Campaign::findOrFail($id);

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }
            $data['image'] = $data['image']->store('campaigns', 'public');
        }

        $campaign->update($data);
        return $campaign;
    }

    public function deleteCampaign($id)
    {
        $campaign = Campaign::findOrFail($id);
        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }
        return $campaign->delete();
    }

    public function updateProgress($id, $amount)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->increment('current_amount', $amount);
        
        if ($campaign->current_amount >= $campaign->goal_amount) {
            $campaign->update(['status' => 'completed']);
        }
        
        return $campaign;
    }
}

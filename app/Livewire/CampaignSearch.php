<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignSearch extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $campaigns = Campaign::active()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('short_description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('livewire.campaign-search', [
            'campaigns' => $campaigns,
        ]);
    }
}

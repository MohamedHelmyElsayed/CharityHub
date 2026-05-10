<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Donation;

class AdminDonationLedger extends Component
{
    use WithPagination;

    public $search = '';
    public $status = 'All Statuses';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Donation::with(['user', 'campaign', 'donor']);

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('idempotency_key', 'like', "%{$search}%")
                  ->orWhere('gateway_transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('donor', fn($dq) => $dq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->status) && $this->status !== 'All Statuses') {
            $query->where('status', strtolower($this->status));
        }

        return view('livewire.admin-donation-ledger', [
            'donations' => $query->latest()->paginate(20),
        ]);
    }
}

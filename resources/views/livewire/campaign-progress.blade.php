<div wire:poll.2s="refreshData">
    @if($campaign)
    <div class="space-y-4">
        <div class="text-center">
            <div class="text-3xl font-bold text-blue-600">EGP {{ number_format($campaign->current_amount, 0) }}</div>
            <div class="text-gray-400 text-sm">raised of EGP {{ number_format($campaign->goal_amount, 0) }} goal</div>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-blue-500 to-sky-500 rounded-full transition-all duration-1000 ease-out"
                 style="width: {{ $campaign->progress_percentage }}%"></div>
        </div>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div>
                <div class="text-lg font-bold text-gray-800">{{ $campaign->progress_percentage }}%</div>
                <div class="text-xs text-gray-400">Funded</div>
            </div>
            <div>
                <div class="text-lg font-bold text-gray-800">{{ $campaign->donor_count }}</div>
                <div class="text-xs text-gray-400">Donors</div>
            </div>
            <div>
                @if($campaign->days_remaining !== null)
                    <div class="text-lg font-bold {{ $campaign->days_remaining < 7 ? 'text-red-500' : 'text-gray-800' }}">
                        {{ $campaign->days_remaining }}
                    </div>
                    <div class="text-xs text-gray-400">Days Left</div>
                @else
                    <div class="text-lg font-bold text-gray-800">∞</div>
                    <div class="text-xs text-gray-400">No Deadline</div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

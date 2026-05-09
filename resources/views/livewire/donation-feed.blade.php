<div wire:poll.3s="loadDonations">
    @if(count($donations) > 0)
        <ul class="space-y-3">
            @foreach($donations as $donation)
            <li class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 hover:bg-emerald-50 transition-colors">
                <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 text-emerald-700 font-bold text-sm">
                    {{ strtoupper(substr($donation['name'], 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-800 text-sm truncate">{{ $donation['name'] }}</span>
                        <span class="text-emerald-600 font-bold text-sm ml-2 flex-shrink-0">${{ number_format($donation['amount'], 0) }}</span>
                    </div>
                    @if($donation['message'])
                        <p class="text-gray-500 text-xs mt-0.5 italic truncate">"{{ $donation['message'] }}"</p>
                    @endif
                    <div class="text-gray-400 text-xs mt-0.5">{{ $donation['time'] }}</div>
                </div>
            </li>
            @endforeach
        </ul>
    @else
        <div class="text-center py-8 text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <p class="text-sm">Be the first to donate!</p>
        </div>
    @endif
</div>

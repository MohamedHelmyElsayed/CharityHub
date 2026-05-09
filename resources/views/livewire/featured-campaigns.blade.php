<div wire:poll.10s class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
    @forelse($campaigns as $campaign)
        @include('components.campaign-card', ['campaign' => $campaign])
    @empty
        <div class="col-span-3 text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
            <p class="text-lg font-medium text-slate-500">No active campaigns at the moment. Check back soon!</p>
        </div>
    @endforelse
</div>

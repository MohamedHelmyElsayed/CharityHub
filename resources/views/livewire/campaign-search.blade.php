<div wire:poll.10s>
    {{-- Search Input --}}
    <div class="mb-12 relative max-w-lg">
        <div class="absolute top-1/2 -translate-y-1/2 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input wire:model.live.debounce.300ms="search" 
               type="text" 
               class="block w-full pl-12 pr-11 py-3 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm transition-all text-sm" 
               placeholder="Search campaigns by title or description...">
        
        @if($search)
            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>


    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @forelse($campaigns as $campaign)
            @include('components.campaign-card', ['campaign' => $campaign])
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-32 bg-white rounded-[2.5rem] border border-dashed border-slate-200">
                <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-slate-100">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900 mb-2">No campaigns found</h3>
                <p class="text-slate-500 font-medium max-w-sm mx-auto">We couldn't find any campaigns matching "{{ $search }}". Try using different keywords.</p>
                @if($search)
                    <button wire:click="$set('search', '')" class="mt-8 text-blue-600 font-bold hover:underline">Clear search and view all</button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-16">
        {{ $campaigns->links() }}
    </div>
</div>

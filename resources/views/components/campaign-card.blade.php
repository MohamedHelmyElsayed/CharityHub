@props(['campaign'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col h-full group">
    <a href="{{ route('campaigns.show', $campaign->id) }}" class="block relative h-56 overflow-hidden">
        <img src="{{ $campaign->image ? asset('storage/' . $campaign->image) : 'https://images.unsplash.com/photo-1541888001694-0f36792cdba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-in-out">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent opacity-60"></div>
        <div class="absolute bottom-3 left-4">
            <span class="px-2.5 py-1 text-xs font-bold bg-white/90 text-gray-900 rounded-lg shadow backdrop-blur-sm">{{ ucfirst($campaign->status) }}</span>
        </div>
    </a>
    <div class="p-6 flex-grow flex flex-col">
        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
            <a href="{{ route('campaigns.show', $campaign->id) }}" class="hover:text-primary-600 transition">{{ $campaign->title }}</a>
        </h3>
        <p class="text-gray-500 text-sm mb-6 line-clamp-3 flex-grow leading-relaxed">
            {{ $campaign['description'] ?? 'Support this amazing cause and help us reach our goal. Every contribution makes a meaningful difference in the community.' }}
        </p>
        
        <div class="mt-auto">
            <x-progress-bar :goal="$campaign->goal_amount" :raised="$campaign->current_amount" />
            <a href="{{ route('donate') }}?campaign={{ $campaign->id }}" class="mt-5 w-full flex items-center justify-center px-4 py-2 border border-primary-100 text-sm font-semibold rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-600 hover:text-white transition duration-200">
                Support Cause
            </a>
        </div>
    </div>
</div>

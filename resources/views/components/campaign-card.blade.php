<div class="bg-white rounded-3xl border border-slate-200 overflow-hidden group hover:border-blue-200 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 flex flex-col h-full">
    {{-- Cover Image --}}
    <div class="relative overflow-hidden h-56 bg-slate-100">
        @if($campaign->cover_image)
            <img src="{{ Storage::url($campaign->cover_image) }}" alt="{{ $campaign->title }}"
                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <div class="absolute top-4 right-4 flex gap-2">
            @if($campaign->featured)
                <span class="px-3 py-1.5 bg-white/90 backdrop-blur-md text-amber-600 text-xs font-bold rounded-xl shadow-sm border border-white/20">
                    Featured
                </span>
            @endif
            
            @if($campaign->status === 'active')
                <span class="px-3 py-1.5 bg-blue-600/90 backdrop-blur-md text-white text-xs font-bold rounded-xl shadow-sm border border-blue-500/50">Active</span>
            @elseif($campaign->status === 'ended')
                <span class="px-3 py-1.5 bg-emerald-600/90 backdrop-blur-md text-white text-xs font-bold rounded-xl shadow-sm border border-emerald-500/50">Goal Reached</span>
            @else
                <span class="px-3 py-1.5 bg-slate-100/90 backdrop-blur-md text-slate-600 text-xs font-bold rounded-xl shadow-sm border border-slate-200/50">Draft</span>
            @endif
        </div>
    </div>

    {{-- Content --}}
    <div class="p-6 flex flex-col flex-grow">
        <h3 class="text-xl font-bold text-slate-900 mb-2 line-clamp-2 leading-tight">
            <a href="{{ route('campaigns.show', $campaign->slug ?? $campaign->id) }}" class="hover:text-blue-600 transition-colors">
                {{ $campaign->title }}
            </a>
        </h3>

        @if($campaign->short_description)
            <p class="text-slate-500 text-sm mb-6 line-clamp-2 font-medium leading-relaxed">{{ $campaign->short_description }}</p>
        @endif

        <div class="mt-auto">
            {{-- Progress Bar --}}
            <div class="mb-5">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <span class="text-lg font-extrabold text-blue-600">EGP {{ number_format($campaign->current_amount, 0) }}</span>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide ml-1">Raised</span>
                    </div>
                    <span class="text-sm font-bold text-slate-700">{{ $campaign->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-1000 ease-out"
                         style="width: {{ $campaign->progress_percentage }}%"></div>
                </div>
                <div class="flex justify-between text-xs font-medium text-slate-400 mt-2">
                    <span>Goal: EGP {{ number_format($campaign->goal_amount, 0) }}</span>
                    @if($campaign->days_remaining !== null)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $campaign->days_remaining > 0 ? $campaign->days_remaining . ' days left' : 'Ended' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="pt-5 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $campaign->donor_count }} <span class="hidden sm:inline">donors</span>
                </div>
                <a href="{{ route('campaigns.show', $campaign->slug ?? $campaign->id) }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-blue-600 transition-colors duration-300">
                    {{ $campaign->status === 'ended' ? 'View Details' : 'Donate' }}
                </a>
            </div>
        </div>
    </div>
</div>

@extends('layouts.app')

@section('title', 'Impact Reports — Our Transparency')

@section('content')
<div class="min-h-screen bg-slate-50 py-16 lg:py-24 relative overflow-hidden">
    {{-- Background decorations --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-gradient-to-br from-blue-100/50 to-transparent blur-3xl opacity-60 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-gradient-to-tr from-indigo-100/40 to-transparent blur-3xl opacity-50 rounded-full translate-y-1/3 -translate-x-1/4"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        {{-- Header Section --}}
        <div class="text-center max-w-3xl mx-auto mb-20">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full text-blue-600 text-sm font-bold tracking-wide mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Our Accountability
            </span>
            <h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                Where every dollar <br><span class="text-blue-600">creates a story.</span>
            </h1>
            <p class="text-lg text-slate-500 font-medium leading-relaxed">
                Transparency is at the heart of everything we do. Explore our verified impact reports to see how your contributions are making a tangible difference across the globe.
            </p>
        </div>

        {{-- Reports Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            @forelse($reports as $report)
            <div class="group bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 hover:-translate-y-2 flex flex-col">
                {{-- Card Image --}}
                <div class="relative aspect-[16/10] overflow-hidden">
                    @if($report->photos->count() > 0)
                        <img src="{{ Storage::url($report->photos->first()->path) }}" alt="{{ $report->title }}" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                    @else
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>
                    
                    <div class="absolute top-6 right-6">
                        <span class="px-4 py-2 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-white text-xs font-bold uppercase tracking-wider">
                            {{ $report->report_period ?? 'Final Report' }}
                        </span>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-8 flex-grow flex flex-col">
                    <div class="mb-4">
                        <span class="text-blue-600 font-bold text-xs uppercase tracking-widest mb-2 block">{{ $report->campaign->title }}</span>
                        <h2 class="text-2xl font-bold text-slate-900 leading-snug group-hover:text-blue-600 transition-colors">
                            {{ $report->title }}
                        </h2>
                    </div>

                    <p class="text-slate-500 font-medium text-sm line-clamp-3 mb-6 flex-grow">
                        {{ \Illuminate\Support\Str::limit(strip_tags($report->outcomes_narrative), 120) }}
                    </p>

                    {{-- Mini Stats --}}
                    <div class="grid grid-cols-2 gap-4 pt-6 border-t border-slate-50 mb-8">
                        <div>
                            <span class="block text-xl font-extrabold text-slate-900 tracking-tight">{{ number_format($report->beneficiary_count) }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Beneficiaries</span>
                        </div>
                        <div>
                            <span class="block text-xl font-extrabold text-slate-900 tracking-tight">${{ number_format($report->funds_used, 0) }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Funds Used</span>
                        </div>
                    </div>

                    <a href="{{ route('impact.show', $report) }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-4 bg-slate-900 text-white rounded-2xl hover:bg-blue-600 transition-all duration-300 font-bold shadow-lg shadow-slate-900/10 hover:shadow-blue-500/25">
                        Read Full Story
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border border-dashed border-slate-200">
                <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-300 border border-slate-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">No Reports Published Yet</h3>
                <p class="text-slate-500 font-medium">We are currently verifying our latest impacts. Please check back soon.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($reports->hasPages())
        <div class="flex justify-center">
            <div class="bg-white px-6 py-4 rounded-3xl shadow-sm border border-slate-100">
                {{ $reports->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

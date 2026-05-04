@extends('layouts.app')

@section('title', $report->title . ' — Impact Report')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
    {{-- Header --}}
    <div class="bg-slate-900 rounded-[2rem] p-10 lg:p-16 text-white mb-12 shadow-2xl shadow-slate-900/20 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-blue-200 text-sm font-bold tracking-wide mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Verified Impact Report
            </div>
            <h1 class="text-4xl lg:text-6xl font-extrabold mb-4 tracking-tight leading-tight">{{ $report->title }}</h1>
            <p class="text-blue-100 text-xl font-medium max-w-2xl">Campaign: <strong class="text-white">{{ $report->campaign->title }}</strong></p>
            @if($report->report_period)
                <p class="text-slate-400 text-sm mt-3 font-semibold uppercase tracking-wider">Period: {{ $report->report_period }}</p>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
            <div class="text-6xl font-extrabold text-slate-900 mb-2 tracking-tight">{{ number_format($report->beneficiary_count) }}</div>
            <div class="text-blue-600 font-bold uppercase tracking-wider text-sm">Beneficiaries Reached</div>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
            <div class="text-6xl font-extrabold text-slate-900 mb-2 tracking-tight">${{ number_format($report->funds_used, 0) }}</div>
            <div class="text-blue-600 font-bold uppercase tracking-wider text-sm">Funds Utilized</div>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
            <div class="text-6xl font-extrabold text-slate-900 mb-2 tracking-tight">{{ $report->locations->count() }}</div>
            <div class="text-blue-600 font-bold uppercase tracking-wider text-sm">Locations Served</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
        {{-- Narrative --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 lg:p-10">
            <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                Outcomes & Impact
            </h2>
            <div class="prose prose-lg text-slate-600 font-medium leading-relaxed">
                {!! nl2br(e($report->outcomes_narrative)) !!}
            </div>

            @if($report->pdf_path)
            <div class="mt-8 pt-8 border-t border-slate-100">
                <a href="{{ Storage::url($report->pdf_path) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3.5 bg-slate-900 text-white rounded-xl hover:bg-blue-600 transition-colors duration-300 font-bold shadow-lg shadow-slate-900/10 hover:shadow-blue-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Official PDF Report
                </a>
            </div>
            @endif
        </div>

        {{-- Google Map --}}
        @if($report->locations->count() > 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 lg:p-10 flex flex-col h-full">
            <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                Beneficiary Locations
            </h2>
            <div id="impact-map" class="w-full h-[300px] rounded-2xl bg-slate-100 mb-6 border border-slate-200"></div>
            <ul class="space-y-3 flex-grow">
                @foreach($report->locations as $loc)
                <li class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition-colors">
                    <div>
                        <div class="font-bold text-slate-900">{{ $loc->name }}</div>
                        @if($loc->description)<div class="text-sm font-medium text-slate-500 mt-0.5">{{ $loc->description }}</div>@endif
                    </div>
                    <div class="text-right">
                        <span class="block text-lg font-extrabold text-blue-600">{{ number_format($loc->beneficiaries) }}</span>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Helped</span>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- Photo Gallery --}}
    @if($report->photos->count() > 0)
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 lg:p-10">
        <h2 class="text-2xl font-bold text-slate-900 mb-8 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            Photo Gallery
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($report->photos as $photo)
            <div class="group relative overflow-hidden rounded-2xl aspect-square border border-slate-100 shadow-sm cursor-pointer">
                <img src="{{ Storage::url($photo->path) }}" alt="{{ $photo->caption ?? 'Impact photo' }}"
                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                @if($photo->caption)
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                    <p class="text-white font-medium text-sm">{{ $photo->caption }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if($report->locations->count() > 0 && config('services.google_maps.key'))
@push('scripts')
<script>
@php
    $mapLocations = $report->locations->map(fn($l) => [
        'lat' => (float)$l->latitude,
        'lng' => (float)$l->longitude,
        'name' => $l->name,
        'count' => $l->beneficiaries
    ])->values()->all();
@endphp
function initMap() {
    const locations = @json($mapLocations);

    if (locations.length === 0) return;

    const map = new google.maps.Map(document.getElementById('impact-map'), {
        zoom: 6,
        center: { lat: locations[0].lat, lng: locations[0].lng },
        styles: [
            { featureType: 'all', elementType: 'geometry.fill', stylers: [{ color: '#f8fafc' }] },
            { featureType: 'water', elementType: 'geometry.fill', stylers: [{ color: '#e2e8f0' }] }
        ],
        disableDefaultUI: true,
        zoomControl: true,
    });

    const infoWindow = new google.maps.InfoWindow();

    locations.forEach(loc => {
        const marker = new google.maps.Marker({
            position: { lat: loc.lat, lng: loc.lng },
            map: map,
            title: loc.name,
            icon: { 
                path: google.maps.SymbolPath.CIRCLE, 
                scale: 10, 
                fillColor: '#2563eb', 
                fillOpacity: 0.9, 
                strokeColor: '#ffffff', 
                strokeWeight: 2.5 
            }
        });

        marker.addListener('click', () => {
            infoWindow.setContent(`
                <div style="padding: 4px; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <strong style="color: #0f172a; font-size: 14px;">${loc.name}</strong><br>
                    <span style="color: #2563eb; font-weight: bold;">${loc.count}</span> 
                    <span style="color: #64748b; font-size: 12px;">beneficiaries</span>
                </div>
            `);
            infoWindow.open(map, marker);
        });
    });
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"></script>
@endpush
@endif
@endsection

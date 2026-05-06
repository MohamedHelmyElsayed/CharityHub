@extends('layouts.app')

@section('title', (isset($report) ? 'Edit' : 'Create') . ' Impact Report — Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ isset($report) ? 'Edit' : 'Create New' }} Impact Report</h1>
            <p class="mt-2 text-gray-600">Showcase your success with detailed metrics and location data.</p>
        </div>
        <a href="{{ route('custom_admin.impact-reports.index') }}" class="text-sm font-bold text-gray-400 hover:text-blue-600 transition flex items-center gap-2 mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
    </div>

    <form action="{{ isset($report) ? route('custom_admin.impact-reports.update', $report->id) : route('custom_admin.impact-reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($report)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Main Data --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 lg:p-10">
                    <h2 class="text-xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        General Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Report Title</label>
                            <input type="text" name="title" required value="{{ old('title', $report->title ?? '') }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none"
                                   placeholder="e.g. Water Wells Q1 2025 Impact Report">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Campaign</label>
                            <select name="campaign_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                                <option value="">Select campaign...</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" {{ (isset($report) && $report->campaign_id == $campaign->id) ? 'selected' : '' }}>
                                        {{ $campaign->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Report Period</label>
                            <input type="text" name="report_period" required value="{{ old('report_period', $report->report_period ?? '') }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none"
                                   placeholder="e.g. Q1 2025">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Beneficiaries Reached</label>
                            <input type="number" name="beneficiary_count" required value="{{ old('beneficiary_count', $report->beneficiary_count ?? 0) }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Funds Utilized ($)</label>
                            <input type="number" step="0.01" name="funds_used" required value="{{ old('funds_used', $report->funds_used ?? 0) }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                        </div>
                    </div>
                </div>

                {{-- Narrative --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8 lg:p-10">
                    <h2 class="text-xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        Impact Narrative
                    </h2>
                    <textarea name="outcomes_narrative" rows="8" required
                              class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none resize-none"
                              placeholder="Describe the detailed outcomes...">{{ old('outcomes_narrative', $report->outcomes_narrative ?? '') }}</textarea>
                </div>
            </div>

            {{-- Right Column: Locations & Status --}}
            <div class="space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-xl font-bold text-gray-900">Locations</h2>
                        <button type="button" id="add-location" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    <div id="locations-container" class="space-y-4 mb-8">
                        @if(isset($report))
                            @foreach($report->locations as $index => $location)
                                <div class="location-row bg-gray-50 rounded-2xl border border-gray-100 p-5 relative group transition-all">
                                    <div class="space-y-3">
                                        <div class="relative search-wrapper">
                                            <input type="text" name="locations[{{ $index }}][name]" required value="{{ $location->name }}" 
                                                   class="location-name w-full px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none" 
                                                   placeholder="Search location...">
                                            <div class="suggestions-list absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-1 hidden border border-gray-100 overflow-hidden"></div>
                                        </div>
                                        <div class="flex gap-3">
                                            <input type="number" name="locations[{{ $index }}][beneficiaries]" required value="{{ $location->beneficiaries }}" 
                                                   class="w-24 px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm outline-none" placeholder="Count">
                                            <input type="text" name="locations[{{ $index }}][description]" value="{{ $location->description }}" 
                                                   class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm outline-none" placeholder="Description">
                                        </div>
                                        <input type="hidden" name="locations[{{ $index }}][latitude]" value="{{ $location->latitude }}" class="lat-input">
                                        <input type="hidden" name="locations[{{ $index }}][longitude]" value="{{ $location->longitude }}" class="lng-input">
                                    </div>
                                    <button type="button" class="remove-location absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="rounded-3xl border border-gray-100 overflow-hidden shadow-inner">
                        <div id="admin-map" style="height: 300px; width: 100%; background: #f8fafc;"></div>
                    </div>
                </div>

                {{-- Status & Save --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-8">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Report Status</label>
                    <select name="status" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none mb-6">
                        <option value="draft" {{ (isset($report) && $report->status == 'draft') ? 'selected' : '' }}>Draft Mode</option>
                        <option value="published" {{ (isset($report) && $report->status == 'published') ? 'selected' : '' }}>Published</option>
                    </select>

                    <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-2xl font-bold hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 transform hover:-translate-y-1">
                        {{ isset($report) ? 'Save Changes' : 'Publish Report' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .location-row.active { border-color: #3b82f6 !important; background-color: #f0f9ff !important; }
    .suggestion-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 13px; font-weight: 500; color: #475569; }
    .suggestion-item:last-child { border-bottom: none; }
    .suggestion-item:hover { background-color: #f8fafc; color: #2563eb; }
</style>
@endpush

@push('scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initAdminMap"></script>
<script>
    let map, marker, activeRow = null;

    function initAdminMap() {
        map = new google.maps.Map(document.getElementById('admin-map'), {
            center: { lat: 20, lng: 0 }, zoom: 2, disableDefaultUI: true, zoomControl: true,
        });
        marker = new google.maps.Marker({ map: map, visible: false, draggable: true });
        map.addListener('click', (e) => { if (activeRow) updateLocation(e.latLng); });
        marker.addListener('dragend', (e) => { if (activeRow) updateLocation(e.latLng); });
        document.querySelectorAll('.location-row').forEach(setupRow);
    }

    function updateLocation(latLng) {
        marker.setPosition(latLng);
        marker.setVisible(true);
        activeRow.querySelector('.lat-input').value = latLng.lat().toFixed(8);
        activeRow.querySelector('.lng-input').value = latLng.lng().toFixed(8);
    }

    function setupRow(row) {
        const input = row.querySelector('.location-name');
        const list = row.querySelector('.suggestions-list');
        let timeout = null;

        input.addEventListener('focus', () => {
            activeRow = row;
            document.querySelectorAll('.location-row').forEach(r => r.classList.remove('active'));
            row.classList.add('active');
            
            const lat = row.querySelector('.lat-input').value;
            const lng = row.querySelector('.lng-input').value;
            if (lat && lng) {
                const pos = { lat: parseFloat(lat), lng: parseFloat(lng) };
                marker.setPosition(pos);
                marker.setVisible(true);
                map.setCenter(pos);
                map.setZoom(10);
            } else {
                marker.setVisible(false);
            }
        });

        input.addEventListener('input', () => {
            clearTimeout(timeout);
            const query = input.value;
            if (query.length < 3) { list.classList.add('hidden'); return; }
            
            timeout = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
                    .then(res => res.json())
                    .then(data => {
                        list.innerHTML = '';
                        if (data.length > 0) {
                            list.classList.remove('hidden');
                            data.forEach(item => {
                                const el = document.createElement('div');
                                el.className = 'suggestion-item';
                                el.textContent = item.display_name;
                                el.onclick = () => {
                                    input.value = item.display_name;
                                    const pos = { lat: parseFloat(item.lat), lng: parseFloat(item.lon) };
                                    map.setCenter(pos);
                                    map.setZoom(12);
                                    updateLocation(new google.maps.LatLng(pos.lat, pos.lng));
                                    list.classList.add('hidden');
                                };
                                list.appendChild(el);
                            });
                        } else {
                            list.classList.add('hidden');
                        }
                    });
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!row.contains(e.target)) list.classList.add('hidden');
        });
    }

    document.getElementById('add-location').addEventListener('click', () => {
        const container = document.getElementById('locations-container');
        const index = container.querySelectorAll('.location-row').length;
        const row = document.createElement('div');
        row.className = 'location-row bg-gray-50 rounded-2xl border border-gray-100 p-5 relative group transition-all';
        row.innerHTML = `
            <div class="space-y-3">
                <div class="relative search-wrapper">
                    <input type="text" name="locations[${index}][name]" required class="location-name w-full px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Search location...">
                    <div class="suggestions-list absolute z-50 w-full bg-white shadow-2xl rounded-xl mt-1 hidden border border-gray-100 overflow-hidden"></div>
                </div>
                <div class="flex gap-3">
                    <input type="number" name="locations[${index}][beneficiaries]" required value="0" class="w-24 px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm outline-none" placeholder="Count">
                    <input type="text" name="locations[${index}][description]" class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl text-sm outline-none" placeholder="Description">
                    <input type="hidden" name="locations[${index}][latitude]" class="lat-input">
                    <input type="hidden" name="locations[${index}][longitude]" class="lng-input">
                </div>
            </div>
            <button type="button" class="remove-location absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;
        container.appendChild(row);
        setupRow(row);
        row.querySelector('.location-name').focus();
    });
</script>
@endpush
@endsection

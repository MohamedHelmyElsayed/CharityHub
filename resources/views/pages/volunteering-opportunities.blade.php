@extends('layouts.app')
@section('title', 'Volunteering Opportunities')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Inter',sans-serif}
.opp-card{transition:transform .25s ease,box-shadow .25s ease}
.opp-card:hover{transform:translateY(-6px);box-shadow:0 20px 60px rgba(0,0,0,.12)}
.cat-badge{font-size:.65rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;padding:.25rem .65rem;border-radius:999px}
.search-input:focus{outline:none;box-shadow:0 0 0 3px rgba(124,58,237,.18)}
.filter-btn.active{background:#7c3aed;color:#fff}
.filter-btn{transition:all .18s}
.hero-gradient{background:linear-gradient(135deg,#1e1b4b 0%,#312e81 40%,#1e40af 100%)}
</style>

{{-- ── HERO ────────────────────────────────────────────────────────────── --}}
<div class="hero-gradient text-white py-20 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10"
         style="background-image:radial-gradient(circle at 20% 80%, #a78bfa 0%, transparent 50%), radial-gradient(circle at 80% 20%, #60a5fa 0%, transparent 50%)"></div>
    <div class="relative max-w-5xl mx-auto text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6"
              style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2)">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
            Make a Difference
        </span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black mb-5 leading-tight">
            Volunteering<br><span class="text-violet-300">Opportunities</span>
        </h1>
        <p class="text-lg text-indigo-100 max-w-2xl mx-auto leading-relaxed">
            Join hands with our community. Explore opportunities to contribute your skills, time, and passion for causes that matter.
        </p>
        <div class="mt-10 max-w-xl mx-auto relative">
            <input id="searchInput" type="text" placeholder="Search opportunities, locations, skills…"
                   class="search-input w-full px-6 py-4 pr-14 rounded-2xl text-slate-900 font-medium text-sm shadow-xl"
                   style="background:rgba(255,255,255,.97)">
            <svg class="absolute right-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── FILTERS ─────────────────────────────────────────────────────────── --}}
<div class="bg-white border-b border-slate-100 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center gap-2 overflow-x-auto">
        <button class="filter-btn active px-4 py-1.5 rounded-full text-sm font-semibold border border-violet-200 bg-violet-600 text-white"
                data-filter="all">All</button>
        @foreach($categories as $cat)
        <button class="filter-btn px-4 py-1.5 rounded-full text-sm font-semibold border border-slate-200 text-slate-600 hover:bg-violet-50 hover:border-violet-300"
                data-filter="{{ $cat }}">{{ ucfirst($cat) }}</button>
        @endforeach
        <div class="ml-auto flex items-center gap-2">
            <span class="text-xs text-slate-400 font-medium" id="resultCount">{{ $opportunities->count() }} opportunities</span>
        </div>
    </div>
</div>

{{-- ── MAIN GRID ───────────────────────────────────────────────────────── --}}
<div class="min-h-screen" style="background:linear-gradient(180deg,#f8fafc 0%,#ede9fe11 100%)">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if(session('success'))
    <div class="mb-6 p-4 rounded-2xl flex gap-3 items-start" style="background:#d1fae5;border:1px solid #6ee7b7">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    @forelse($opportunities as $opp)
    @php
        $catColors = [
            'general'     => ['#ede9fe','#7c3aed'],
            'fundraising' => ['#fef3c7','#d97706'],
            'cleanup'     => ['#d1fae5','#059669'],
            'education'   => ['#dbeafe','#2563eb'],
            'medical'     => ['#fee2e2','#dc2626'],
            'food'        => ['#ffedd5','#ea580c'],
            'community'   => ['#f0fdf4','#16a34a'],
        ];
        $colors = $catColors[$opp->category ?? 'general'] ?? ['#ede9fe','#7c3aed'];
        $statusMap = [
            'open'      => ['#d1fae5','#065f46','Open'],
            'full'      => ['#fee2e2','#991b1b','Full'],
            'completed' => ['#f1f5f9','#475569','Completed'],
            'cancelled' => ['#f1f5f9','#475569','Cancelled'],
            'draft'     => ['#fef3c7','#92400e','Coming Soon'],
        ];
        $st = $statusMap[$opp->status] ?? ['#f1f5f9','#475569', ucfirst($opp->status)];
        $totalVolunteers = $opp->shifts->sum('required_volunteers');
        $assignedCount   = $opp->shifts->sum('assigned_count');
        $spotsLeft       = max(0, $totalVolunteers - $assignedCount);
    @endphp

    <div class="opp-card bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm mb-6 opportunity-item"
         data-category="{{ $opp->category }}"
         data-title="{{ strtolower($opp->title) }}"
         data-location="{{ strtolower($opp->location) }}">
        <div class="flex flex-col md:flex-row">
            {{-- Image --}}
            <div class="md:w-72 flex-shrink-0 relative overflow-hidden bg-slate-100" style="min-height:200px">
                @if($opp->cover_image || $opp->banner_image)
                    <img src="{{ asset('storage/' . ($opp->banner_image ?? $opp->cover_image)) }}"
                         alt="{{ $opp->title }}"
                         class="w-full h-full object-cover absolute inset-0">
                @else
                    <div class="absolute inset-0 flex items-center justify-center"
                         style="background:linear-gradient(135deg,{{ $colors[0] }},{{ $colors[1] }}22)">
                        <svg class="w-16 h-16 opacity-30" style="color:{{ $colors[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                @endif
                {{-- Category badge overlay --}}
                <div class="absolute top-3 left-3">
                    <span class="cat-badge" style="background:{{ $colors[0] }};color:{{ $colors[1] }}">
                        {{ ucfirst($opp->category ?? $opp->event_type ?? 'General') }}
                    </span>
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h2 class="text-xl font-extrabold text-slate-900 leading-tight">{{ $opp->title }}</h2>
                        <span class="flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background:{{ $st[0] }};color:{{ $st[1] }}">{{ $st[2] }}</span>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4">{{ Str::limit($opp->description, 150) }}</p>

                    {{-- Meta row --}}
                    <div class="flex flex-wrap gap-4 text-xs text-slate-500 font-medium">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $opp->location }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $opp->start_date->format('M d') }} – {{ $opp->end_date->format('M d, Y') }}
                        </span>
                        @if($totalVolunteers > 0)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $spotsLeft }} / {{ $totalVolunteers }} spots left
                        </span>
                        @endif
                        @if($opp->registration_deadline)
                        <span class="flex items-center gap-1.5 {{ now()->gt($opp->registration_deadline) ? 'text-red-400' : 'text-amber-500' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ now()->gt($opp->registration_deadline) ? 'Deadline passed' : 'Deadline: '.$opp->registration_deadline->format('M d') }}
                        </span>
                        @endif
                    </div>

                    {{-- Skills --}}
                    @if(!empty($opp->required_skills))
                    <div class="flex flex-wrap gap-1.5 mt-4">
                        @foreach(array_slice($opp->required_skills, 0, 4) as $skill)
                        <span class="px-2 py-0.5 rounded-lg text-xs font-medium" style="background:#f1f5f9;color:#64748b">{{ $skill }}</span>
                        @endforeach
                        @if(count($opp->required_skills) > 4)
                        <span class="px-2 py-0.5 rounded-lg text-xs font-medium text-slate-400">+{{ count($opp->required_skills) - 4 }} more</span>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-50">
                    @if($opp->campaign)
                    <span class="text-xs text-slate-400">
                        Part of: <span class="font-semibold text-slate-600">{{ $opp->campaign->title }}</span>
                    </span>
                    @else
                    <span></span>
                    @endif
                    <a href="{{ route('volunteering.show', $opp->slug) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition hover:opacity-90 hover:shadow-lg"
                       style="background:linear-gradient(135deg,#7c3aed,#2563eb)">
                        View Details
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-24">
        <div class="w-24 h-24 rounded-3xl mx-auto mb-6 flex items-center justify-center" style="background:#ede9fe">
            <svg class="w-12 h-12 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-700 mb-2">No opportunities yet</h3>
        <p class="text-slate-400">Check back soon — new opportunities are added regularly.</p>
    </div>
    @endforelse

    <div id="noResults" class="hidden text-center py-24">
        <p class="text-slate-400 font-medium">No opportunities match your search.</p>
    </div>
</div>
</div>

<script>
const items       = document.querySelectorAll('.opportunity-item');
const searchInput = document.getElementById('searchInput');
const filterBtns  = document.querySelectorAll('.filter-btn');
const countEl     = document.getElementById('resultCount');
const noResults   = document.getElementById('noResults');
let activeFilter  = 'all';
let searchTerm    = '';

function applyFilters() {
    let visible = 0;
    items.forEach(item => {
        const matchCat    = activeFilter === 'all' || item.dataset.category === activeFilter;
        const matchSearch = !searchTerm ||
            item.dataset.title.includes(searchTerm) ||
            item.dataset.location.includes(searchTerm);
        const show = matchCat && matchSearch;
        item.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    countEl.textContent = visible + ' opportunit' + (visible === 1 ? 'y' : 'ies');
    noResults.classList.toggle('hidden', visible > 0);
}

filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeFilter = btn.dataset.filter;
        applyFilters();
    });
});

searchInput.addEventListener('input', e => {
    searchTerm = e.target.value.toLowerCase().trim();
    applyFilters();
});
</script>
@endsection

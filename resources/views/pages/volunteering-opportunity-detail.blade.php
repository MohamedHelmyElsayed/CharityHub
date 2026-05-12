@extends('layouts.app')
@section('title', $opportunity->title . ' — Volunteering Opportunity')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
*{font-family:'Inter',sans-serif}
.hero-overlay{background:linear-gradient(to bottom,rgba(15,10,40,.55) 0%,rgba(15,10,40,.85) 100%)}
.info-card{border-radius:1.25rem;border:1px solid #e2e8f0;background:#fff;padding:1.5rem}
.skill-tag{display:inline-flex;align-items:center;padding:.3rem .75rem;border-radius:999px;font-size:.72rem;font-weight:700;letter-spacing:.03em}
.section-title{font-size:1.1rem;font-weight:800;color:#1e293b;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem}
.section-title::before{content:'';display:inline-block;width:4px;height:1.1rem;border-radius:999px;background:linear-gradient(135deg,#7c3aed,#2563eb)}
</style>

{{-- ── HERO BANNER ─────────────────────────────────────────────────────── --}}
<div class="relative h-64 sm:h-80 lg:h-96 overflow-hidden">
    @if($opportunity->cover_image || $opportunity->banner_image)
        <img src="{{ asset('storage/' . ($opportunity->banner_image ?? $opportunity->cover_image)) }}"
             alt="{{ $opportunity->title }}"
             class="absolute inset-0 w-full h-full object-cover">
    @else
        <div class="absolute inset-0" style="background:linear-gradient(135deg,#1e1b4b,#312e81,#1e40af)"></div>
    @endif
    <div class="hero-overlay absolute inset-0"></div>
    <div class="relative h-full flex flex-col justify-end px-6 pb-8 max-w-5xl mx-auto">
        <a href="{{ route('volunteering.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/70 hover:text-white mb-4 transition w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            All Opportunities
        </a>
        <div class="flex flex-wrap gap-2 mb-3">
            <span class="px-3 py-1 rounded-full text-xs font-bold"
                  style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.25)">
                {{ ucfirst($opportunity->category ?? $opportunity->event_type ?? 'General') }}
            </span>
            @php
                $stMap = ['open'=>['#d1fae5','#065f46','Open'],'full'=>['#fee2e2','#991b1b','Full'],'completed'=>['#f1f5f9','#475569','Completed'],'cancelled'=>['#f1f5f9','#475569','Cancelled'],'draft'=>['#fef3c7','#92400e','Coming Soon']];
                $st = $stMap[$opportunity->status] ?? ['#f1f5f9','#475569',ucfirst($opportunity->status)];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-bold"
                  style="background:{{ $st[0] }};color:{{ $st[1] }}">{{ $st[2] }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight">{{ $opportunity->title }}</h1>
        <p class="text-white/70 text-sm mt-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ $opportunity->location }}
        </p>
    </div>
</div>

{{-- ── MAIN CONTENT ────────────────────────────────────────────────────── --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $type)
    @if(session($type))
    @php $colors=['success'=>'#d1fae5,#065f46,#6ee7b7','error'=>'#fee2e2,#991b1b,#fca5a5','info'=>'#dbeafe,#1e40af,#93c5fd'][$type]; $c=explode(',',$colors); @endphp
    <div class="mb-6 p-4 rounded-2xl border flex gap-3 items-start"
         style="background:{{ $c[0] }};border-color:{{ $c[2] }}">
        <p class="text-sm font-semibold" style="color:{{ $c[1] }}">{{ session($type) }}</p>
    </div>
    @endif
    @endforeach

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT: Details ─────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Description --}}
            <div class="info-card">
                <h2 class="section-title">About This Opportunity</h2>
                <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600">
                    {!! nl2br(e($opportunity->description)) !!}
                </div>
            </div>

            {{-- Requirements --}}
            @if($opportunity->requirements)
            <div class="info-card">
                <h2 class="section-title">Requirements</h2>
                <div class="text-sm leading-relaxed text-slate-600">
                    {!! nl2br(e($opportunity->requirements)) !!}
                </div>
            </div>
            @endif

            {{-- Benefits --}}
            @if($opportunity->benefits)
            <div class="info-card">
                <h2 class="section-title">Volunteer Benefits</h2>
                <div class="text-sm leading-relaxed text-slate-600">
                    {!! nl2br(e($opportunity->benefits)) !!}
                </div>
            </div>
            @endif

            {{-- Available Shifts (only for approved volunteers) --}}
            @if($userApplication && $userApplication->status === 'approved')
            <div class="info-card">
                <h2 class="section-title">Available Shifts</h2>
                @php $openShifts = $opportunity->shifts->where('status','open'); @endphp
                @forelse($openShifts as $shift)
                @php
                    $hasRequest = $mySlotRequestIds->contains($shift->id);
                    $spotsLeft  = max(0, $shift->required_volunteers - $shift->assigned_count);
                @endphp
                <div class="flex items-center justify-between rounded-xl px-4 py-3 mb-3 border border-slate-100"
                     style="background:#f8fafc">
                    <div>
                        <p class="font-bold text-slate-900 text-sm">{{ $shift->title }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $shift->shift_date?->format('M d, Y') }} &middot;
                            {{ $shift->start_time }} – {{ $shift->end_time }} &middot;
                            {{ $spotsLeft }} spot{{ $spotsLeft !== 1 ? 's' : '' }} left
                        </p>
                    </div>
                    @if($hasRequest)
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold" style="background:#dbeafe;color:#1e40af">Requested</span>
                    @elseif($spotsLeft > 0)
                        <form action="{{ route('volunteer.shifts.request') }}" method="POST">
                            @csrf
                            <input type="hidden" name="shift_id" value="{{ $shift->id }}">
                            <button type="submit"
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold text-white transition hover:opacity-90"
                                    style="background:linear-gradient(135deg,#7c3aed,#2563eb)">
                                Request Slot &rarr;
                            </button>
                        </form>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold" style="background:#fee2e2;color:#991b1b">Full</span>
                    @endif
                </div>
                @empty
                <p class="text-sm text-slate-400 italic">No open shifts available at the moment.</p>
                @endforelse
            </div>
            @endif

            {{-- Gallery --}}
            @if(!empty($opportunity->gallery))
            <div class="info-card">
                <h2 class="section-title">Gallery</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($opportunity->gallery as $img)
                    <img src="{{ asset('storage/'.$img) }}" alt="Gallery"
                         class="rounded-xl object-cover w-full" style="height:140px">
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Sidebar ────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Apply CTA --}}
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEmployee()))
                <div class="rounded-2xl p-6 bg-slate-100 border border-slate-200 shadow-sm">
                    <h3 class="font-black text-lg text-slate-800 mb-2">Admin View</h3>
                    <p class="text-sm text-slate-500 mb-0">You are viewing this as an administrator. Admins and employees cannot apply for volunteering roles.</p>
                </div>
            @else
                <div class="rounded-2xl p-6 text-white shadow-lg" style="background:linear-gradient(135deg,#7c3aed,#2563eb)">
                    <h3 class="font-black text-lg mb-2">Ready to Help?</h3>
                    <p class="text-sm text-white/80 mb-5">Apply now and make a real difference in your community.</p>

                    @if($userApplication)
                        @if($userApplication->status === 'approved')
                            <div class="rounded-xl p-3 text-center font-bold text-sm" style="background:rgba(255,255,255,.15)">
                                ✓ You're approved for this opportunity!
                            </div>
                        @elseif($userApplication->status === 'pending')
                            <div class="rounded-xl p-3 text-center font-bold text-sm" style="background:rgba(255,255,255,.15)">
                                ⏳ Application under review
                            </div>
                        @else
                            <div class="rounded-xl p-3 text-center font-bold text-sm" style="background:rgba(220,38,38,.3)">
                                Application not approved
                            </div>
                        @endif
                    @elseif($opportunity->status === 'open' && (!$opportunity->registration_deadline || now()->lt($opportunity->registration_deadline)))
                        <a href="{{ route('volunteering.apply', $opportunity->slug) }}"
                           class="block w-full text-center py-3 rounded-xl font-bold text-sm transition hover:opacity-90"
                           style="background:#fff;color:#7c3aed">
                            Apply Now &rarr;
                        </a>
                    @else
                        <div class="rounded-xl p-3 text-center font-bold text-sm" style="background:rgba(255,255,255,.15)">
                            Applications Closed
                        </div>
                    @endif

                    @guest
                    <p class="text-xs text-white/60 text-center mt-3">
                        <a href="{{ route('login') }}" class="underline">Log in</a> to apply
                    </p>
                    @endguest
                </div>
            @endif

            {{-- Key Info Card --}}
            <div class="info-card space-y-4">
                <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wide">Key Details</h3>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#ede9fe">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Dates</p>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ $opportunity->start_date->format('M d') }} – {{ $opportunity->end_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                @if($opportunity->registration_deadline)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#fef3c7">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Apply By</p>
                        <p class="text-sm font-semibold {{ now()->gt($opportunity->registration_deadline) ? 'text-red-600' : 'text-slate-800' }}">
                            {{ $opportunity->registration_deadline->format('M d, Y · H:i') }}
                            @if(now()->gt($opportunity->registration_deadline))
                                <span class="text-red-400"> — Closed</span>
                            @endif
                        </p>
                    </div>
                </div>
                @endif

                @php $totalVol = $opportunity->shifts->sum('required_volunteers'); @endphp
                @if($totalVol > 0)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#d1fae5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Volunteers Needed</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $totalVol }} total</p>
                    </div>
                </div>
                @endif

                @if(!empty($opportunity->required_skills))
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Skills Needed</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($opportunity->required_skills as $skill)
                        <span class="skill-tag" style="background:#ede9fe;color:#6d28d9">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($opportunity->campaign)
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Related Campaign</p>
                    <a href="{{ route('campaigns.show', $opportunity->campaign->slug) }}"
                       class="text-sm font-semibold text-violet-600 hover:underline">
                        {{ $opportunity->campaign->title }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

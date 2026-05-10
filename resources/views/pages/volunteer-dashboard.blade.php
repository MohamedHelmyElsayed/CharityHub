@extends('layouts.app')
@section('title', 'Volunteer Dashboard')

@section('content')
<div class="min-h-screen" style="background: linear-gradient(135deg, #f8fafc 0%, #ede9fe 40%, #eff6ff 100%)">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ── Header ────────────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg text-white font-black text-2xl"
                 style="background: linear-gradient(135deg, #7c3aed, #2563eb)">
                {{ strtoupper(substr($volunteer->name ?? auth()->user()->name ?? 'V', 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $volunteer->name ?? auth()->user()->name }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    @php
                        $statusColor = match($volunteer->status) {
                            'approved','active' => 'background:#d1fae5;color:#065f46',
                            'pending'           => 'background:#fef3c7;color:#92400e',
                            'rejected'          => 'background:#fee2e2;color:#991b1b',
                            'suspended'         => 'background:#ffedd5;color:#9a3412',
                            default             => 'background:#f1f5f9;color:#475569',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold" style="{{ $statusColor }}">
                        {{ ucfirst($volunteer->status) }}
                    </span>
                    @if($volunteer->status === 'pending')
                        <span class="text-xs font-medium" style="color:#b45309">Your application is under review</span>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('volunteer.index') }}"
           class="px-5 py-2.5 rounded-xl font-semibold text-sm text-white shadow-md transition hover:opacity-90"
           style="background: linear-gradient(135deg, #7c3aed, #2563eb)">
            Browse Shifts
        </a>
    </div>

    {{-- ── Stats Row ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Approved Hours</p>
            <h3 class="text-3xl font-extrabold" style="color:#7c3aed">{{ number_format($totalApprovedHours, 1) }}</h3>
            <p class="text-xs text-slate-400 mt-1">Total verified</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Pending Hours</p>
            <h3 class="text-3xl font-extrabold" style="color:#d97706">{{ number_format($pendingHours, 1) }}</h3>
            <p class="text-xs text-slate-400 mt-1">Awaiting approval</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Shifts Completed</p>
            <h3 class="text-3xl font-extrabold" style="color:#059669">{{ $completedShifts }}</h3>
            <p class="text-xs text-slate-400 mt-1">This lifetime</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Donations</p>
            <h3 class="text-3xl font-extrabold" style="color:#2563eb">EGP {{ number_format($donationStats['total_donated'], 0) }}</h3>
            <p class="text-xs text-slate-400 mt-1">{{ $donationStats['donation_count'] }} donations</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left Column ────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Pending Notice --}}
            @if($volunteer->status === 'pending')
            <div class="rounded-2xl border p-5 flex gap-4 items-start" style="background:#fffbeb;border-color:#fde68a">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background:#fef3c7">
                    <svg class="w-5 h-5" style="color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold" style="color:#92400e">Application Under Review</p>
                    <p class="text-sm mt-1" style="color:#b45309">Your volunteer application is being reviewed by our admin team. Once approved you'll be able to browse and request shifts. You'll receive a notification by email.</p>
                </div>
            </div>
            @endif

            {{-- Upcoming Approved Shifts --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full inline-block" style="background:#10b981"></span>
                        Upcoming Approved Shifts
                    </h2>
                </div>
                @forelse($upcomingRequests as $req)
                <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-4 hover:bg-slate-50 transition">
                    <div class="w-12 h-12 rounded-xl flex flex-col items-center justify-center flex-shrink-0"
                         style="background:#ede9fe">
                        <span class="text-xs font-bold" style="color:#7c3aed">{{ $req->shift?->shift_date?->format('M') ?? '—' }}</span>
                        <span class="text-lg font-extrabold leading-none" style="color:#5b21b6">{{ $req->shift?->shift_date?->format('d') ?? '—' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-900 truncate">{{ $req->shift?->event?->title ?? 'Event' }}</p>
                        <p class="text-sm text-slate-500">{{ $req->shift?->title }} &middot; {{ $req->shift?->start_time }} &ndash; {{ $req->shift?->end_time }}</p>
                        <p class="text-xs text-slate-400">{{ $req->shift?->location ?? $req->shift?->event?->location }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold" style="background:#d1fae5;color:#065f46">Approved</span>
                </div>
                @empty
                <div class="px-6 py-10 text-center text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="font-medium text-slate-500">No upcoming approved shifts yet</p>
                    @if($volunteer->is_approved)
                        <a href="{{ route('volunteer.index') }}" class="text-sm font-bold" style="color:#7c3aed">Browse available shifts &rarr;</a>
                    @else
                        <p class="text-sm mt-1" style="color:#d97706">Shifts will be available once your application is approved</p>
                    @endif
                </div>
                @endforelse
            </div>

            {{-- Available Events to Join (only for approved volunteers) --}}
            @if($volunteer->is_approved && $availableEvents->count())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900">Open Events — Request a Shift</h2>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($availableEvents->take(4) as $event)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-semibold text-slate-900">{{ $event->title }}</p>
                            <span class="text-xs text-slate-400">{{ $event->start_date?->format('M d') }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mb-3">{{ $event->location }}</p>
                        @php $openShifts = $event->shifts->where('status','open'); @endphp
                        @if($openShifts->count())
                        <div class="space-y-2">
                            @foreach($openShifts->take(2) as $shift)
                            <div class="flex items-center justify-between rounded-xl px-4 py-2.5" style="background:#f8fafc">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $shift->title }}</p>
                                    <p class="text-xs text-slate-400">
                                        {{ $shift->shift_date?->format('M d') }} &middot;
                                        {{ $shift->start_time }}&ndash;{{ $shift->end_time }} &middot;
                                        {{ $shift->available_spots }} spot{{ $shift->available_spots !== 1 ? 's' : '' }} left
                                    </p>
                                </div>
                                <form action="{{ route('volunteer.shifts.request') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="shift_id" value="{{ $shift->id }}">
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-white transition hover:opacity-90 whitespace-nowrap"
                                            style="background: linear-gradient(135deg, #7c3aed, #2563eb)">
                                        Request &rarr;
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-xs text-slate-400 italic">No open shifts for this event</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Attendance History --}}
            @if($attendanceHistory->count())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900">Attendance History</h2>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($attendanceHistory as $log)
                    @php
                        $ac = match($log->status) {
                            'verified'    => 'background:#d1fae5;color:#065f46',
                            'checked_out' => 'background:#dbeafe;color:#1e40af',
                            'checked_in'  => 'background:#fef3c7;color:#92400e',
                            default       => 'background:#f1f5f9;color:#64748b',
                        };
                    @endphp
                    <div class="px-6 py-3 flex items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $log->shift?->event?->title ?? '—' }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $log->check_in?->format('M d, Y · H:i') }}
                                @if($log->check_out) &rarr; {{ $log->check_out->format('H:i') }} ({{ number_format($log->calculated_hours, 1) }}h) @endif
                            </p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-bold" style="{{ $ac }}">
                            {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── Right Column ───────────────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Profile Card --}}
            <div class="rounded-2xl p-6 text-white shadow-lg" style="background: linear-gradient(135deg, #7c3aed, #2563eb)">
                <h3 class="font-bold mb-4 text-xs uppercase tracking-widest" style="color:rgba(255,255,255,0.7)">Your Profile</h3>

                @if(!empty($volunteer->skills) && count($volunteer->skills))
                <div class="mb-4">
                    <p class="text-xs mb-2" style="color:rgba(255,255,255,0.6)">Skills</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($volunteer->skills as $skill)
                        <span class="px-2 py-1 rounded-lg text-xs font-medium" style="background:rgba(255,255,255,0.2)">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="text-sm mb-4" style="color:rgba(255,255,255,0.6)">No skills listed yet.</p>
                @endif

                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.15)">
                        <p class="text-2xl font-extrabold">{{ number_format($totalApprovedHours, 0) }}</p>
                        <p class="text-xs" style="color:rgba(255,255,255,0.7)">Approved Hrs</p>
                    </div>
                    <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.15)">
                        <p class="text-2xl font-extrabold">{{ $slotRequests->where('status','approved')->count() }}</p>
                        <p class="text-xs" style="color:rgba(255,255,255,0.7)">Shifts Joined</p>
                    </div>
                </div>

                @if($volunteer->bio)
                <p class="text-sm mt-4 leading-relaxed" style="color:rgba(255,255,255,0.75)">{{ Str::limit($volunteer->bio, 120) }}</p>
                @endif
            </div>

            {{-- My Shift Requests --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900 text-sm">My Shift Requests</h2>
                </div>
                <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
                    @forelse($slotRequests as $req)
                    <div class="px-5 py-3 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">
                                {{ $req->shift?->title ?? ($req->campaign?->title ?? 'Legacy request') }}
                            </p>
                            <p class="text-xs text-slate-400">{{ $req->created_at->format('M d, Y') }}</p>
                        </div>
                        @php
                            $rc = match($req->status) {
                                'approved'  => 'background:#d1fae5;color:#065f46',
                                'pending'   => 'background:#fef3c7;color:#92400e',
                                'rejected'  => 'background:#fee2e2;color:#991b1b',
                                'cancelled' => 'background:#f1f5f9;color:#94a3b8',
                                default     => 'background:#f1f5f9;color:#94a3b8',
                            };
                        @endphp
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="px-2 py-1 rounded-full text-xs font-bold" style="{{ $rc }}">
                                {{ ucfirst($req->status) }}
                            </span>
                            @if($req->status === 'pending' && $req->shift_id)
                            <form action="{{ route('volunteer.shifts.requests.cancel', $req->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition">Cancel</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-slate-400 text-sm">No shift requests yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Hour Logs --}}
            @if($hourLogs->count())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900 text-sm">Hour Log Status</h2>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($hourLogs->take(5) as $log)
                    @php
                        $hc = match($log->status) {
                            'approved'       => 'background:#d1fae5;color:#065f46',
                            'pending_review' => 'background:#fef3c7;color:#92400e',
                            'adjusted'       => 'background:#dbeafe;color:#1e40af',
                            'rejected'       => 'background:#fee2e2;color:#991b1b',
                            default          => 'background:#f1f5f9;color:#94a3b8',
                        };
                    @endphp
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ number_format($log->calculated_hours, 2) }} hrs</p>
                            <p class="text-xs text-slate-400">{{ Str::limit($log->attendanceLog?->shift?->event?->title ?? '—', 30) }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-bold" style="{{ $hc }}">
                            {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                            @if(in_array($log->status, ['approved','adjusted'])) &bull; {{ number_format($log->approved_hours ?? $log->calculated_hours, 1) }}h @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Donations --}}
            @if($recentDonations->count())
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900 text-sm">Recent Donations</h2>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($recentDonations as $don)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 truncate max-w-40">{{ $don->campaign?->title ?? 'General' }}</p>
                            <p class="text-xs text-slate-400">{{ $don->created_at->format('M d, Y') }}</p>
                        </div>
                        <span class="text-sm font-bold" style="color:#2563eb">EGP {{ number_format($don->amount, 0) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- end right column --}}
    </div>
</div>
</div>
@endsection

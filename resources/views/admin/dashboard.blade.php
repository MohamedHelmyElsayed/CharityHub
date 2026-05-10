@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">

        @include('admin.partials.sidebar')

        <div class="flex-1 space-y-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Dashboard Overview</h1>
                <p class="text-gray-500 mt-1">Welcome back. Here's everything at a glance.</p>
            </div>

            {{-- ── Financial KPIs ─────────────────────────────────────────── --}}
            <div>
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Financial</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1.5 bg-blue-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Total Raised</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">EGP {{ number_format($stats['total_raised'], 0) }}</h3>
                        <p class="text-xs text-green-600 mt-1 font-semibold">{{ $stats['total_donations'] }} completed donations</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1.5 bg-emerald-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Active Campaigns</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['active_campaigns'] }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-semibold">of {{ $stats['total_campaigns'] }} total</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1.5 bg-yellow-400"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Total Donors</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['total_donors'] }}</h3>
                        <p class="text-xs text-yellow-600 mt-1 font-semibold">{{ $stats['pending_donations'] }} pending</p>
                    </div>
                </div>
            </div>

            {{-- ── Volunteer KPIs ─────────────────────────────────────────── --}}
            <div>
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Volunteer Management</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1.5 bg-violet-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Volunteers</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['total_volunteers'] }}</h3>
                        <p class="text-xs text-violet-600 mt-1 font-semibold">{{ $stats['approved_volunteers'] }} approved</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1.5 bg-orange-400"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Pending Approval</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['pending_volunteers'] }}</h3>
                        @if($stats['pending_volunteers'] > 0)
                        <a href="{{ route('custom_admin.volunteers.index') }}" class="text-xs text-orange-600 mt-1 font-bold underline">Review now →</a>
                        @else
                        <p class="text-xs text-gray-400 mt-1">All clear</p>
                        @endif
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1.5 bg-teal-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Approved Hours</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_approved_hours'], 1) }}</h3>
                        <p class="text-xs text-teal-600 mt-1 font-semibold">{{ $stats['pending_hour_logs'] }} pending review</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 h-full w-1.5 bg-rose-500"></div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Slot Requests</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['pending_slot_requests'] }}</h3>
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $stats['active_events'] }} open events</p>
                    </div>
                </div>
            </div>

            {{-- ── Pending Volunteer Applications ─────────────────────────── --}}
            @if($pendingVolunteers->count())
            <div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-orange-100 bg-orange-50 flex justify-between items-center">
                    <h3 class="font-bold text-orange-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Pending Volunteer Applications
                    </h3>
                    <a href="{{ route('custom_admin.volunteers.index') }}" class="text-xs font-bold text-orange-700 underline">View All →</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($pendingVolunteers as $vol)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($vol->name ?? $vol->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $vol->name ?? $vol->user?->name }}</p>
                                <p class="text-xs text-gray-400">{{ $vol->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('custom_admin.volunteers.update-status', $vol->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button class="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition">Approve</button>
                            </form>
                            <form action="{{ route('custom_admin.volunteers.update-status', $vol->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-lg hover:bg-red-200 transition">Reject</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── Pending Hour Logs ──────────────────────────────────────── --}}
            @if($pendingHourLogs->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">⏱ Pending Hour Approvals</h3>
                    <a href="{{ route('filament.admin.resources.hour-logs.index') }}" class="text-xs font-bold text-blue-600 underline">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Volunteer</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Shift</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Logged</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pendingHourLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $log->volunteer?->name ?? $log->volunteer?->user?->name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $log->attendanceLog?->shift?->title ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm font-bold text-teal-700">{{ number_format($log->calculated_hours, 2) }} hrs</td>
                                <td class="px-6 py-3 text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- ── Recent Donations ───────────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-900 text-lg">Recent Donations</h3>
                    <a href="{{ route('custom_admin.donations.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Donor</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Campaign</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($recentDonations as $donation)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                        {{ $donation->user ? substr($donation->user->name, 0, 1) : 'A' }}
                                    </div>
                                    {{ $donation->user ? $donation->user->name : 'Anonymous' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $donation->campaign?->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-extrabold text-gray-900">EGP {{ number_format($donation->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-400">{{ $donation->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $donation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400 italic">No recent donations.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

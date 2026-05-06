@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Staff Operations Center</h1>
                <p class="text-slate-500 font-medium mt-2">Manage campaigns and monitor community activity.</p>
            </div>
            <div class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold uppercase tracking-widest border border-indigo-100">
                Staff Account
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            @foreach([
                ['label' => 'Active Campaigns', 'value' => $stats['active_campaigns'], 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color' => 'indigo'],
                ['label' => 'Total Volunteers', 'value' => $stats['total_volunteers'], 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'blue'],
                ['label' => 'Pending Donations', 'value' => $stats['pending_donations'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                ['label' => 'Recent Activity', 'value' => $stats['recent_donations_count'], 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'color' => 'emerald'],
            ] as $stat)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $stat['color'] }}-50 flex items-center justify-center text-{{ $stat['color'] }}-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
                        <h3 class="text-2xl font-extrabold text-slate-900">{{ $stat['value'] }}</h3>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Quick Actions & Campaigns --}}
            <div class="space-y-8">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6">Staff Quick Actions</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('admin.campaigns.create') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50 transition-all group text-center">
                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-600 mx-auto mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-700">Add Campaign</span>
                        </a>
                        <a href="{{ route('admin.volunteers.index') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50 transition-all group text-center">
                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600 mx-auto mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-700">Manage Volunteers</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900">Recent Campaigns</h2>
                        <a href="{{ route('admin.campaigns.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">View All</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($recentCampaigns as $campaign)
                        <div class="px-8 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div>
                                <p class="font-bold text-slate-900 text-sm">{{ $campaign->title }}</p>
                                <p class="text-xs text-slate-500 font-medium">Goal: EGP {{ number_format($campaign->goal_amount) }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $campaign->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $campaign->status }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Volunteers List --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Recently Active Volunteers</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($recentVolunteers as $volunteer)
                    <div class="px-8 py-5 flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($volunteer->name) }}&background=EEF2FF&color=4F46E5" alt="" class="w-10 h-10 rounded-full border border-slate-100">
                        <div>
                            <p class="font-bold text-slate-900 text-sm">{{ $volunteer->name }}</p>
                            <div class="flex gap-1 mt-1">
                                @foreach(array_slice($volunteer->skills ?? [], 0, 2) as $skill)
                                <span class="px-2 py-0.5 bg-slate-50 text-slate-500 rounded text-[9px] font-bold border border-slate-100">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-8 py-12 text-center text-slate-400 italic">No recent volunteer activity.</div>
                    @endforelse
                </div>
                <div class="p-6 bg-slate-50/50 border-t border-slate-100 text-center">
                    <a href="{{ route('admin.volunteers.index') }}" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors flex items-center justify-center gap-2">
                        Open Volunteer Management
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

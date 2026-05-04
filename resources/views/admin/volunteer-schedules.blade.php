@extends('layouts.app')

@section('title', 'Volunteer Schedules — Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0 hidden lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-900 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Admin Panel
                    </h3>
                </div>
                <nav class="p-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Dashboard Overview</a>
                    <a href="{{ route('admin.campaigns.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Manage Campaigns</a>
                    <a href="{{ route('admin.donations.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Donations Ledger</a>
                    <a href="{{ route('admin.volunteers.index') }}" class="bg-primary-50 text-primary-700 block px-4 py-3 rounded-lg text-sm font-bold transition">Volunteers</a>
                </nav>
            </div>
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Volunteer Schedules</h1>

            <div class="grid grid-cols-1 gap-8">
                @forelse($schedules as $schedule)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $schedule->event_name }}</h2>
                            <p class="text-sm text-gray-500 font-medium">Campaign: {{ $schedule->campaign->title }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <span class="block text-sm font-bold text-gray-900">{{ $schedule->event_date->format('M d, Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</span>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full border border-blue-200 uppercase tracking-wider">
                                {{ $schedule->status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Assigned Volunteers ({{ $schedule->volunteers->count() }}/{{ $schedule->max_volunteers }})</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            @foreach($schedule->volunteers as $vol)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                    {{ substr($vol->name, 0, 1) }}
                                </div>
                                <div class="flex-grow">
                                    <div class="text-xs font-bold text-gray-900">{{ $vol->name }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $vol->pivot->status }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($schedule->volunteers->count() < $schedule->max_volunteers)
                        <div class="pt-6 border-t border-gray-50">
                            <form action="{{ route('admin.volunteer-schedules.assign', $schedule->id) }}" method="POST" class="flex gap-3">
                                @csrf
                                <select name="volunteer_id" class="flex-1 border-gray-300 rounded-lg text-sm border p-2 bg-white" required>
                                    <option value="">Assign a volunteer...</option>
                                    @foreach($volunteers as $v)
                                        @if(!$schedule->volunteers->contains($v->id))
                                            <option value="{{ $v->id }}">{{ $v->name }} ({{ implode(', ', $v->skills) }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-primary-700 transition">Assign</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500 italic">
                    No schedules found.
                </div>
                @endforelse
            </div>

            @if($schedules->hasPages())
            <div class="mt-8">
                {{ $schedules->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

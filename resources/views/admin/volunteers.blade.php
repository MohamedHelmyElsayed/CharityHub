@extends('layouts.app')

@section('title', 'Manage Volunteers')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1 space-y-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Volunteer Applications</h1>

            <!-- Pending Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-amber-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Pending Applications
                    </h3>
                    <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2 py-1 rounded-full border border-amber-200">{{ $pendingVolunteers->count() }} new</span>
                </div>
                
                <div class="p-6">
                    @if($pendingVolunteers->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($pendingVolunteers as $vol)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-lg">
                                            {{ substr($vol->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-extrabold text-gray-900">{{ $vol->name }}</h3>
                                            <div class="text-sm font-medium text-gray-500 mt-1 flex flex-col gap-1">
                                                <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> {{ $vol->email }}</span>
                                                @if($vol->phone)
                                                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> {{ $vol->phone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200">{{ $vol->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Skills & Interests</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($vol->skills as $skill)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                    @if($vol->bio)
                                    <p class="text-sm mt-3 text-gray-600 italic">"{{ $vol->bio }}"</p>
                                    @endif
                                </div>

                                @if($vol->schedules->count() > 0)
                                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                                    <p class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-3">Requested Event(s)</p>
                                    <ul class="space-y-2">
                                        @foreach($vol->schedules as $schedule)
                                        <li class="text-sm text-blue-900 font-medium flex flex-col">
                                            <span class="font-bold">{{ $schedule->event_name }}</span>
                                            <span class="text-xs text-blue-700">{{ $schedule->event_date->format('M d, Y') }} at {{ $schedule->start_time->format('H:i') }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                </div>

                                <div class="mt-auto pt-4 border-t border-gray-100 flex gap-3">
                                    <form action="{{ route('custom_admin.volunteers.update-status', $vol->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm">Approve</button>
                                    </form>
                                    <form action="{{ route('custom_admin.volunteers.update-status', $vol->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="w-full bg-white border border-red-300 text-red-600 py-2.5 rounded-lg text-sm font-bold hover:bg-red-50 transition" onclick="return confirm('Are you sure you want to reject this applicant?')">Reject</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="font-medium text-gray-900 mb-1">No pending applications</p>
                            <p class="text-sm">You're all caught up!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                        Approved Volunteers Roster
                    </h3>
                    <span class="text-xs font-bold text-gray-500 bg-gray-200 px-2 py-1 rounded-full">{{ $activeVolunteers->count() }} active</span>
                </div>
                
                <div class="p-6">
                    @if($activeVolunteers->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($activeVolunteers as $vol)
                            <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col hover:border-blue-300 transition-colors">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                            {{ substr($vol->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm">{{ $vol->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $vol->total_hours }} hours logged</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('custom_admin.volunteers.update-status', $vol->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 transition" title="Deactivate">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="flex flex-wrap gap-1 mt-auto">
                                    @foreach(array_slice($vol->skills, 0, 3) as $skill)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full border border-gray-200">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                    @if(count($vol->skills) > 3)
                                        <span class="px-2 py-0.5 text-gray-400 text-[10px] font-bold">+{{ count($vol->skills) - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="font-medium text-lg text-gray-900 mb-1">No active volunteers found</p>
                            <p class="text-sm">Approve applications above to add them to the roster.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

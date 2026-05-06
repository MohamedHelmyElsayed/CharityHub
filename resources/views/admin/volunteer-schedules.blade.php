@extends('layouts.app')

@section('title', 'Volunteer Schedules — Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 class="text-3xl font-extrabold text-gray-900">Volunteer Schedules</h1>
                <a href="{{ route('custom_admin.schedules.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Schedule
                </a>
            </div>

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
                            <div class="flex gap-2">
                                <a href="{{ route('custom_admin.schedules.edit', $schedule->id) }}" class="p-2 text-gray-400 hover:text-primary-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('custom_admin.schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Delete this schedule?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Assigned Volunteers ({{ $schedule->volunteers->count() }}{{ $schedule->max_volunteers ? '/' . $schedule->max_volunteers : '' }})</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            @foreach($schedule->volunteers as $vol)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100 group relative">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                    {{ substr($vol->name, 0, 1) }}
                                </div>
                                <div class="flex-grow">
                                    <div class="text-xs font-bold text-gray-900">{{ $vol->name }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $vol->pivot->status }}</div>
                                </div>
                                <!-- Unassign Button -->
                                <form action="{{ route('custom_admin.volunteer-schedules.unassign', [$schedule->id, $vol->id]) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600 transition" onclick="return confirm('Remove {{ $vol->name }} from this event?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>

                        @if(!$schedule->max_volunteers || $schedule->volunteers->count() < $schedule->max_volunteers)
                        <div class="pt-6 border-t border-gray-50">
                            <form action="{{ route('custom_admin.volunteer-schedules.assign', $schedule->id) }}" method="POST" class="flex gap-3">
                                @csrf
                                <select name="volunteer_id" class="flex-1 border-gray-300 rounded-lg text-sm border p-2 bg-white" required>
                                    <option value="">Assign a volunteer...</option>
                                    @foreach($volunteers as $v)
                                        @if(!$schedule->volunteers->contains($v->id))
                                            <option value="{{ $v->id }}">{{ $v->name }} ({{ implode(', ', $v->skills) }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">Assign</button>
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

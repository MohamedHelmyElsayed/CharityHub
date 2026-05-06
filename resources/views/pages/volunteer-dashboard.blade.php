@extends('layouts.app')

@section('title', 'Volunteer Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50 py-12 lg:py-20 relative overflow-hidden">
    {{-- Background decorations --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-100/50 blur-3xl opacity-40 rounded-full -translate-y-1/2 -translate-x-1/4"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex flex-col lg:flex-row gap-10">
            {{-- Profile Sidebar --}}
            <div class="lg:w-1/3 space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-10 text-center relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
                    
                    <div class="relative mb-6">
                        <div class="w-32 h-32 bg-slate-100 rounded-full mx-auto flex items-center justify-center text-4xl font-extrabold text-blue-600 border-4 border-white shadow-xl relative z-10 group-hover:scale-105 transition-transform duration-500">
                            {{ substr($volunteer->name, 0, 1) }}
                        </div>
                        <div class="absolute inset-0 bg-blue-500 blur-2xl opacity-10 rounded-full scale-125 group-hover:opacity-20 transition-opacity"></div>
                    </div>

                    <h1 class="text-2xl font-extrabold text-slate-900 mb-2">{{ $volunteer->name }}</h1>
                    <p class="text-slate-500 font-medium mb-6">{{ $volunteer->email }}</p>

                    <div class="flex justify-center gap-3 mb-8">
                        @foreach($volunteer->skills as $skill)
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider rounded-full border border-blue-100">
                            {{ $skill }}
                        </span>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-8 border-t border-slate-50">
                        <div>
                            <span class="block text-2xl font-extrabold text-slate-900 tracking-tight">{{ $volunteer->total_hours }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hours Logged</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-extrabold text-slate-900 tracking-tight">{{ $pastSchedules->count() }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Events Done</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-slate-900 rounded-[2.5rem] p-8 lg:p-10 text-white">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        Quick Actions
                    </h3>
                    <div class="space-y-4">
                        <a href="{{ route('volunteer.index') }}" class="flex items-center justify-between p-4 bg-white/5 hover:bg-white/10 rounded-2xl border border-white/10 transition-all font-bold text-sm">
                            Find New Events
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <button class="flex items-center justify-between w-full p-4 bg-white/5 hover:bg-white/10 rounded-2xl border border-white/10 transition-all font-bold text-sm text-left">
                            Update Profile
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:w-2/3 space-y-12">
                {{-- Upcoming Events --}}
                <section>
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Upcoming Events</h2>
                        <span class="px-4 py-1.5 bg-green-50 text-green-600 text-xs font-bold rounded-full border border-green-100">
                            {{ $upcomingSchedules->count() }} Active
                        </span>
                    </div>

                    <div class="space-y-6">
                        @forelse($upcomingSchedules as $schedule)
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col md:flex-row gap-8 items-center group hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                            <div class="w-20 h-20 rounded-2xl bg-blue-50 text-blue-600 flex flex-col items-center justify-center flex-shrink-0 border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <span class="text-2xl font-black leading-none">{{ $schedule->event_date->format('d') }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest">{{ $schedule->event_date->format('M') }}</span>
                            </div>
                            <div class="flex-grow text-center md:text-left">
                                <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $schedule->event_name }}</h3>
                                <div class="flex flex-wrap justify-center md:justify-start gap-4 text-slate-500 font-medium text-sm">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $schedule->location_name }}
                                    </span>
                                </div>
                            </div>
                            <button class="px-6 py-3 bg-slate-50 text-slate-900 rounded-xl hover:bg-slate-900 hover:text-white transition-all font-bold text-sm border border-slate-100">
                                View Details
                            </button>
                        </div>
                        @empty
                        <div class="py-16 text-center bg-white rounded-[2.5rem] border border-dashed border-slate-200">
                            <p class="text-slate-400 font-medium italic">No upcoming events. Go find some!</p>
                        </div>
                        @endforelse
                    </div>
                </section>

                {{-- Activity Log --}}
                <section>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-8">Past Contributions</h2>
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-slate-50">
                                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Event</th>
                                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Hours</th>
                                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($pastSchedules as $schedule)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-8 py-6 font-bold text-slate-900">{{ $schedule->event_name }}</td>
                                        <td class="px-8 py-6 text-slate-500 font-medium">{{ $schedule->event_date->format('M d, Y') }}</td>
                                        <td class="px-8 py-6">
                                            @if(!$schedule->pivot->hours_worked && $schedule->event_date->isPast())
                                                <form action="{{ route('volunteer.log-hours') }}" method="POST" class="flex items-center gap-2">
                                                    @csrf
                                                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                    <input type="number" name="hours" step="0.5" min="0.5" max="24" required 
                                                           class="w-16 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold focus:ring-2 focus:ring-blue-500"
                                                           placeholder="Hrs">
                                                    <button type="submit" class="p-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="px-3 py-1 bg-blue-50 text-blue-600 font-black rounded-lg text-sm">
                                                    {{ $schedule->pivot->hours_worked ?? '-' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                                {{ $schedule->pivot->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-10 text-center text-slate-400 italic">No history yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

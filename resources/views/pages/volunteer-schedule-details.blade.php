@extends('layouts.app')

@section('title', 'Event Details - ' . $schedule->event_name)

@section('content')
<div class="min-h-screen bg-slate-50 py-16 lg:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm font-semibold text-slate-400">
                <li><a href="{{ route('volunteer.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                <li class="text-slate-900">Event Details</li>
            </ol>
        </nav>

        <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            {{-- Header/Image placeholder or Campaign link --}}
            @if($schedule->campaign)
            <div class="relative h-64 bg-slate-900">
                <img src="{{ $schedule->campaign->featured_image ? asset('storage/' . $schedule->campaign->featured_image) : 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop' }}" 
                     alt="{{ $schedule->event_name }}" class="w-full h-full object-cover opacity-60">
                <div class="absolute bottom-0 left-0 right-0 p-8 lg:p-12 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent">
                    <div class="flex flex-col gap-3">
                        @if($schedule->campaign)
                        <span class="px-4 py-1.5 bg-blue-600/90 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest rounded-full self-start shadow-lg">
                            Part of: {{ $schedule->campaign->title }}
                        </span>
                        @endif
                        <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">{{ $schedule->event_name }}</h1>
                    </div>
                </div>
            </div>
            @else
            <div class="p-12 bg-gradient-to-br from-slate-900 to-slate-800">
                <h1 class="text-4xl font-black text-white tracking-tight">{{ $schedule->event_name }}</h1>
            </div>
            @endif

            <div class="p-8 lg:p-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <div class="flex items-start gap-4 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Date</p>
                            <p class="text-slate-900 font-extrabold truncate">{{ $schedule->event_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Time</p>
                            <p class="text-slate-900 font-extrabold">{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-6 bg-slate-50 rounded-3xl border border-slate-100 sm:col-span-2 lg:col-span-1">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Location</p>
                            <p class="text-slate-900 font-extrabold leading-tight">{{ $schedule->location_name ?? $schedule->location }}</p>
                        </div>
                    </div>
                </div>

                <div class="prose prose-slate max-w-none">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">About this Event</h3>
                    <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $schedule->description ?? 'No detailed description provided for this event.' }}</p>
                </div>

                @if($schedule->campaign)
                <div class="mt-12 p-8 bg-slate-50 rounded-[2rem] border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="font-bold text-slate-900 text-lg mb-1">Support this Campaign</h4>
                        <p class="text-sm text-slate-500 font-medium">Learn more about the mission behind this event.</p>
                    </div>
                    <a href="{{ route('campaigns.show', $schedule->campaign->slug) }}" class="px-8 py-4 bg-white border border-slate-200 text-slate-900 rounded-2xl font-bold text-sm hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                        View Campaign Page
                    </a>
                </div>
                @endif

                <div class="mt-12 flex justify-center">
                    <a href="{{ route('volunteer.dashboard') }}" class="px-10 py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

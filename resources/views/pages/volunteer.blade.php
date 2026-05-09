@extends('layouts.app')

@section('title', 'Become a Volunteer')

@section('content')
<div class="min-h-screen bg-slate-50 py-12 lg:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="text-center mb-16 space-y-4">
            <h1 class="text-4xl lg:text-6xl font-black text-slate-900 tracking-tight">
                Join Our <span class="text-blue-600">Community</span>
            </h1>
            <p class="text-lg text-slate-500 font-medium max-w-2xl mx-auto">
                Help us make a difference. Fill out the form below to start your volunteering journey with CharityHub.
            </p>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
            <div class="p-8 lg:p-12">
                <form method="POST" action="{{ route('volunteer.register') }}" class="space-y-10">
                    @csrf
                    
                    @guest
                        <div class="p-6 bg-blue-50 border border-blue-100 rounded-3xl flex gap-4 items-center">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow-sm text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-blue-800">
                                Have an account? <a href="{{ route('login') }}" class="underline decoration-2 hover:text-blue-600 transition-colors">Sign in</a> to track your hours automatically.
                            </p>
                        </div>
                    @endguest

                    {{-- Personal Info Section --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                            <h3 class="text-xl font-bold text-slate-900">Personal Information</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700 ml-1">Full Name</label>
                                <input type="text" name="name" required value="{{ old('name', auth()->user()?->name) }}"
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-medium"
                                       placeholder="Enter your name">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700 ml-1">Email Address</label>
                                <input type="email" name="email" required value="{{ old('email', auth()->user()?->email) }}"
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-medium"
                                       placeholder="your@email.com">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700 ml-1">Phone Number</label>
                                <input type="text" name="phone" required value="{{ old('phone', $myVolunteer?->phone) }}"
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-medium"
                                       placeholder="+1 (555) 000-0000">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-slate-700 ml-1">Skills (e.g. Design, IT)</label>
                                <input type="text" name="skills" required value="{{ old('skills', $myVolunteer ? implode(', ', $myVolunteer->skills) : '') }}"
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-medium"
                                       placeholder="What are you good at?">
                            </div>
                        </div>
                    </div>

                    {{-- Event Selection Section --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                            <h3 class="text-xl font-bold text-slate-900">Choose an Event <span class="text-slate-400 font-normal text-sm ml-2">(Optional)</span></h3>
                        </div>

                        {{-- Event Selection Dropdown --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 ml-1">Select an Event</label>
                            <div class="relative">
                                <select name="schedule_id" id="event_selector" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-medium appearance-none">
                                    <option value="">-- General Volunteering (No specific event) --</option>
                                    @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->id }}">
                                            {{ $schedule->event_date->format('M d, Y') }} | {{ $schedule->event_name }} ({{ $schedule->campaign->title }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 ml-1 mt-2">Select an event you wish to participate in, or leave empty for general volunteering.</p>
                        </div>
                    </div>

                    {{-- Bio Section --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                            <h3 class="text-xl font-bold text-slate-900">Why Volunteer?</h3>
                        </div>
                        <textarea name="bio" required rows="5"
                                  class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all font-medium resize-none"
                                  placeholder="Tell us a bit about yourself and your motivation...">{{ old('bio', $myVolunteer?->bio) }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-6 border-t border-slate-100">
                        <button type="submit"
                                class="w-full py-5 bg-slate-900 hover:bg-blue-600 text-white font-extrabold text-xl rounded-3xl transition-all duration-300 shadow-xl shadow-slate-900/10 hover:shadow-blue-500/30 flex justify-center items-center gap-3">
                            <span>Submit Application</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <p class="text-sm text-center text-slate-400 font-medium mt-6">We'll review your application and contact you soon.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

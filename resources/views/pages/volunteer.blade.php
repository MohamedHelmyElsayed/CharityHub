@extends('layouts.app')

@section('title', 'Become a Volunteer')

@section('content')
<div class="min-h-screen bg-slate-50 py-16 lg:py-24 relative overflow-hidden">
    {{-- Background decorations --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-40 right-20 w-[600px] h-[600px] bg-gradient-to-br from-blue-100 to-transparent blur-3xl opacity-50 rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            
            {{-- Left column: Info --}}
            <div class="lg:sticky lg:top-32 space-y-8">
                <div>
                    <span class="text-blue-600 font-bold text-sm uppercase tracking-wider mb-3 block">Join Our Team</span>
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.1]">
                        Give your time.<br>Change the world.
                    </h1>
                </div>
                
                <p class="text-lg text-slate-500 font-medium leading-relaxed max-w-lg">
                    Whether you have professional skills to offer or just a few hours on the weekend, your time is invaluable. Join our global network of volunteers.
                </p>

                <div class="space-y-6 pt-4">
                    @foreach([
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Flexible Commitment', 'desc' => 'Choose schedules that fit your availability.'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Verified Hours', 'desc' => 'Get official certificates for your volunteer hours.'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Global Community', 'desc' => 'Connect with like-minded change-makers.'],
                    ] as $perk)
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm border border-slate-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $perk['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg">{{ $perk['title'] }}</h3>
                            <p class="text-slate-500 font-medium text-sm mt-1">{{ $perk['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right column: Form --}}
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 lg:p-12 relative z-10">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Application Form</h2>
                
                <form method="POST" action="{{ route('volunteer.register') }}" class="space-y-6">
                    @csrf
                    
                    @guest
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex gap-3 items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-medium text-blue-800">
                                You are applying as a guest. To easily track your hours and schedules later, <a href="{{ route('login') }}" class="font-bold underline hover:text-blue-900">sign in</a> first.
                            </p>
                        </div>
                    @endguest

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                            <input type="text" name="name" required value="{{ old('name', auth()->user()?->name) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all font-medium @error('name') border-red-300 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                            <input type="email" name="email" required value="{{ old('email', auth()->user()?->email) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all font-medium @error('email') border-red-300 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" required value="{{ old('phone') }}"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all font-medium"
                               placeholder="+1 (555) 000-0000">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Skills & Interests (Comma separated)</label>
                        <input type="text" name="skills" required value="{{ old('skills') }}"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all font-medium"
                               placeholder="e.g. translation, event planning, web design">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Why do you want to volunteer?</label>
                        <textarea name="bio" required rows="4"
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all font-medium resize-none"
                                  placeholder="Tell us a little about yourself..."></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-4 bg-slate-900 hover:bg-blue-600 text-white font-bold text-lg rounded-xl transition-all duration-300 shadow-xl shadow-slate-900/10 hover:shadow-blue-500/25 flex justify-center items-center gap-2">
                        Submit Application
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                    <p class="text-xs text-center text-slate-400 font-medium">We will review your application and get back to you within 48 hours.</p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

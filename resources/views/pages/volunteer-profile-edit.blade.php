@extends('layouts.app')

@section('title', 'Update Volunteer Profile')

@section('content')
<div class="min-h-screen bg-slate-50 py-16 lg:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm font-semibold text-slate-400">
                <li><a href="{{ route('volunteer.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                <li class="text-slate-900">Update Profile</li>
            </ol>
        </nav>

        <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3">
                {{-- Sidebar Info --}}
                <div class="bg-slate-900 p-8 lg:p-12 text-white flex flex-col justify-between">
                    <div>
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mb-8 shadow-lg shadow-blue-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h1 class="text-3xl font-black tracking-tight mb-4">Your Profile</h1>
                        <p class="text-slate-400 font-medium leading-relaxed">Keep your information up to date so we can match you with the best opportunities.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-sm font-bold text-slate-300">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verified Volunteer
                        </div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Member since {{ $myVolunteer->created_at->format('M Y') }}</div>
                    </div>
                </div>

                {{-- Form Column --}}
                <div class="md:col-span-2 p-8 lg:p-12">
                    <form method="POST" action="{{ route('volunteer.register') }}" class="space-y-8">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                                <input type="text" name="name" required value="{{ old('name', $myVolunteer->name) }}"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                                <input type="email" name="email" required value="{{ old('email', $myVolunteer->email) }}"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                            <input type="text" name="phone" required value="{{ old('phone', $myVolunteer->phone) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Skills & Interests (Comma separated)</label>
                            <input type="text" name="skills" required value="{{ old('skills', implode(', ', $myVolunteer->skills ?? [])) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Bio / About You</label>
                            <textarea name="bio" required rows="4"
                                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium resize-none">{{ old('bio', $myVolunteer->bio) }}</textarea>
                        </div>

                        <div class="flex items-center justify-between gap-6 pt-4">
                            <a href="{{ route('volunteer.dashboard') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Cancel Changes</a>
                            <button type="submit" class="px-10 py-4 bg-slate-900 hover:bg-blue-600 text-white font-bold rounded-2xl transition-all shadow-xl shadow-slate-900/10 hover:shadow-blue-500/25">
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', isset($schedule) ? 'Edit Schedule' : 'Create Schedule')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar (Copy from others) -->
        <div class="w-64 flex-shrink-0 hidden lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-900 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Admin Panel
                    </h3>
                </div>
                <nav class="p-3 space-y-1">
                    <a href="{{ route('custom_admin.dashboard') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Dashboard Overview</a>
                    <a href="{{ route('custom_admin.campaigns.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Manage Campaigns</a>
                    <a href="{{ route('custom_admin.donations.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Donations Ledger</a>
                    <a href="{{ route('custom_admin.volunteers.index') }}" class="bg-primary-50 text-primary-700 block px-4 py-3 rounded-lg text-sm font-bold transition">Volunteers</a>
                </nav>
            </div>
        </div>

        <div class="flex-1">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('custom_admin.schedules.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-500 hover:text-gray-900 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-extrabold text-gray-900">{{ isset($schedule) ? 'Edit Event Schedule' : 'Create New Event Schedule' }}</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <form action="{{ isset($schedule) ? route('custom_admin.schedules.update', $schedule->id) : route('custom_admin.schedules.store') }}" 
                      method="POST" 
                      class="p-8 space-y-6">
                    @csrf
                    @if(isset($schedule))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Event Name --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Event Name</label>
                            <input type="text" name="event_name" required value="{{ old('event_name', $schedule->event_name ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. Community Food Drive">
                        </div>

                        {{-- Campaign --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Related Campaign</label>
                            <select name="campaign_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all bg-white">
                                <option value="">Select a Campaign</option>
                                @foreach($campaigns as $camp)
                                    <option value="{{ $camp->id }}" {{ (old('campaign_id', $schedule->campaign_id ?? '') == $camp->id) ? 'selected' : '' }}>
                                        {{ $camp->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Capacity --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Max Volunteers</label>
                            <input type="number" name="max_volunteers" required value="{{ old('max_volunteers', $schedule->max_volunteers ?? '10') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Event Date</label>
                            <input type="date" name="event_date" required value="{{ old('event_date', isset($schedule->event_date) ? $schedule->event_date->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Status (Only on Edit) --}}
                        @if(isset($schedule))
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all bg-white">
                                <option value="scheduled" {{ (old('status', $schedule->status) === 'scheduled') ? 'selected' : '' }}>Scheduled</option>
                                <option value="cancelled" {{ (old('status', $schedule->status) === 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                                <option value="completed" {{ (old('status', $schedule->status) === 'completed') ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        @endif

                        {{-- Times --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Start Time</label>
                            <input type="time" name="start_time" required value="{{ old('start_time', isset($schedule->start_time) ? $schedule->start_time->format('H:i') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">End Time</label>
                            <input type="time" name="end_time" required value="{{ old('end_time', isset($schedule->end_time) ? $schedule->end_time->format('H:i') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Location --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Location Name</label>
                            <input type="text" name="location" required value="{{ old('location', $schedule->location ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. Central Park North Gate">
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="4"
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none"
                                      placeholder="Provide details about the volunteer tasks...">{{ old('description', $schedule->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                        <a href="{{ route('custom_admin.schedules.index') }}" class="px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-4 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-gray-200">
                            {{ isset($schedule) ? 'Update Schedule' : 'Create Schedule' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

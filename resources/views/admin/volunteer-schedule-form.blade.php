@extends('layouts.app')

@section('title', isset($schedule) ? 'Edit Opportunity' : 'Create Opportunity')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('custom_admin.schedules.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-500 hover:text-gray-900 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-extrabold text-gray-900">{{ isset($schedule) ? 'Edit Volunteer Opportunity' : 'Create New Volunteer Opportunity' }}</h1>
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
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Opportunity Title</label>
                            <input type="text" name="title" required value="{{ old('title', $schedule->title ?? '') }}"
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

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all bg-white">
                                <option value="open" {{ (old('status', $schedule->status ?? 'open') === 'open') ? 'selected' : '' }}>Open (Accepting Applications)</option>
                                <option value="full" {{ (old('status', $schedule->status ?? '') === 'full') ? 'selected' : '' }}>Full</option>
                                <option value="completed" {{ (old('status', $schedule->status ?? '') === 'completed') ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ (old('status', $schedule->status ?? '') === 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        {{-- Type & Category --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Event Type</label>
                            <select name="event_type" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all bg-white">
                                <option value="In-person" {{ (old('event_type', $schedule->event_type ?? '') === 'In-person') ? 'selected' : '' }}>In-person</option>
                                <option value="Remote" {{ (old('event_type', $schedule->event_type ?? '') === 'Remote') ? 'selected' : '' }}>Remote</option>
                                <option value="Hybrid" {{ (old('event_type', $schedule->event_type ?? '') === 'Hybrid') ? 'selected' : '' }}>Hybrid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                            <input type="text" name="category" required value="{{ old('category', $schedule->category ?? 'Community Service') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. Environment, Education">
                        </div>

                        {{-- Dates --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" required value="{{ old('start_date', isset($schedule->start_date) ? $schedule->start_date->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" required value="{{ old('end_date', isset($schedule->end_date) ? $schedule->end_date->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Deadline & Capacity --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Registration Deadline</label>
                            <input type="date" name="registration_deadline" value="{{ old('registration_deadline', isset($schedule->registration_deadline) ? $schedule->registration_deadline->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Max Volunteers Needed</label>
                            <input type="number" name="max_volunteers" required value="{{ old('max_volunteers', $schedule->max_volunteers ?? '10') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Location --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Location Name</label>
                            <input type="text" name="location" required value="{{ old('location', $schedule->location ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. Central Park North Gate">
                        </div>

                        {{-- Required Skills --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Required Skills (Comma separated)</label>
                            <input type="text" name="required_skills" value="{{ old('required_skills', isset($schedule->required_skills) ? implode(', ', $schedule->required_skills) : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. Communication, Teamwork, Teaching">
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Opportunity Description</label>
                            <textarea name="description" rows="4" required
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none"
                                      placeholder="Provide details about the volunteer tasks...">{{ old('description', $schedule->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                        <a href="{{ route('custom_admin.schedules.index') }}" class="px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-4 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-gray-200">
                            {{ isset($schedule) ? 'Update Opportunity' : 'Create Opportunity' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

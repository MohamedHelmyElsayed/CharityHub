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

            {{-- ── Manage Shifts Section ────────────────────────────────────────── --}}
            @if(isset($schedule))
            <div class="mt-12 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-extrabold text-gray-900">Opportunity Shifts</h2>
                    <p class="text-sm text-gray-500 font-medium">Add time slots for volunteers to join.</p>
                </div>

                {{-- Add Shift Form --}}
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <form action="{{ route('custom_admin.schedules.shifts.store', $schedule->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Shift Title</label>
                                <input type="text" name="title" required placeholder="e.g. Morning Shift"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date</label>
                                <input type="date" name="shift_date" required value="{{ $schedule->start_date->format('Y-m-d') }}"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Time Range</label>
                                <div class="flex items-center gap-2">
                                    <input type="time" name="start_time" required value="09:00"
                                           class="w-full px-2 py-2 border border-gray-200 rounded-lg text-sm">
                                    <span class="text-gray-400">-</span>
                                    <input type="time" name="end_time" required value="17:00"
                                           class="w-full px-2 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Goal</label>
                                <input type="number" name="required_volunteers" required value="5" min="1"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div class="md:col-span-5 flex justify-end">
                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-6 rounded-lg transition-all shadow-sm">
                                    + Add Shift
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Existing Shifts List --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Shift</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date & Time</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Capacity</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($schedule->shifts as $shift)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $shift->title }}</p>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider {{ $shift->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $shift->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="font-medium">{{ $shift->shift_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $shift->start_time }} - {{ $shift->end_time }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm font-bold text-gray-900">{{ $shift->assigned_count }} / {{ $shift->required_volunteers }}</div>
                                    <div class="w-24 h-1.5 bg-gray-100 rounded-full mx-auto mt-1 overflow-hidden">
                                        @php $pct = min(100, ($shift->assigned_count / $shift->required_volunteers) * 100); @endphp
                                        <div class="h-full bg-emerald-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('custom_admin.schedules.shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Remove this shift?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">No shifts created yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

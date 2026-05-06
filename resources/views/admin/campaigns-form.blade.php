@extends('layouts.app')

@section('title', isset($campaign) ? 'Edit Campaign' : 'Create Campaign')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Admin Content -->
        <div class="flex-1">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('custom_admin.campaigns.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-500 hover:text-gray-900 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-extrabold text-gray-900">{{ isset($campaign) ? 'Edit Campaign' : 'Create New Campaign' }}</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <form action="{{ isset($campaign) ? route('custom_admin.campaigns.update', $campaign->id) : route('custom_admin.campaigns.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      class="p-8 space-y-6">
                    @csrf
                    @if(isset($campaign))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Campaign Title</label>
                            <input type="text" name="title" required value="{{ old('title', $campaign->title ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. Clean Water for East Africa">
                        </div>

                        {{-- Goal Amount --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Goal Amount (EGP)</label>
                            <input type="number" name="goal_amount" required value="{{ old('goal_amount', $campaign->goal_amount ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="0.00">
                        </div>

                        {{-- Deadline --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deadline</label>
                            <input type="date" name="deadline" required value="{{ old('deadline', isset($campaign->deadline) ? $campaign->deadline->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        </div>

                        {{-- Status (Only on Edit) --}}
                        @if(isset($campaign))
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all bg-white">
                                <option value="active" {{ (old('status', $campaign->status) === 'active') ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ (old('status', $campaign->status) === 'completed') ? 'selected' : '' }}>Completed</option>
                                <option value="paused" {{ (old('status', $campaign->status) === 'paused') ? 'selected' : '' }}>Paused</option>
                            </select>
                        </div>
                        @endif

                        {{-- Short Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Short Description</label>
                            <input type="text" name="short_description" value="{{ old('short_description', $campaign->short_description ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="A brief summary for cards...">
                        </div>

                        {{-- Full Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Full Description</label>
                            <textarea name="description" rows="5" required
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none"
                                      placeholder="Describe the campaign goals, impact, and why people should donate...">{{ old('description', $campaign->description ?? '') }}</textarea>
                        </div>

                        {{-- Image Upload --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Campaign Image</label>
                            @if(isset($campaign) && $campaign->image)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($campaign->image) }}" class="w-48 h-32 object-cover rounded-xl border">
                                </div>
                            @endif
                            <input type="file" name="image" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                            <p class="text-xs text-gray-400 mt-2">Recommended size: 1200x800px. Max 2MB.</p>
                        </div>

                        {{-- Location (Optional) --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Latitude (Optional)</label>
                            <input type="text" name="lat" value="{{ old('lat', $campaign->lat ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. 30.0444">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Longitude (Optional)</label>
                            <input type="text" name="long" value="{{ old('long', $campaign->long ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                   placeholder="e.g. 31.2357">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                        <a href="{{ route('custom_admin.campaigns.index') }}" class="px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-4 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-gray-200">
                            {{ isset($campaign) ? 'Update Campaign' : 'Create Campaign' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Volunteer Opportunities — Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 class="text-3xl font-extrabold text-gray-900">Volunteer Opportunities</h1>
                <a href="{{ route('custom_admin.schedules.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Opportunity
                </a>
            </div>

            <div class="grid grid-cols-1 gap-8">
                @forelse($schedules as $opportunity)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-bold text-gray-900 truncate">{{ $opportunity->title }}</h2>
                            <p class="text-sm text-gray-500 font-medium">Campaign: {{ $opportunity->campaign->title }}</p>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <span class="block text-sm font-bold text-gray-900">
                                    {{ $opportunity->start_date->format('M d') }} - {{ $opportunity->end_date->format('M d, Y') }}
                                </span>
                                <span class="text-xs text-gray-500 uppercase tracking-wider font-bold">{{ $opportunity->category }} • {{ $opportunity->event_type }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 {{ $opportunity->status === 'open' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-gray-100 text-gray-800 border-gray-200' }} text-xs font-bold rounded-full border uppercase tracking-wider">
                                    {{ $opportunity->status }}
                                </span>
                                <div class="flex gap-2">
                                    <a href="{{ route('custom_admin.schedules.edit', $opportunity->id) }}" class="p-2 text-gray-400 hover:text-primary-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('custom_admin.schedules.destroy', $opportunity->id) }}" method="POST" onsubmit="return confirm('Delete this opportunity?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Location</h3>
                                <p class="text-sm text-gray-700 font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $opportunity->location }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Capacity</h3>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 max-w-[100px]">
                                        @php $percent = $opportunity->max_volunteers > 0 ? min(100, ($opportunity->approved_applications_count / $opportunity->max_volunteers) * 100) : 0; @endphp
                                        <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-700 font-bold">{{ $opportunity->approved_applications_count ?? 0 }}/{{ $opportunity->max_volunteers }}</span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Deadline</h3>
                                <p class="text-sm {{ $opportunity->registration_deadline && $opportunity->registration_deadline->isPast() ? 'text-red-600' : 'text-gray-700' }} font-bold">
                                    {{ $opportunity->registration_deadline ? $opportunity->registration_deadline->format('M d, Y') : 'No deadline' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-50 flex flex-wrap gap-2">
                            @if(is_array($opportunity->required_skills))
                                @foreach($opportunity->required_skills as $skill)
                                <span class="px-2 py-1 bg-gray-50 text-gray-600 text-[10px] font-bold rounded border border-gray-100 uppercase tracking-tight">{{ $skill }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500 italic">
                    No opportunities found. Click "Create Opportunity" to get started.
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

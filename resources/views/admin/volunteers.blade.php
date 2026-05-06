@extends('layouts.app')

@section('title', 'Manage Volunteers')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Volunteer Applications</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @forelse($volunteers as $vol)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-lg">
                                {{ substr($vol->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">{{ $vol->name }}</h3>
                                <p class="text-sm font-medium text-gray-500">{{ $vol->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200">{{ $vol->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Skills & Interests</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($vol->skills as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100 flex gap-3">
                        <form action="{{ route('custom_admin.volunteers.update-status', $vol->id) }}" method="POST" class="flex-1 flex gap-2">
                            @csrf
                            @method('PATCH')
                            @if($vol->status === 'inactive')
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="flex-1 bg-primary-600 text-white py-2.5 rounded-lg text-sm font-bold hover:bg-primary-700 transition shadow-sm">Approve</button>
                            @else
                                <input type="hidden" name="status" value="inactive">
                                <button type="submit" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-50 transition">Deactivate</button>
                            @endif
                        </form>
                    </div>
                </div>
                @empty
                    <div class="col-span-2 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                        <p class="text-gray-500">No volunteer applications found.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-900">Approved Volunteers Roster</h3>
                </div>
                <div class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="font-medium text-lg text-gray-900 mb-1">No active volunteers found</p>
                    <p class="text-sm">Approve applications above to add them to the roster.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

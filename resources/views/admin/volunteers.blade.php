@extends('layouts.app')

@section('title', 'Manage Volunteers')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0 hidden lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-900 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Admin Panel
                    </h3>
                </div>
                <nav class="p-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Dashboard Overview</a>
                    <a href="{{ route('admin.campaigns.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Manage Campaigns</a>
                    <a href="{{ route('admin.donations.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Donations Ledger</a>
                    <a href="{{ route('admin.volunteers.index') }}" class="bg-primary-50 text-primary-700 block px-4 py-3 rounded-lg text-sm font-bold transition">Volunteers</a>
                </nav>
            </div>
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Volunteer Applications</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @foreach([
                    ['name' => 'Alice Cooper', 'email' => 'alice@example.com', 'phone' => '+1 555-0123', 'date' => '2 days ago', 'interests' => ['Event Organization', 'Fundraising'], 'status' => 'Pending'],
                    ['name' => 'David Lee', 'email' => 'david@example.com', 'phone' => '+1 555-0198', 'date' => '5 days ago', 'interests' => ['Field Work'], 'status' => 'Pending'],
                    ['name' => 'Amanda Wright', 'email' => 'amanda@example.com', 'phone' => '+1 555-0245', 'date' => '1 week ago', 'interests' => ['Fundraising', 'Administrative'], 'status' => 'Pending']
                ] as $vol)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-lg">
                                {{ substr($vol['name'], 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-gray-900">{{ $vol['name'] }}</h3>
                                <p class="text-sm font-medium text-gray-500">{{ $vol['email'] }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200">{{ $vol['date'] }}</span>
                    </div>
                    
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Stated Interests</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($vol['interests'] as $interest)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-100">
                                    {{ $interest }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100 flex gap-3">
                        <button class="flex-1 bg-primary-600 text-white py-2.5 rounded-lg text-sm font-bold hover:bg-primary-700 transition shadow-sm">Approve</button>
                        <button class="flex-1 bg-white border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-50 transition">Reject</button>
                    </div>
                </div>
                @endforeach
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

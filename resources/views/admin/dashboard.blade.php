@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Admin Content -->
        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Dashboard Overview</h1>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-2 bg-primary-500"></div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">Total Raised</p>
                    <h3 class="text-4xl font-extrabold text-gray-900">${{ number_format($stats['total_raised'], 2) }}</h3>
                    <p class="text-sm text-green-600 mt-2 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        Live data
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-2 bg-green-500"></div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">Active Campaigns</p>
                    <h3 class="text-4xl font-extrabold text-gray-900">{{ $stats['active_campaigns'] }}</h3>
                    <p class="text-sm text-gray-500 mt-2 font-medium">Out of {{ $stats['total_campaigns'] }} total</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-2 bg-yellow-500"></div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">Total Volunteers</p>
                    <h3 class="text-4xl font-extrabold text-gray-900">{{ $stats['total_volunteers'] }}</h3>
                    <p class="text-sm text-green-600 mt-2 font-medium flex items-center gap-1">
                        {{ $stats['total_volunteer_hours'] }} hours contributed
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-900 text-lg">Recent Donations</h3>
                    <a href="{{ route('custom_admin.donations.index') }}" class="text-sm font-bold text-primary-600 hover:text-primary-800">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Donor</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Campaign</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($recentDonations as $donation)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                                        {{ $donation->user ? substr($donation->user->name, 0, 1) : 'A' }}
                                    </div>
                                    {{ $donation->user ? $donation->user->name : 'Anonymous' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                    {{ $donation->campaign ? $donation->campaign->title : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-gray-900">${{ number_format($donation->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $donation->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $donation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 italic">No recent donations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

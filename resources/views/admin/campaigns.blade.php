@extends('layouts.app')

@section('title', 'Manage Campaigns')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Admin Sidebar -->
        <div class="w-64 flex-shrink-0 hidden lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-900 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Admin Panel
                    </h3>
                </div>
                <nav class="p-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Dashboard Overview</a>
                    <a href="{{ route('admin.campaigns.index') }}" class="bg-primary-50 text-primary-700 block px-4 py-3 rounded-lg text-sm font-bold transition">Manage Campaigns</a>
                    <a href="{{ route('admin.donations.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Donations Ledger</a>
                    <a href="{{ route('admin.volunteers.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Volunteers</a>
                </nav>
            </div>
        </div>

        <!-- Admin Content -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 class="text-3xl font-extrabold text-gray-900">Campaigns</h1>
                <button class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Campaign
                </button>
            </div>

            <x-alert type="success" message="The campaign list has been updated successfully." />

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Campaign</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Progress</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($campaigns as $camp)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-gray-900">{{ $camp->title }}</div>
                                    <div class="text-sm text-gray-500 mt-1">Created {{ $camp->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-between text-xs font-bold mb-1">
                                        <span class="text-primary-600">${{ number_format($camp->current_amount, 2) }}</span>
                                        <span class="text-gray-500">${{ number_format($camp->goal_amount, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 shadow-inner">
                                        <div class="{{ $camp->status === 'completed' ? 'bg-green-500' : 'bg-primary-500' }} h-2 rounded-full" style="width: {{ $camp->goal_amount > 0 ? min(100, ($camp->current_amount / $camp->goal_amount) * 100) : 0 }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($camp->status === 'active')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">{{ ucfirst($camp->status) }}</span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800 border border-gray-200">{{ ucfirst($camp->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.campaigns.edit', $camp->id) }}" class="text-primary-600 hover:text-primary-900 mr-4 font-bold">Edit</a>
                                    <form action="{{ route('admin.campaigns.destroy', $camp->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No campaigns found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($campaigns, 'links'))
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $campaigns->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

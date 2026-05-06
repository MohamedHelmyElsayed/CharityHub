@extends('layouts.app')

@section('title', 'Manage Campaigns')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Admin Content -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 class="text-3xl font-extrabold text-gray-900">Campaigns</h1>
                <a href="{{ route('custom_admin.campaigns.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Campaign
                </a>
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
                                    <a href="{{ route('custom_admin.campaigns.edit', $camp->id) }}" class="text-primary-600 hover:text-primary-900 mr-4 font-bold">Edit</a>
                                    <form action="{{ route('custom_admin.campaigns.destroy', $camp->id) }}" method="POST" class="inline">
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

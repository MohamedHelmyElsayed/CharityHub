@extends('layouts.app')

@section('title', 'Manage Donations')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Donations Ledger</h1>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <form action="{{ route('custom_admin.donations.index') }}" method="GET" class="p-6 border-b border-gray-100 flex flex-wrap gap-4 items-end bg-gray-50">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Search Donor</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 py-2.5 px-4 border" placeholder="Name, email, or TXN ID...">
                    </div>
                    <div class="w-48">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 py-2.5 px-4 border bg-white">
                            <option value="">All Statuses</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-800 transition shadow-md">Filter Results</button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('custom_admin.donations.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-3 underline">Clear</a>
                    @endif
                </form>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Transaction Info</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Donor Details</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($donations as $don)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono font-bold text-gray-900">{{ $don->idempotency_key ?? 'TXN-'.$don->id }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $don->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $don->donor ? $don->donor->name : ($don->user ? $don->user->name : 'Anonymous') }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $don->donor ? $don->donor->email : ($don->user ? $don->user->email : 'N/A') }}</div>
                                    <div class="text-xs font-medium text-primary-600 mt-1">To: {{ $don->campaign ? $don->campaign->title : 'General Fund' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-lg font-extrabold text-gray-900">
                                    ${{ number_format($don->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($don->status === 'completed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                            <svg class="w-3 h-3 mr-1 mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            Completed
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <svg class="w-3 h-3 mr-1 mt-1 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            {{ ucfirst($don->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No donations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($donations, 'links'))
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $donations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

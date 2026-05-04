@extends('layouts.app')

@section('title', 'Manage Donations')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        
        <!-- Sidebar -->
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
                    <a href="{{ route('admin.campaigns.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Manage Campaigns</a>
                    <a href="{{ route('admin.donations.index') }}" class="bg-primary-50 text-primary-700 block px-4 py-3 rounded-lg text-sm font-bold transition">Donations Ledger</a>
                    <a href="{{ route('admin.volunteers.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Volunteers</a>
                </nav>
            </div>
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Donations Ledger</h1>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-100 flex flex-wrap gap-4 items-end bg-gray-50">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Search Donor</label>
                        <input type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 py-2.5 px-4 border" placeholder="Name, email, or TXN ID...">
                    </div>
                    <div class="w-48">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                        <select class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 py-2.5 px-4 border bg-white">
                            <option>All Statuses</option>
                            <option>Completed</option>
                            <option>Pending</option>
                            <option>Failed</option>
                        </select>
                    </div>
                    <button class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-800 transition shadow-md">Filter Results</button>
                </div>
                
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
                            @foreach([
                                ['id' => 'TXN-10293', 'date' => 'Oct 25, 2023', 'name' => 'Sarah Jenkins', 'email' => 'sarah@example.com', 'campaign' => 'Education for All', 'amount' => 50, 'status' => 'Completed'],
                                ['id' => 'TXN-10294', 'date' => 'Oct 25, 2023', 'name' => 'Michael Brown', 'email' => 'michael@example.com', 'campaign' => 'Clean Water Initiative', 'amount' => 250, 'status' => 'Completed'],
                                ['id' => 'TXN-10295', 'date' => 'Oct 24, 2023', 'name' => 'Emily Davis', 'email' => 'emily@example.com', 'campaign' => 'Disaster Relief Fund', 'amount' => 100, 'status' => 'Completed'],
                                ['id' => 'TXN-10296', 'date' => 'Oct 24, 2023', 'name' => 'Robert Wilson', 'email' => 'robert@example.com', 'campaign' => 'Wildlife Conservation', 'amount' => 75, 'status' => 'Pending']
                            ] as $don)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono font-bold text-gray-900">{{ $don['id'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $don['date'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $don['name'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $don['email'] }}</div>
                                    <div class="text-xs font-medium text-primary-600 mt-1">To: {{ $don['campaign'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-lg font-extrabold text-gray-900">
                                    ${{ number_format($don['amount'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($don['status'] === 'Completed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                            <svg class="w-3 h-3 mr-1 mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            Completed
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <svg class="w-3 h-3 mr-1 mt-1 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-500">Total volume on this page: <span class="font-extrabold text-gray-900">$475.00</span></span>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 text-sm font-bold text-gray-400 cursor-not-allowed">Prev</button>
                        <button class="px-3 py-1 text-sm font-bold text-primary-600 hover:text-primary-800">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

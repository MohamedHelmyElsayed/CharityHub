@extends('layouts.app')

@section('title', 'Financial Ledger — Admin')

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
                    <a href="{{ route('custom_admin.dashboard') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Dashboard Overview</a>
                    <a href="{{ route('custom_admin.campaigns.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Manage Campaigns</a>
                    <a href="{{ route('custom_admin.donations.index') }}" class="bg-primary-50 text-primary-700 block px-4 py-3 rounded-lg text-sm font-bold transition">Donations Ledger</a>
                    <a href="{{ route('custom_admin.volunteers.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-4 py-3 rounded-lg text-sm font-semibold transition">Volunteers</a>
                </nav>
            </div>
        </div>

        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Financial Ledger</h1>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <form action="{{ route('custom_admin.ledger') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Type</label>
                            <select name="type" class="w-full border-gray-300 rounded-lg text-sm border p-2 bg-white">
                                <option value="">All Types</option>
                                <option value="payment_success" {{ request('type') == 'payment_success' ? 'selected' : '' }}>Donation</option>
                                <option value="refund_issued" {{ request('type') == 'refund_issued' ? 'selected' : '' }}>Refund</option>
                                <option value="payment_failed" {{ request('type') == 'payment_failed' ? 'selected' : '' }}>Failed Payment</option>
                                <option value="manual_adjustment" {{ request('type') == 'manual_adjustment' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">From</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full border-gray-300 rounded-lg text-sm border p-2 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">To</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full border-gray-300 rounded-lg text-sm border p-2 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Gateway</label>
                            <select name="gateway" class="w-full border-gray-300 rounded-lg text-sm border p-2 bg-white">
                                <option value="">All Gateways</option>
                                <option value="stripe" {{ request('gateway') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="paymob" {{ request('gateway') == 'paymob' ? 'selected' : '' }}>PayMob</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition">Filter</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Campaign/Donor</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Gateway</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-bold text-gray-900">
                                    {{ $log->gateway_transaction_id ?? 'Log-'.$log->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase tracking-widest border {{ in_array($log->transaction_type, ['payment_success', 'subscription_renewed']) ? 'bg-green-50 text-green-700 border-green-100' : ($log->transaction_type === 'refund_issued' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-gray-50 text-gray-700 border-gray-100') }}">
                                        {{ str_replace('_', ' ', $log->transaction_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $log->campaign?->title ?? 'General' }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->donor?->name ?? 'External' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold {{ in_array($log->transaction_type, ['payment_success', 'subscription_renewed']) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ in_array($log->transaction_type, ['payment_success', 'subscription_renewed']) ? '+' : '-' }}${{ number_format($log->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-bold {{ $log->gateway === 'stripe' ? 'text-indigo-600' : 'text-orange-600' }}">
                                        {{ ucfirst($log->gateway ?? 'Manual') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-medium">
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">No transactions recorded.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

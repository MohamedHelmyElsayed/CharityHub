@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50/50 py-12" x-data="{ showCancelModal: false, activeSubscriptionId: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Welcome back, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-slate-500 font-medium mt-2">Here's an overview of your impact and contributions.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('donate') }}" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    New Donation
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @foreach([
                ['label' => 'Total Donated', 'value' => 'EGP ' . number_format($stats['total_donated'], 2), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'blue'],
                ['label' => 'Impact Points', 'value' => number_format($stats['impact_points']), 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                ['label' => 'Donations', 'value' => $stats['donation_count'], 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color' => 'rose'],
                ['label' => 'Certificates', 'value' => $stats['certificates_count'], 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'color' => 'emerald'],
            ] as $stat)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $stat['color'] }}-50 flex items-center justify-center text-{{ $stat['color'] }}-600 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                    </div>
                </div>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stat['value'] }}</h3>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Recent Activity --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900">Recent Donations</h2>
                        <span class="text-sm font-medium text-slate-400">{{ $recentDonations->count() }} total records</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Campaign</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentDonations as $donation)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                {{ substr($donation->campaign->title, 0, 1) }}
                                            </div>
                                            <span class="font-bold text-slate-900 text-sm truncate max-w-[200px]">{{ $donation->campaign->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="font-bold text-slate-900 text-sm">EGP {{ number_format($donation->amount, 2) }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        @php
                                            $statusColors = [
                                                'completed' => 'bg-emerald-100 text-emerald-700',
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'failed' => 'bg-red-100 text-red-700',
                                            ];
                                            $color = $statusColors[$donation->status] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $color }}">
                                            {{ $donation->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-slate-500 font-medium text-sm">{{ $donation->created_at->format('M d, Y') }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <p class="text-slate-400 font-bold">No donations yet</p>
                                            <a href="{{ route('donate') }}" class="text-blue-600 font-bold text-sm mt-2 hover:underline">Start making a difference</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Recurring Subscriptions --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900">Recurring Plans</h2>
                        <span class="text-sm font-medium text-slate-400">{{ collect($subscriptions ?? [])->count() }} active plans</span>
                    </div>
                    <div class="p-8 space-y-6">
                        @forelse($subscriptions ?? [] as $sub)
                        <div class="border border-slate-100 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-lg">{{ $sub->campaign ? $sub->campaign->title : 'General Donation' }}</h3>
                                    <p class="text-sm font-bold text-slate-500 mt-1">EGP {{ number_format($sub->amount, 2) }} / month</p>
                                    @if($sub->isActive())
                                        <p class="text-xs text-emerald-600 font-bold mt-1 uppercase tracking-wider">Active • Next Billing: {{ $sub->next_billing_date ? $sub->next_billing_date->format('M d, Y') : 'N/A' }}</p>
                                    @else
                                        <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">Cancelled</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3 self-end md:self-auto">
                                @if($sub->isActive())
                                    <button @click="showCancelModal = true; activeSubscriptionId = {{ $sub->id }}" class="px-5 py-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 font-bold text-sm rounded-xl transition-colors border border-rose-100">
                                        Cancel Plan
                                    </button>
                                @else
                                    <form action="{{ route('donate.checkout') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="amount" value="{{ $sub->amount }}">
                                        @if($sub->campaign_id)
                                            <input type="hidden" name="campaign_id" value="{{ $sub->campaign_id }}">
                                        @else
                                            <input type="hidden" name="campaign_id" value="1">
                                        @endif
                                        <input type="hidden" name="type" value="recurring">
                                        <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                                        <input type="hidden" name="gdpr_consent" value="1">
                                        <input type="hidden" name="anonymous" value="0">
                                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
                                        <button type="submit" class="px-5 py-2.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 font-bold text-sm rounded-xl transition-colors border border-emerald-100">
                                            Renew Plan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-slate-500 font-bold">No recurring plans active.</p>
                            <a href="{{ route('donate') }}" class="text-indigo-600 font-bold text-sm mt-2 inline-block hover:underline">Start a monthly plan</a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Certificates --}}
            <div class="space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        My Certificates
                    </h2>
                    
                    <div class="space-y-4">
                        @forelse($certificates as $cert)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between group hover:border-emerald-200 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 line-clamp-1">{{ $cert->campaign->title }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $cert->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('certificates.download', $cert->certificate_uuid) }}" class="w-8 h-8 rounded-full bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:border-emerald-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <p class="text-sm font-medium text-slate-400 italic">No certificates available yet.</p>
                        </div>
                        @endforelse
                    </div>

                    @if($certificates->count() > 0)
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-relaxed">
                            Certificates are automatically generated for all completed donations. You can use these to verify your impact on the ledger.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal (Alpine.js) -->
    <div 
        x-show="showCancelModal" 
        class="fixed inset-0 z-[100] overflow-y-auto" 
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showCancelModal = false">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div class="bg-white p-8">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-50 mb-6">
                            <svg class="h-8 w-8 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Cancel Recurring Plan?</h3>
                        <div class="mt-4">
                            <p class="text-slate-500 font-medium leading-relaxed text-sm">
                                By cancelling this recurring donation, you are stopping future support for this campaign. Your previous donations will remain processed.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50/80 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-100">
                    <form :action="'/donor/subscriptions/' + activeSubscriptionId + '/cancel'" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-rose-600 text-sm font-bold text-white hover:bg-rose-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                            Yes, Cancel
                        </button>
                    </form>
                    <button @click="showCancelModal = false" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-200 px-6 py-3 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                        Keep Supporting
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Donor Dashboard')

@section('content')
<div class="bg-slate-50 min-h-screen py-12" x-data="{ showCancelModal: false, activeSubscriptionId: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Impact Dashboard</h1>
                <p class="mt-2 text-slate-500 font-medium">Tracking your journey of making the world a better place.</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('donate') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-xl shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all transform hover:scale-105">
                    Start New Donation
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Stats -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Summary -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 overflow-hidden relative">
                    <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-primary-50 rounded-full opacity-50"></div>
                    <div class="relative z-10">
                        <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-primary-200 mb-6">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900">{{ auth()->user()->name }}</h2>
                        <p class="text-slate-500 text-sm font-medium">{{ auth()->user()->email }}</p>
                        <div class="mt-6 pt-6 border-t border-slate-50">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Member Since</span>
                            <p class="text-slate-900 font-bold mt-1">{{ auth()->user()->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Lifetime Giving Stats -->
                <div class="bg-slate-900 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-slate-800 rounded-full"></div>
                    <div class="relative z-10">
                        <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">Lifetime Impact</p>
                        <h3 class="text-4xl font-black mt-3">${{ number_format($donations->where('status', 'completed')->sum('amount'), 2) }}</h3>
                        <p class="text-slate-500 text-xs mt-4 leading-relaxed">Your contributions have helped change lives across multiple initiatives.</p>
                    </div>
                </div>

                <!-- Active Recurring Count -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">Active Plans</p>
                            <h4 class="text-3xl font-black mt-1 text-slate-900">{{ $subscriptions->where('status', 'active')->count() }}</h4>
                        </div>
                        <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-3 space-y-10">
                <!-- Active Subscriptions -->
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Active Recurring Donations</h2>
                    </div>

                    @forelse($subscriptions as $sub)
                    <div class="group bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden mb-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                        <div class="p-8 md:p-10">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex items-center space-x-6">
                                    <div class="h-20 w-20 rounded-2xl bg-slate-50 border border-slate-100 flex-shrink-0 overflow-hidden">
                                        @if($sub->campaign->image_url)
                                            <img src="{{ $sub->campaign->image_url }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-slate-100 text-slate-400 font-black">CH</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-2xl font-black text-slate-900">{{ $sub->campaign->title }}</h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $sub->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ $sub->status }}
                                            </span>
                                        </div>
                                        <p class="text-slate-500 font-bold mt-1">${{ number_format($sub->amount, 2) }} <span class="text-slate-400 font-medium">per {{ $sub->billing_interval }}</span></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 self-end md:self-auto">
                                    @if($sub->isActive())
                                    <button @click="showCancelModal = true; activeSubscriptionId = {{ $sub->id }}" class="px-5 py-2.5 rounded-xl border-2 border-rose-50 text-rose-600 font-bold text-sm hover:bg-rose-50 hover:border-rose-100 transition-all">
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
                                        <button type="submit" class="px-5 py-2.5 rounded-xl border-2 border-emerald-50 text-emerald-600 font-bold text-sm hover:bg-emerald-50 hover:border-emerald-100 transition-all">
                                            Renew Plan
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ $sub->campaign ? route('campaigns.show', $sub->campaign) : '#' }}" class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-700 font-bold text-sm hover:bg-slate-100 transition-all">
                                        Details
                                    </a>
                                </div>
                            </div>

                            <div class="mt-10 grid grid-cols-2 md:grid-cols-4 gap-8 py-8 border-t border-slate-50">
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Next Billing</p>
                                    <p class="text-slate-900 font-black mt-1">{{ $sub->next_billing_date ? $sub->next_billing_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Billing Method</p>
                                    <p class="text-slate-900 font-black mt-1 capitalize">{{ $sub->gateway }} Card</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Total Donated</p>
                                    <p class="text-slate-900 font-black mt-1">${{ number_format($sub->donations->where('status', 'completed')->sum('amount'), 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Member Since</p>
                                    <p class="text-slate-900 font-black mt-1">{{ $sub->created_at->format('M Y') }}</p>
                                </div>
                            </div>

                            <!-- Mini Timeline -->
                            <div class="mt-4 pt-6 border-t border-slate-50">
                                <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-4">Recent Activity</p>
                                <div class="flex items-center space-x-2">
                                    @foreach($sub->donations->sortByDesc('created_at')->take(5) as $history)
                                        <div class="group/dot relative">
                                            <div class="h-3 w-3 rounded-full {{ $history->status === 'completed' ? 'bg-emerald-500' : 'bg-rose-500' }} border-2 border-white shadow-sm"></div>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max hidden group-hover/dot:block bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded shadow-xl z-20">
                                                {{ $history->created_at->format('M d') }}: ${{ number_format($history->amount, 0) }}
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($sub->donations->count() > 5)
                                        <span class="text-[10px] text-slate-400 font-bold">+{{ $sub->donations->count() - 5 }} more</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border-2 border-dashed border-slate-200 p-16 text-center">
                        <div class="h-20 w-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">No recurring donations found</h3>
                        <p class="text-slate-500 mt-2 max-w-xs mx-auto">You haven't set up any monthly plans yet. Recurring donations provide the stable support we need to make long-term impact.</p>
                        <a href="{{ route('donate') }}" class="mt-8 inline-flex items-center px-8 py-3 rounded-2xl bg-primary-600 text-white font-black hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">
                            Create Your First Plan
                        </a>
                    </div>
                    @endforelse
                </section>

                <!-- Full Giving History -->
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Recent Giving History</h2>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-50">
                                <thead>
                                    <tr class="bg-slate-50/30">
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Transaction</th>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Amount</th>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($donations as $donation)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex items-center space-x-4">
                                                <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                                                    @if($donation->type === 'recurring')
                                                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-black text-slate-900">{{ $donation->campaign->title }}</div>
                                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $donation->type }} donation</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="text-sm font-black text-slate-900">${{ number_format($donation->amount, 2) }}</div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $donation->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : ($donation->status === 'failed' ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-600') }}">
                                                {{ $donation->status }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="text-xs text-slate-500 font-medium">{{ $donation->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-8 py-6 whitespace-nowrap text-right">
                                            @if($donation->certificate_path)
                                            <a href="{{ Storage::url($donation->certificate_path) }}" target="_blank" class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600 transition-all border border-slate-100">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                                </svg>
                                            </a>
                                            @else
                                            <div class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-slate-50 text-slate-200">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-16 text-center text-slate-400 font-medium italic">No transactions found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal (Professional Alpine.js) -->
    <div 
        x-show="showCancelModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
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
                class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div class="bg-white p-10">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 mb-6">
                            <svg class="h-10 w-10 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900">Are you sure?</h3>
                        <div class="mt-4">
                            <p class="text-slate-500 font-medium leading-relaxed">
                                By cancelling this recurring donation, you are stopping future support for this campaign. Your previous donations will remain processed and the impact you've already made will last forever.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50/50 px-10 py-8 flex flex-col sm:flex-row-reverse gap-4">
                    <form :action="'/donor/subscriptions/' + activeSubscriptionId + '/cancel'" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-lg shadow-rose-100 px-8 py-4 bg-rose-600 text-base font-black text-white hover:bg-rose-700 transition-all focus:outline-none">
                            Yes, Cancel Plan
                        </button>
                    </form>
                    <button @click="showCancelModal = false" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-2xl border border-slate-200 px-8 py-4 bg-white text-base font-black text-slate-700 hover:bg-slate-50 transition-all focus:outline-none">
                        Keep Supporting
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Shadows & Gradients */
    .shadow-xl {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
    }
    .shadow-2xl {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
    }
</style>
@endsection

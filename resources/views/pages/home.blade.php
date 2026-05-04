@extends('layouts.app')

@section('title', 'Home')

@section('content')
{{-- Hero Section --}}
<section class="relative bg-white overflow-hidden border-b border-slate-100">
    {{-- Abstract Background Elements --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-[800px] h-[800px] rounded-full bg-gradient-to-br from-blue-50 to-indigo-50/50 blur-3xl opacity-70"></div>
        <div class="absolute top-40 -left-20 w-[500px] h-[500px] rounded-full bg-gradient-to-tr from-sky-50 to-blue-50/30 blur-3xl opacity-60"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-24 lg:pt-40 lg:pb-32">
        <div class="text-center max-w-4xl mx-auto space-y-10">
            <div class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 shadow-sm rounded-full text-slate-600 text-sm font-semibold tracking-wide hover:shadow-md transition-shadow">
                <span class="flex h-2 w-2 relative mr-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Trusted by 10,000+ modern donors
            </div>

            <h1 class="text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 leading-[1.1]">
                Empower Change, <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Transform Lives.</span>
            </h1>

            <p class="text-xl text-slate-500 leading-relaxed max-w-2xl mx-auto font-medium">
                The next-generation philanthropy platform. Complete transparency, immutable audit trails, and real-time impact tracking for every single donation.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-5 pt-4">
                <a href="{{ route('donate') }}"
                   class="w-full sm:w-auto px-8 py-4 bg-slate-900 text-white font-semibold rounded-2xl hover:bg-blue-600 shadow-xl shadow-slate-900/10 hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300">
                    Make a Donation
                </a>
                <a href="{{ route('campaigns.index') }}"
                   class="w-full sm:w-auto px-8 py-4 bg-white border border-slate-200 text-slate-700 font-semibold rounded-2xl hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all duration-300">
                    Explore Campaigns
                </a>
            </div>

            <div class="pt-10 flex flex-wrap items-center justify-center gap-8 text-sm font-semibold text-slate-400">
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Stripe Protected</div>
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Verified Certificates</div>
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Real-time Ledger</div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="relative -mt-16 z-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 lg:p-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 divide-x divide-slate-100">
            @php
                $statItems = [
                    ['value' => 'EGP '.number_format($stats['total_raised'] ?? 0, 0), 'label' => 'Total Raised', 'desc' => 'Across all campaigns'],
                    ['value' => number_format($stats['total_donors'] ?? 0), 'label' => 'Active Donors', 'desc' => 'Making an impact'],
                    ['value' => number_format($stats['active_campaigns'] ?? 0), 'label' => 'Live Campaigns', 'desc' => 'Currently funding'],
                    ['value' => number_format($stats['total_donations'] ?? 0), 'label' => 'Total Donations', 'desc' => 'Verified on ledger'],
                ];
            @endphp
            @foreach($statItems as $index => $stat)
            <div class="{{ $index > 0 ? 'pl-8' : '' }}">
                <div class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ $stat['value'] }}</div>
                <div class="text-sm font-bold text-blue-600 mt-2">{{ $stat['label'] }}</div>
                <div class="text-xs font-medium text-slate-400 mt-1">{{ $stat['desc'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Active Campaigns Section --}}
<section class="py-32 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div class="max-w-2xl">
                <span class="text-blue-600 font-bold text-sm uppercase tracking-wider mb-3 block">Featured Causes</span>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">Campaigns that need your help today.</h2>
            </div>
            <a href="{{ route('campaigns.index') }}"
               class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors group">
                View all campaigns
                <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($campaigns as $campaign)
                @include('components.campaign-card', ['campaign' => $campaign])
            @empty
                <div class="col-span-3 text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
                    <p class="text-lg font-medium text-slate-500">No active campaigns at the moment. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="py-32 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <span class="text-blue-600 font-bold text-sm uppercase tracking-wider mb-3 block">Trust by Design</span>
            <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">A transparent approach to giving.</h2>
            <p class="text-lg text-slate-500 font-medium">We've engineered trust into every step of the process, ensuring you always know exactly where your money goes.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
            {{-- Connecting Line for Desktop --}}
            <div class="hidden md:block absolute top-12 left-[15%] right-[15%] h-0.5 bg-gradient-to-r from-blue-100 via-indigo-100 to-blue-100 -z-10"></div>
            
            @php
            $steps = [
                ['n'=>'01','title'=>'Select a Campaign','desc'=>'Find a vetted cause. Every campaign is strictly verified before publishing.','icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                ['n'=>'02','title'=>'Donate Securely','desc'=>'Processed safely via Stripe. Your data is encrypted and never stored by us.','icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['n'=>'03','title'=>'Track Impact','desc'=>'Receive a verifiable certificate and track the campaign\'s real-world progress.','icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ];
            @endphp
            
            @foreach($steps as $step)
            <div class="relative bg-white pt-8 group">
                <div class="w-24 h-24 mx-auto bg-blue-50 rounded-3xl flex items-center justify-center mb-8 transform group-hover:-translate-y-2 transition-transform duration-300 shadow-sm border border-blue-100">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <div class="text-center">
                    <div class="text-sm font-black text-slate-300 mb-2">{{ $step['n'] }}</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $step['title'] }}</h3>
                    <p class="text-slate-500 font-medium leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Banner --}}
<section class="py-32 bg-slate-900 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-blue-600/30 to-indigo-600/30 blur-3xl rounded-full transform translate-x-1/2 -translate-y-1/4"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-gradient-to-tr from-sky-500/20 to-blue-500/20 blur-3xl rounded-full transform -translate-x-1/2 translate-y-1/4"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto text-center px-4 sm:px-6">
        <h2 class="text-5xl font-extrabold text-white mb-6 tracking-tight">Ready to make your mark?</h2>
        <p class="text-xl text-slate-300 font-medium mb-12 max-w-2xl mx-auto">Join a modern platform that brings accountability and design to charitable giving.</p>
        <div class="flex flex-col sm:flex-row gap-5 justify-center">
            <a href="{{ route('donate') }}" class="px-8 py-4 bg-white text-slate-900 font-bold rounded-2xl hover:bg-slate-50 transform hover:-translate-y-1 transition-all duration-300 shadow-xl">
                Start Donating
            </a>
            <a href="{{ route('volunteer.index') }}" class="px-8 py-4 bg-slate-800 text-white border border-slate-700 font-bold rounded-2xl hover:bg-slate-700 hover:border-slate-600 transform hover:-translate-y-1 transition-all duration-300">
                Become a Volunteer
            </a>
        </div>
    </div>
</section>
@endsection

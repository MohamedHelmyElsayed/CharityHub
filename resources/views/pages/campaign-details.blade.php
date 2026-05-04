@extends('layouts.app')

@section('title', 'Campaign Details')

@section('content')
@php
    $campaign = [
        'title' => 'Clean Water Initiative for Rural Villages',
        'description' => "Access to clean water is a basic human right, yet thousands of families in rural areas still walk miles every day to fetch water that is often unsafe to drink. Our initiative aims to drill deep-water wells and install solar-powered pumps in 10 villages, providing a sustainable and clean water source for over 5,000 people. \n\nWith your support, we can eradicate waterborne diseases, improve sanitation, and allow children—especially girls—to attend school instead of spending their days collecting water. Every dollar brings us closer to a healthier, brighter future for these communities.\n\nJoin us in our mission to change lives, one well at a time. The impact of your donation will last for generations.",
        'goal' => 50000,
        'raised' => 32500,
        'image' => 'https://images.unsplash.com/photo-1541888001694-0f36792cdba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        'donors' => 458,
        'days_left' => 12
    ];
@endphp

<div class="bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Link -->
        <a href="{{ route('campaigns.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-primary-600 mb-6 transition">
            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Campaigns
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="relative h-96">
                        <img src="{{ $campaign['image'] }}" alt="{{ $campaign['title'] }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <h1 class="absolute bottom-6 left-8 right-8 text-3xl md:text-4xl font-extrabold text-white leading-tight">
                            {{ $campaign['title'] }}
                        </h1>
                    </div>
                    
                    <div class="p-8">
                        <div class="flex items-center space-x-3 text-sm font-semibold mb-6 border-b border-gray-100 pb-6">
                            <span class="bg-primary-50 text-primary-700 px-3 py-1 rounded-full border border-primary-100">Environment</span>
                            <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full border border-green-100">Health</span>
                        </div>
                        
                        <div class="prose max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($campaign['description'])) !!}
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Organizer</h3>
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name=Charity+Hub&background=0ea5e9&color=fff" alt="Organizer" class="w-12 h-12 rounded-full mr-4">
                                <div>
                                    <p class="font-bold text-gray-900">CharityHub Organization</p>
                                    <p class="text-sm text-gray-500">Verified Non-profit &bull; 15 Campaigns</p>
                                </div>
                                <button class="ml-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-lg transition">Contact</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Donation Widget -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 sticky top-24">
                    <div class="mb-8">
                        <div class="flex items-end mb-2">
                            <span class="text-4xl font-extrabold text-gray-900">${{ number_format($campaign['raised']) }}</span>
                            <span class="text-lg text-gray-500 ml-2 mb-1">raised of ${{ number_format($campaign['goal']) }}</span>
                        </div>
                        <x-progress-bar :goal="$campaign['goal']" :raised="$campaign['raised']" />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-center mb-8">
                        <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl">
                            <span class="block text-2xl font-bold text-gray-900">{{ $campaign['donors'] }}</span>
                            <span class="text-sm font-medium text-gray-500">Donors</span>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl">
                            <span class="block text-2xl font-bold text-gray-900">{{ $campaign['days_left'] }}</span>
                            <span class="text-sm font-medium text-gray-500">Days Left</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('donate') }}" class="w-full flex items-center justify-center px-8 py-4 border border-transparent text-xl font-bold rounded-xl text-white bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition transform hover:-translate-y-1">
                        Donate Now
                    </a>
                    
                    <div class="mt-6 flex items-center justify-center text-sm font-medium text-gray-500 gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        All donations are secure and encrypted
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-6 text-lg">Recent Donations</h3>
                        <ul class="space-y-5">
                            @foreach([['name' => 'Anonymous', 'amount' => 100, 'time' => '2 mins ago'], ['name' => 'Sarah J.', 'amount' => 50, 'time' => '1 hour ago'], ['name' => 'Michael B.', 'amount' => 250, 'time' => '3 hours ago']] as $donation)
                            <li class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-primary-50 border border-primary-100 rounded-full p-3">
                                        <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-gray-900">{{ $donation['name'] }}</p>
                                        <div class="flex items-center text-sm font-medium text-gray-500">
                                            <span>${{ $donation['amount'] }}</span>
                                            <span class="mx-2">&bull;</span>
                                            <span>{{ $donation['time'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <button class="w-full mt-6 py-2 border border-gray-200 text-gray-600 font-medium rounded-lg hover:bg-gray-50 transition">See all</button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

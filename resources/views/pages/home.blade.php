@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32 pt-20">
            <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                <polygon points="50,0 100,0 50,100 0,100" />
            </svg>
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Empower change and</span>
                        <span class="block text-primary-600 xl:inline">transform lives today</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Join CharityHub's global community of donors and volunteers. Discover impactful campaigns, contribute directly, and track the difference you make in the world.
                    </p>
                    <div class="mt-8 sm:flex sm:justify-center lg:justify-start gap-4">
                        <div class="rounded-md shadow">
                            <a href="{{ route('campaigns.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-lg text-white bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg transition transform hover:-translate-y-1">
                                Browse Campaigns
                            </a>
                        </div>
                        <div class="mt-3 sm:mt-0">
                            <a href="{{ route('volunteer.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-gray-200 text-base font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg transition transform hover:-translate-y-1">
                                Become a Volunteer
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Volunteers working together happily">
    </div>
</div>

<!-- Stats Section -->
<div class="bg-primary-600">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                Trusted by thousands worldwide
            </h2>
            <p class="mt-3 text-xl text-primary-100 sm:mt-4">
                Together, we are making a measurable impact.
            </p>
        </div>
        <dl class="mt-10 text-center sm:max-w-3xl sm:mx-auto sm:grid sm:grid-cols-3 sm:gap-8">
            <div class="flex flex-col">
                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-primary-100">Campaigns Funded</dt>
                <dd class="order-1 text-5xl font-extrabold text-white">400+</dd>
            </div>
            <div class="flex flex-col mt-10 sm:mt-0">
                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-primary-100">Dollars Raised</dt>
                <dd class="order-1 text-5xl font-extrabold text-white">$2.5M</dd>
            </div>
            <div class="flex flex-col mt-10 sm:mt-0">
                <dt class="order-2 mt-2 text-lg leading-6 font-medium text-primary-100">Active Volunteers</dt>
                <dd class="order-1 text-5xl font-extrabold text-white">1,200</dd>
            </div>
        </dl>
    </div>
</div>

<!-- Featured Campaigns -->
<div class="bg-gray-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl tracking-tight">Urgent Causes</h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Support our most critical campaigns right now and make an immediate impact.
            </p>
        </div>

        @php
        $campaigns = [
            [
                'title' => 'Clean Water Initiative in Rural Areas',
                'description' => 'Help us build sustainable water wells to provide access to clean and safe drinking water for thousands of families.',
                'goal' => 50000,
                'raised' => 32500,
                'image' => 'https://images.unsplash.com/photo-1541888001694-0f36792cdba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'title' => 'Education for Underprivileged Children',
                'description' => 'Providing school supplies, books, and scholarships to children in poverty to ensure they get the education they deserve.',
                'goal' => 25000,
                'raised' => 18000,
                'image' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ],
            [
                'title' => 'Disaster Relief Fund & Shelter',
                'description' => 'Emergency response fund to provide immediate shelter, food, and medical supplies to victims of recent natural disasters.',
                'goal' => 100000,
                'raised' => 85000,
                'image' => 'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
            ]
        ];
        @endphp

        <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($campaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
        
        <div class="mt-12 text-center">
            <a href="{{ route('campaigns.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                View All Campaigns
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</div>
@endsection

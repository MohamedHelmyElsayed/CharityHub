@extends('layouts.app')

@section('title', 'Explore Campaigns')

@section('content')
<div class="bg-white py-12 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-extrabold leading-tight text-gray-900 sm:text-4xl">
                    Discover Campaigns
                </h2>
                <p class="mt-3 text-lg text-gray-500">Find causes that resonate with you and start making a difference today.</p>
            </div>
            <div class="mt-6 flex md:mt-0 md:ml-4 gap-4">
                <div class="relative">
                    <input type="text" placeholder="Search causes..." class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg shadow-sm border">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <select class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg shadow-sm border font-medium text-gray-700 bg-white">
                    <option>All Categories</option>
                    <option>Education</option>
                    <option>Medical & Health</option>
                    <option>Environment</option>
                    <option>Emergency Relief</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-gray-50">
    @php
    $campaigns = [
        [
            'title' => 'Clean Water Initiative',
            'description' => 'Help us build wells in rural communities to provide access to clean and safe drinking water for thousands of families.',
            'goal' => 50000,
            'raised' => 32500,
            'image' => 'https://images.unsplash.com/photo-1541888001694-0f36792cdba5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'title' => 'Education for All',
            'description' => 'Providing school supplies, books, and uniforms to underprivileged children to ensure they get the education they deserve.',
            'goal' => 25000,
            'raised' => 18000,
            'image' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'title' => 'Disaster Relief Fund',
            'description' => 'Emergency response fund to provide immediate shelter, food, and medical supplies to victims of natural disasters.',
            'goal' => 100000,
            'raised' => 85000,
            'image' => 'https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'title' => 'Help Children Combat Hunger',
            'description' => 'Providing nutritious meals to children in poverty-stricken areas. Your donation can feed a child for a whole month.',
            'goal' => 15000,
            'raised' => 5000,
            'image' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'title' => 'Wildlife Conservation Project',
            'description' => 'Protecting endangered species and preserving their natural habitats from deforestation and poaching.',
            'goal' => 40000,
            'raised' => 12000,
            'image' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'title' => 'Medical Assistance for Seniors',
            'description' => 'Covering essential medical treatments and medicines for elderly individuals who cannot afford healthcare.',
            'goal' => 30000,
            'raised' => 28500,
            'image' => 'https://images.unsplash.com/photo-1516826957135-700ede19c6ce?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ]
    ];
    @endphp

    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach($campaigns as $campaign)
            <x-campaign-card :campaign="$campaign" />
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-16 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </a>
            <a href="#" aria-current="page" class="z-10 bg-primary-50 border-primary-500 text-primary-600 relative inline-flex items-center px-4 py-2 border text-sm font-bold">1</a>
            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">2</a>
            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">3</a>
            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">8</a>
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </a>
        </nav>
    </div>
</div>
@endsection

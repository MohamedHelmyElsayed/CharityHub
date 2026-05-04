@extends('layouts.app')

@section('title', 'Explore Campaigns')

@section('content')
<div class="bg-slate-50 min-h-screen py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="max-w-3xl mb-16">
            <span class="text-blue-600 font-bold text-sm uppercase tracking-wider mb-3 block">Discover Causes</span>
            <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">Support verified campaigns making a real impact.</h1>
            <p class="text-lg text-slate-500 font-medium">Browse our active initiatives below. Every donation is tracked securely on our ledger, ensuring complete transparency.</p>
        </div>

        {{-- Filters (Placeholder UI for future expansion) --}}
        <div class="flex flex-col sm:flex-row gap-4 mb-10 pb-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-900">Filter by:</span>
                <span class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-full cursor-pointer">All Causes</span>
                <span class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-full hover:bg-slate-50 cursor-pointer transition-colors">Emergency</span>
                <span class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-full hover:bg-slate-50 cursor-pointer transition-colors">Education</span>
            </div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($campaigns as $campaign)
                @include('components.campaign-card', ['campaign' => $campaign])
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-32 bg-white rounded-3xl border border-dashed border-slate-300">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No campaigns found</h3>
                    <p class="text-slate-500 font-medium">There are currently no active campaigns to display.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($campaigns->hasPages())
        <div class="mt-16">
            {{ $campaigns->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

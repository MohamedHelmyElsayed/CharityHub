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

        {{-- Live Search Component --}}
        @livewire('campaign-search')
    </div>
</div>
@endsection

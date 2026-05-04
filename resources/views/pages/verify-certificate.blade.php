@extends('layouts.app')

@section('title', 'Verify Donation Certificate')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-white to-blue-50 flex items-center justify-center py-16 px-4">
    <div class="max-w-lg w-full">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            {{-- Header --}}
            <div class="{{ $status === 'revoked' ? 'bg-red-500' : 'bg-gradient-to-r from-blue-700 to-blue-600' }} p-8 text-white text-center">
                @if($status === 'revoked')
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h1 class="text-2xl font-bold">Certificate Revoked</h1>
                    <p class="opacity-80 mt-1 text-sm">This certificate has been revoked.</p>
                @else
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <h1 class="text-2xl font-bold">Certificate Verified ✓</h1>
                    <p class="opacity-80 mt-1 text-sm">This donation certificate is authentic.</p>
                @endif
            </div>

            {{-- Details --}}
            <div class="p-8 space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-500 text-sm font-medium">Donor</span>
                    <span class="font-semibold text-gray-800">{{ $maskedName }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-500 text-sm font-medium">Amount</span>
                    <span class="font-bold text-blue-600 text-lg">EGP {{ number_format($amount, 2) }}</span>
                </div>
                @if($campaign)
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-500 text-sm font-medium">Campaign</span>
                    <span class="font-semibold text-gray-800 text-right max-w-[60%]">{{ $campaign->title }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-500 text-sm font-medium">Date Issued</span>
                    <span class="font-semibold text-gray-800">{{ $issuedAt->format('M j, Y') }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-500 text-sm font-medium">Status</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $status === 'revoked' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>
            </div>

            <div class="px-8 pb-8">
                <p class="text-xs text-gray-400 text-center">Certificate ID: {{ $certificate->uuid }}</p>
                <a href="{{ route('home') }}"
                   class="mt-4 block w-full text-center px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                    Make Your Own Donation
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', $campaign->title)
@section('og_title', $campaign->og_title ?? $campaign->title)
@section('og_description', $campaign->og_description ?? $campaign->short_description)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Cover Image --}}
            @if($campaign->cover_image)
                <img src="{{ Storage::url($campaign->cover_image) }}" alt="{{ $campaign->title }}"
                     class="w-full h-72 object-cover rounded-2xl shadow-md">
            @endif

            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full">{{ ucfirst($campaign->status) }}</span>
                    @if($campaign->deadline)
                        <span class="text-sm text-gray-400">Ends {{ $campaign->deadline->format('M j, Y') }}</span>
                    @endif
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $campaign->title }}</h1>
                <div class="prose prose-lg text-gray-600 max-w-none">
                    {!! nl2br(e($campaign->description)) !!}
                </div>
            </div>

            {{-- Social Sharing --}}
            <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Share this campaign</h3>
                <div class="flex flex-wrap gap-3">
                    @php $shareUrl = $campaign->share_url; $shareTitle = urlencode($campaign->title); @endphp
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ $shareTitle }}" target="_blank"
                       class="px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-medium hover:bg-sky-600 transition-colors">
                        Twitter/X
                    </a>
                    <a href="https://wa.me/?text={{ $shareTitle }}%20{{ urlencode($shareUrl) }}" target="_blank"
                       class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">
                        WhatsApp
                    </a>
                    <button onclick="navigator.clipboard.writeText('{{ $shareUrl }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy Link', 2000)"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                        Copy Link
                    </button>
                </div>
            </div>

            {{-- Live Donation Feed (Livewire) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Donations</h3>
                @livewire('donation-feed', ['campaignId' => $campaign->id])
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Progress Card (Livewire) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
                @livewire('campaign-progress', ['campaignId' => $campaign->id])

                @if($campaign->status === 'ended')
                    <div class="mt-6 p-4 bg-slate-100 border border-slate-200 rounded-xl text-center">
                        <div class="flex items-center justify-center w-10 h-10 mx-auto bg-emerald-100 text-emerald-600 rounded-full mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h4 class="font-bold text-slate-900">Goal Reached!</h4>
                        <p class="text-sm text-slate-500 mt-1">This campaign is fully funded and closed to new donations.</p>
                    </div>
                @elseif(!(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEmployee())))
                    <a href="{{ route('donate') }}?campaign_id={{ $campaign->id }}"
                       class="block w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white text-center font-bold rounded-xl transition-all shadow-md hover:shadow-emerald-200 mt-6">
                        Donate to This Campaign
                    </a>
                @endif
            </div>

            {{-- Impact Reports --}}
            @if($impactReports->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Impact Reports</h3>
                <ul class="space-y-3">
                    @foreach($impactReports as $report)
                    <li>
                        <a href="{{ route('impact.show', $report) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 transition-colors">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div>
                                <div class="font-medium text-gray-800 text-sm">{{ $report->title }}</div>
                                <div class="text-xs text-gray-400">{{ $report->beneficiary_count }} beneficiaries</div>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

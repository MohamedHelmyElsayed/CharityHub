@extends('layouts.app')

@section('title', 'Donation History')

@section('content')
<div class="min-h-screen bg-slate-50/50 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Donation History</h1>
                <p class="text-slate-500 font-medium mt-1">A complete record of all your contributions.</p>
            </div>
            <a href="{{ route('donate') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-2xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Donation
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 text-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=EFF6FF&color=1D4ED8&size=128"
                             alt="Avatar" class="w-16 h-16 rounded-2xl mx-auto border-4 border-slate-50 shadow-sm mb-3">
                        <h3 class="font-bold text-slate-900 text-sm">{{ auth()->user()->name }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Member</p>
                    </div>
                    <nav class="p-3 space-y-1">
                        <a href="{{ route('user.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Overview
                        </a>
                        <a href="{{ route('donor.donations.history') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold bg-blue-600 text-white shadow-md shadow-blue-500/25">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Donation History
                        </a>
                        <a href="{{ route('user.profile') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Account Profile
                        </a>
                        <div class="pt-2 mt-2 border-t border-slate-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900">All Donations</h2>
                        <span class="text-xs font-bold text-slate-400">{{ $donations->total() }} records</span>
                    </div>

                    @if($donations->isEmpty())
                    <div class="px-8 py-20 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-slate-500 font-bold">No donations yet</p>
                        <a href="{{ route('donate') }}" class="inline-block mt-4 px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                            Make Your First Donation
                        </a>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 text-[10px] uppercase tracking-widest font-bold text-slate-400">
                                <tr>
                                    <th class="px-8 py-4 text-left">Campaign</th>
                                    <th class="px-6 py-4 text-left">Amount</th>
                                    <th class="px-6 py-4 text-left">Type</th>
                                    <th class="px-6 py-4 text-left">Status</th>
                                    <th class="px-6 py-4 text-left">Date</th>
                                    <th class="px-6 py-4 text-left">Certificate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($donations as $donation)
                                @php
                                    $statusConfig = [
                                        'completed' => ['bg-emerald-50 text-emerald-600 border-emerald-100', 'Completed'],
                                        'pending'   => ['bg-amber-50 text-amber-600 border-amber-100', 'Pending'],
                                        'failed'    => ['bg-red-50 text-red-600 border-red-100', 'Failed'],
                                        'refunded'  => ['bg-slate-100 text-slate-500 border-slate-200', 'Refunded'],
                                    ];
                                    [$statusClass, $statusLabel] = $statusConfig[$donation->status] ?? ['bg-slate-50 text-slate-500 border-slate-100', ucfirst($donation->status)];
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-black text-xs flex-shrink-0">
                                                {{ strtoupper(substr($donation->campaign->title ?? 'G', 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-slate-900 text-sm truncate max-w-[160px]">
                                                {{ $donation->campaign->title ?? 'General' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-slate-900 text-sm">EGP {{ number_format($donation->amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase {{ $donation->type === 'recurring' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $donation->type === 'recurring' ? 'Monthly' : 'One-time' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase border {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-slate-500 font-medium text-xs">{{ $donation->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($donation->certificate_uuid && $donation->status === 'completed')
                                        <a href="{{ route('certificates.download', $donation->certificate_uuid) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-bold hover:bg-emerald-100 transition-colors border border-emerald-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Download
                                        </a>
                                        @else
                                        <span class="text-slate-300 text-xs font-medium">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($donations->hasPages())
                    <div class="px-8 py-5 border-t border-slate-50">
                        {{ $donations->links() }}
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

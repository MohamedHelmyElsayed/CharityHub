@extends('layouts.app')

@section('title', 'Donation Successful!')

@section('content')
<div class="min-h-screen bg-slate-50 py-20 lg:py-32 flex items-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-blue-500/10 border border-slate-100 p-10 lg:p-20 relative overflow-hidden">
            {{-- Decoration --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-blue-500"></div>
            
            <div class="mb-10 relative">
                <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto text-green-500 border border-green-100 shadow-xl shadow-green-500/10">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="absolute inset-0 bg-green-500 blur-3xl opacity-10 rounded-full scale-150"></div>
            </div>

            <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 mb-6 tracking-tight">Thank You!</h1>
            <p class="text-xl text-slate-500 font-medium leading-relaxed mb-10">
                Your contribution has been received successfully. You've just taken a step towards changing lives. We'll send a confirmation email with your impact certificate shortly.
            </p>

            <div class="bg-slate-50 rounded-2xl p-6 mb-10 border border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-6 w-full max-w-full overflow-hidden">
                <div class="text-left flex-1 min-w-0 w-full overflow-hidden">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Transaction ID</span>
                    <span class="font-mono text-sm font-bold text-slate-900 block truncate" title="{{ $sessionId ?? 'TXN-'.time() }}">{{ $sessionId ?? 'TXN-'.time() }}</span>
                </div>
                <div class="w-px h-10 bg-slate-200 hidden sm:block flex-shrink-0"></div>
                <div class="text-left flex-shrink-0">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full whitespace-nowrap">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                        Confirmed
                    </span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" class="px-8 py-4 bg-slate-900 text-white rounded-2xl hover:bg-blue-600 transition-all duration-300 font-bold shadow-lg shadow-slate-900/10 hover:shadow-blue-500/25">
                    Return Home
                </a>
                <a href="{{ route('impact.index') }}" class="px-8 py-4 bg-white text-slate-900 rounded-2xl hover:bg-slate-50 transition-all duration-300 font-bold border border-slate-200">
                    See Recent Impact
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

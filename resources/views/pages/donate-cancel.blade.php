@extends('layouts.app')

@section('title', 'Donation Cancelled')

@section('content')
<div class="min-h-screen bg-slate-50 py-20 lg:py-32 flex items-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-900/5 border border-slate-100 p-10 lg:p-20 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-slate-200"></div>
            
            <div class="mb-10">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto text-slate-400 border border-slate-100 shadow-sm">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>

            <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 mb-6 tracking-tight">Payment Cancelled</h1>
            <p class="text-xl text-slate-500 font-medium leading-relaxed mb-10">
                Your donation process was cancelled. No charges were made to your account. If you experienced any technical issues, please let us know.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('donate') }}" class="px-8 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all duration-300 font-bold shadow-lg shadow-blue-500/20">
                    Try Again
                </a>
                <a href="{{ route('home') }}" class="px-8 py-4 bg-white text-slate-900 rounded-2xl hover:bg-slate-50 transition-all duration-300 font-bold border border-slate-200">
                    Return Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

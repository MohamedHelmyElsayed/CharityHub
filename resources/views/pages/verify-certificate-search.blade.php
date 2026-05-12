@extends('layouts.app')

@section('title', 'Verify a Certificate')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-20 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl shadow-slate-200/50 border border-slate-100 p-10 text-center">
        <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-8">
            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>

        <h1 class="text-3xl font-black text-slate-900 mb-4">Certificate Verification</h1>
        <p class="text-slate-500 mb-10 leading-relaxed font-medium">Verify the authenticity of a CharityHub certificate by entering its unique UUID.</p>

        <form action="{{ route('verify.index') }}" method="GET" class="space-y-4">
            <div class="relative group">
                <input type="text" name="uuid" required placeholder="Enter Certificate UUID (e.g. 550e8400-e29b...)"
                       class="w-full px-6 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none font-semibold text-slate-900 placeholder:text-slate-400">
            </div>
            <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-blue-600 transform hover:-translate-y-1 transition-all duration-300 shadow-xl shadow-slate-200">
                Verify Certificate
            </button>
        </form>

        <div class="mt-10 pt-8 border-t border-slate-50">
            <p class="text-xs text-slate-400 font-medium">Safe & Secure verification through CharityHub's transparent auditing system.</p>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-20">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-12">
        <h1 class="text-4xl font-black text-slate-900 mb-8">Terms of Service</h1>
        
        <div class="prose prose-slate max-w-none space-y-6 text-slate-600 font-medium leading-relaxed">
            <p class="text-lg">By using CharityHub, you agree to these terms. Please read them carefully.</p>
            
            <h2 class="text-xl font-bold text-slate-900 mt-10">1. Donation Integrity</h2>
            <p>All donations made through CharityHub are processed securely. Once a donation is distributed to a campaign, it is generally non-refundable unless there is a verified technical error in processing.</p>

            <h2 class="text-xl font-bold text-slate-900 mt-10">2. Volunteer Conduct</h2>
            <p>Volunteers are expected to act with integrity and respect for the communities they serve. CharityHub reserves the right to suspend volunteer profiles for misconduct or violation of safety protocols.</p>

            <h2 class="text-xl font-bold text-slate-900 mt-10">3. Account Security</h2>
            <p>You are responsible for maintaining the confidentiality of your account credentials. You agree to notify us immediately of any unauthorized use of your account.</p>

            <h2 class="text-xl font-bold text-slate-900 mt-10">4. Intellectual Property</h2>
            <p>The content, logo, and platform design are the property of CharityHub. You may not reproduce or use them without explicit permission.</p>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-50 text-sm text-slate-400 font-medium">
            Last Updated: May 12, 2026
        </div>
    </div>
</div>
@endsection

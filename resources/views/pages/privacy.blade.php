@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-20">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-12">
        <h1 class="text-4xl font-black text-slate-900 mb-8">Privacy Policy</h1>
        
        <div class="prose prose-slate max-w-none space-y-6 text-slate-600 font-medium leading-relaxed">
            <p class="text-lg">At CharityHub, we are committed to protecting your privacy and ensuring your trust in our platform. This policy explains how we handle your data.</p>
            
            <h2 class="text-xl font-bold text-slate-900 mt-10">1. Information We Collect</h2>
            <p>We collect information you provide directly to us when you create an account, make a donation, or apply for volunteering. This includes your name, email address, and payment information processed through Stripe.</p>

            <h2 class="text-xl font-bold text-slate-900 mt-10">2. How We Use Your Information</h2>
            <ul class="list-disc pl-6 space-y-2">
                <li>To process donations and issue tax receipts/certificates.</li>
                <li>To coordinate volunteering opportunities.</li>
                <li>To send impactful updates about the campaigns you support.</li>
                <li>To maintain the security and transparency of our platform.</li>
            </ul>

            <h2 class="text-xl font-bold text-slate-900 mt-10">3. Data Transparency</h2>
            <p>CharityHub is built on transparency. While your personal contact details remain private, basic donation metadata (amount and campaign supported) may be part of our public impact auditing logs to ensure accountability.</p>

            <h2 class="text-xl font-bold text-slate-900 mt-10">4. Your Rights</h2>
            <p>You have the right to access, correct, or delete your personal information at any time through your dashboard or by contacting our support team.</p>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-50 text-sm text-slate-400 font-medium">
            Last Updated: May 12, 2026
        </div>
    </div>
</div>
@endsection

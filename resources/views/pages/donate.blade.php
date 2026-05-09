@extends('layouts.app')

@section('title', 'Donate')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-white to-blue-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Make a Donation</h1>
            <p class="text-gray-500 text-lg">100% of your donation goes to the cause. You'll receive a verified PDF certificate.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                {{-- Left: Donation Form --}}
                <div class="p-8 space-y-6">
                    @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-start gap-3 shadow-sm">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-sm font-medium">{{ session('error') }}</div>
                    </div>
                    @endif

                    <form id="donation-form" method="POST" action="{{ route('donate.checkout') }}">
                        @csrf

                        {{-- Campaign Selection --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Choose Campaign</label>
                            <select name="campaign_id" id="campaign_id" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                                <option value="">Select a campaign...</option>
                                @foreach($campaigns as $c)
                                    <option value="{{ $c->id }}" {{ ($selectedCampaign && $selectedCampaign->id === $c->id) ? 'selected' : '' }}>
                                        {{ $c->title }} ({{ $c->progress_percentage }}% funded)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Donation Type --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Donation Type</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="donation-type-label flex items-center justify-center gap-2 p-3 border-2 border-blue-500 bg-blue-50 rounded-xl cursor-pointer transition-all hover:border-blue-400">
                                    <input type="radio" name="type" value="one_time" checked class="text-blue-600 focus:ring-blue-500">
                                    <span class="font-semibold text-sm">One-Time</span>
                                </label>
                                <label class="donation-type-label flex items-center justify-center gap-2 p-3 border-2 border-gray-200 bg-white rounded-xl cursor-pointer hover:border-blue-400 transition-all">
                                    <input type="radio" name="type" value="recurring" class="text-blue-600 focus:ring-blue-500">
                                    <span class="font-semibold text-sm">Monthly</span>
                                </label>
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (EGP)</label>
                            <div class="grid grid-cols-4 gap-2 mb-3">
                                @foreach([250, 500, 1000, 2500] as $preset)
                                <button type="button" onclick="setAmount({{ $preset }})"
                                        class="amount-preset px-3 py-2 border-2 border-gray-200 rounded-lg text-sm font-semibold hover:border-blue-400 hover:bg-blue-50 transition-all">
                                    {{ $preset }}
                                </button>
                                @endforeach
                            </div>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold text-xs">EGP</span>
                                <input type="number" name="amount" id="amount" min="1" max="1000000" step="1"
                                       placeholder="Custom amount" required
                                       class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                            </div>
                        </div>

                        {{-- Personal Info --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                                <input type="text" name="name" required value="{{ auth()->user()?->name }}"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
                                       placeholder="Full name">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" required value="{{ auth()->user()?->email }}"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
                                       placeholder="your@email.com">
                            </div>
                        </div>

                        {{-- Message --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Message (optional)</label>
                            <textarea name="message" rows="2"
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all resize-none"
                                      placeholder="Leave a message of support..."></textarea>
                        </div>

                        {{-- Options --}}
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="anonymous" value="1" class="w-4 h-4 text-blue-600 rounded">
                                <span class="text-sm text-gray-600">Donate anonymously</span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="gdpr_consent" value="1" required class="w-4 h-4 text-blue-600 rounded mt-0.5">
                                <span class="text-sm text-gray-600">I consent to CharityHub storing my data to issue a certificate. <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a></span>
                            </label>
                        </div>

                        <input type="hidden" name="idempotency_key" id="idempotency_key">

                        @guest
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
                            You need to <a href="{{ route('login') }}" class="font-semibold underline">sign in</a> or <a href="{{ route('register') }}" class="font-semibold underline">register</a> to complete a donation.
                        </div>
                        @endguest

                        <button type="submit" id="donate-submit-btn"
                                class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-xl transition-all shadow-md hover:shadow-blue-200 flex items-center justify-center gap-2 disabled:opacity-60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Donate Securely via Paymob
                        </button>
                    </form>
                </div>

                {{-- Right: Trust & Info --}}
                <div class="bg-gradient-to-br from-blue-700 to-blue-800 p-8 text-white flex flex-col justify-between">
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-2xl font-bold mb-3">Your gift, made transparent.</h2>
                            <p class="text-blue-100 leading-relaxed">Every donation receives a personalized, verifiable certificate and appears in our public impact reports.</p>
                        </div>

                        <div class="space-y-4">
                            @foreach([
                                ['title' => 'PDF Certificate', 'desc' => 'Instantly generated and emailed to you'],
                                ['title' => 'Real-time Tracking', 'desc' => 'Watch your impact grow live'],
                                ['title' => 'Full Audit Trail', 'desc' => 'Every donation is immutably recorded'],
                                ['title' => 'Bank-Level Security', 'desc' => 'Paymob PCI-DSS certified checkout'],
                            ] as $feature)
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">{{ $feature['title'] }}</div>
                                    <div class="text-blue-200 text-sm">{{ $feature['desc'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-white/10 rounded-xl text-sm text-blue-100">
                        🔒 Payments processed by <strong class="text-white">Paymob</strong>. We never store your card details.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('idempotency_key').value = crypto.randomUUID();
function setAmount(val) {
    document.getElementById('amount').value = val;
    document.querySelectorAll('.amount-preset').forEach(b => b.classList.remove('border-blue-500', 'bg-blue-50'));
    event.target.classList.add('border-blue-500', 'bg-blue-50');
}
document.getElementById('donation-form').addEventListener('submit', function() {
    const btn = document.getElementById('donate-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...';
});

document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.donation-type-label').forEach(label => {
            label.classList.remove('border-blue-500', 'bg-blue-50');
            label.classList.add('border-gray-200', 'bg-white');
        });
        if(this.checked) {
            this.closest('label').classList.remove('border-gray-200', 'bg-white');
            this.closest('label').classList.add('border-blue-500', 'bg-blue-50');
        }
    });
});
</script>
@endpush
@endsection

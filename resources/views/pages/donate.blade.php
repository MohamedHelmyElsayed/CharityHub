@extends('layouts.app')

@section('title', 'Make a Donation')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Support a Cause</h1>
            <p class="mt-4 text-lg text-gray-600">Your contribution brings us closer to our goal. Thank you for your generosity.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8 md:p-12">
                <form action="{{ route('donate.checkout') }}" method="POST">
                    @csrf
                    
                    <!-- Campaign Selection -->
                    <div class="mb-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Choose a Campaign</h3>
                        <select name="campaign_id" class="block w-full pl-4 pr-10 py-4 text-lg border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-xl shadow-sm border font-bold text-gray-700 bg-gray-50">
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" {{ request('campaign') == $campaign->id ? 'selected' : '' }}>
                                    {{ $campaign->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Donation Type -->
                    <div class="mb-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Donation Frequency</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="recurring" value="0" class="peer sr-only" checked>
                                <div class="rounded-xl border-2 border-gray-200 p-4 text-center hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 transition">
                                    <span class="block text-base font-bold text-gray-900 peer-checked:text-primary-700">One-time</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="recurring" value="1" class="peer sr-only">
                                <div class="rounded-xl border-2 border-gray-200 p-4 text-center hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 transition relative overflow-hidden">
                                    <div class="absolute top-0 right-0 bg-primary-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-bl">Popular</div>
                                    <span class="block text-base font-bold text-gray-900 peer-checked:text-primary-700">Monthly</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Amount Selection -->
                    <div class="mb-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Select Amount</h3>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            @foreach([25, 50, 100, 250, 500] as $amount)
                            <label class="cursor-pointer amount-label">
                                <input type="radio" name="amount" value="{{ $amount }}" class="peer sr-only" {{ $amount === 50 ? 'checked' : '' }}>
                                <div class="rounded-xl border-2 border-gray-200 py-4 text-center hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700 font-extrabold text-xl text-gray-700 transition">
                                    ${{ $amount }}
                                </div>
                            </label>
                            @endforeach
                            <label class="cursor-pointer">
                                <input type="radio" name="amount" id="custom-amount-radio" value="custom" class="peer sr-only">
                                <div class="rounded-xl border-2 border-gray-200 py-4 text-center hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700 font-extrabold text-xl text-gray-700 transition">
                                    Custom
                                </div>
                            </label>
                        </div>
                        <div class="relative mt-4 rounded-md shadow-sm hidden" id="custom-amount-input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold text-xl">$</span>
                            </div>
                            <input type="number" name="custom_amount" id="custom_amount_field" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-8 pr-12 text-xl font-bold border-gray-300 rounded-xl py-4 border shadow-inner" placeholder="0.00">
                        </div>
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-xl font-extrabold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition transform hover:-translate-y-1">
                        Proceed to Payment
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    
                    <p class="mt-4 text-center text-sm text-gray-500 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        You will be redirected to secure Stripe checkout
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const customAmountRadio = document.getElementById('custom-amount-radio');
    const customAmountInput = document.getElementById('custom-amount-input');
    const customAmountField = document.getElementById('custom_amount_field');
    const amountRadios = document.querySelectorAll('input[name="amount"]');

    amountRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.id === 'custom-amount-radio') {
                customAmountInput.classList.remove('hidden');
                customAmountField.required = true;
            } else {
                customAmountInput.classList.add('hidden');
                customAmountField.required = false;
            }
        });
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        if (customAmountRadio.checked) {
            // We need to send the numeric value, so we might need a hidden field or change value
            // But let's keep it simple: the controller can check if amount is 'custom' then use custom_amount
            // Actually, better to just replace the value of 'amount' before submit
            const val = customAmountField.value;
            if (val) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'real_amount';
                hiddenInput.value = val;
                this.appendChild(hiddenInput);
            }
        }
    });
</script>
@endsection

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
                <form action="#" method="POST">
                    
                    <!-- Donation Type -->
                    <div class="mb-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Donation Frequency</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" class="peer sr-only" checked>
                                <div class="rounded-xl border-2 border-gray-200 p-4 text-center hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 transition">
                                    <span class="block text-base font-bold text-gray-900 peer-checked:text-primary-700">One-time</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" class="peer sr-only">
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
                            <label class="cursor-pointer">
                                <input type="radio" name="amount" class="peer sr-only" {{ $amount === 50 ? 'checked' : '' }}>
                                <div class="rounded-xl border-2 border-gray-200 py-4 text-center hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700 font-extrabold text-xl text-gray-700 transition">
                                    ${{ $amount }}
                                </div>
                            </label>
                            @endforeach
                            <label class="cursor-pointer">
                                <input type="radio" name="amount" class="peer sr-only" id="custom-amount-radio">
                                <div class="rounded-xl border-2 border-gray-200 py-4 text-center hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700 font-extrabold text-xl text-gray-700 transition">
                                    Custom
                                </div>
                            </label>
                        </div>
                        <div class="relative mt-4 rounded-md shadow-sm hidden" id="custom-amount-input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold text-xl">$</span>
                            </div>
                            <input type="number" name="custom_amount" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-8 pr-12 text-xl font-bold border-gray-300 rounded-xl py-4 border shadow-inner" placeholder="0.00">
                        </div>
                    </div>

                    <hr class="border-gray-100 my-8">

                    <!-- Personal Info -->
                    <div class="mb-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Your Information</h3>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="first-name" class="block text-sm font-medium text-gray-700">First name</label>
                                <div class="mt-1">
                                    <input type="text" name="first-name" id="first-name" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                                </div>
                            </div>
                            <div>
                                <label for="last-name" class="block text-sm font-medium text-gray-700">Last name</label>
                                <div class="mt-1">
                                    <input type="text" name="last-name" id="last-name" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details (Dummy UI) -->
                    <div class="mb-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Details</h3>
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex items-center justify-between mb-6">
                                <p class="text-sm font-medium text-gray-500">Credit or debit card</p>
                                <div class="flex gap-2">
                                    <div class="w-8 h-5 bg-gray-200 rounded"></div>
                                    <div class="w-8 h-5 bg-gray-200 rounded"></div>
                                    <div class="w-8 h-5 bg-gray-200 rounded"></div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <input type="text" placeholder="Card number" class="block w-full text-base border-gray-300 rounded-lg py-3 px-4 border shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <input type="text" placeholder="MM / YY" class="block w-full text-base border-gray-300 rounded-lg py-3 px-4 border shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    <div>
                                        <input type="text" placeholder="CVC" class="block w-full text-base border-gray-300 rounded-lg py-3 px-4 border shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-xl font-extrabold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition transform hover:-translate-y-1">
                        Donate $50.00
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    
                    <p class="mt-4 text-center text-sm text-gray-500 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        Secure 256-bit SSL encryption
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="amount"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.id === 'custom-amount-radio') {
                document.getElementById('custom-amount-input').classList.remove('hidden');
            } else {
                document.getElementById('custom-amount-input').classList.add('hidden');
            }
        });
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Become a Volunteer')

@section('content')
<div class="bg-gray-900 py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1559027615-cd4628902d4a?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl tracking-tight mb-6">Join Our Volunteer Team</h1>
        <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">Give your time, skills, and heart to make a real difference in communities that need it most. Together, we can build a better tomorrow.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-16 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-8 sm:p-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 border-b border-gray-100 pb-4">Application Form</h2>
            
            <form action="#" method="POST" class="space-y-8">
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
                    <div>
                        <label for="first-name" class="block text-sm font-bold text-gray-700">First name</label>
                        <div class="mt-2">
                            <input type="text" name="first-name" id="first-name" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                        </div>
                    </div>
                    <div>
                        <label for="last-name" class="block text-sm font-bold text-gray-700">Last name</label>
                        <div class="mt-2">
                            <input type="text" name="last-name" id="last-name" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-bold text-gray-700">Email address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="phone" class="block text-sm font-bold text-gray-700">Phone Number</label>
                        <div class="mt-2">
                            <input type="tel" name="phone" id="phone" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-4">Areas of Interest</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 cursor-pointer rounded-lg border border-gray-200 hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input name="interests[]" type="checkbox" class="focus:ring-primary-500 h-5 w-5 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-bold text-gray-900 block">Event Organization</span>
                                <span class="text-gray-500">Help plan and execute community events.</span>
                            </div>
                        </label>
                        <label class="relative flex items-start p-4 cursor-pointer rounded-lg border border-gray-200 hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input name="interests[]" type="checkbox" class="focus:ring-primary-500 h-5 w-5 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-bold text-gray-900 block">Fundraising</span>
                                <span class="text-gray-500">Assist with reaching our financial goals.</span>
                            </div>
                        </label>
                        <label class="relative flex items-start p-4 cursor-pointer rounded-lg border border-gray-200 hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input name="interests[]" type="checkbox" class="focus:ring-primary-500 h-5 w-5 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-bold text-gray-900 block">Field Work</span>
                                <span class="text-gray-500">On-site support and direct action.</span>
                            </div>
                        </label>
                        <label class="relative flex items-start p-4 cursor-pointer rounded-lg border border-gray-200 hover:bg-gray-50">
                            <div class="flex items-center h-5">
                                <input name="interests[]" type="checkbox" class="focus:ring-primary-500 h-5 w-5 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-bold text-gray-900 block">Administrative</span>
                                <span class="text-gray-500">Data entry, calls, and office support.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <label for="message" class="block text-sm font-bold text-gray-700">Why do you want to volunteer with us?</label>
                    <p class="text-xs text-gray-500 mt-1 mb-3">Briefly tell us about your motivation and any relevant experience.</p>
                    <div>
                        <textarea id="message" name="message" rows="5" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50"></textarea>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="button" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-extrabold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition transform hover:-translate-y-1">
                        Submit Application
                    </button>
                    <p class="text-center text-sm text-gray-500 mt-4">We will review your application and get back to you within 3-5 business days.</p>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection

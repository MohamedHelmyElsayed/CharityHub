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
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold text-center shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(auth()->check() && auth()->user()->subtype === 'volunteer')
        <!-- Volunteer Dashboard / Log Hours -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-12">
            <div class="p-8 sm:p-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 border-b border-gray-100 pb-4 text-center">Volunteer Portal</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Log Your Hours</h3>
                        <form action="{{ route('volunteer.hours') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Date</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg py-3 px-4 border bg-gray-50 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Hours Worked</label>
                                <input type="number" name="hours" step="0.5" min="0.5" max="24" required placeholder="e.g. 4.5" class="w-full border-gray-300 rounded-lg py-3 px-4 border bg-gray-50 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <button type="submit" class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white font-extrabold rounded-xl transition shadow-lg shadow-primary-500/20">
                                Save Hours
                            </button>
                        </form>
                    </div>
                    
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Your Impact</h3>
                        <div class="text-center py-6">
                            <span class="block text-5xl font-black text-primary-600">{{ auth()->user()->volunteer->total_hours }}</span>
                            <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Hours Contributed</span>
                        </div>
                        <div class="mt-4">
                            <h4 class="text-sm font-bold text-gray-700 mb-3">Recent Activity</h4>
                            <ul class="space-y-3">
                                @forelse(auth()->user()->volunteer->hours()->latest()->take(3)->get() as $hour)
                                    <li class="flex justify-between text-sm border-b border-gray-200 pb-2">
                                        <span class="text-gray-600">{{ $hour->date }}</span>
                                        <span class="font-bold text-gray-900">{{ $hour->hours }} hours</span>
                                    </li>
                                @empty
                                    <p class="text-xs text-gray-400">No hours logged yet.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Application Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8 sm:p-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 border-b border-gray-100 pb-4">Application Form</h2>
                
                <form action="{{ route('volunteer.register') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    @if(!auth()->check())
                        <div class="p-4 bg-yellow-50 border border-yellow-100 text-yellow-800 rounded-lg text-sm font-medium mb-6">
                            You must be logged in to apply as a volunteer. 
                            <a href="{{ route('login') }}" class="underline font-bold">Login here</a>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
                        <div>
                            <label for="first-name" class="block text-sm font-bold text-gray-700">Full Name</label>
                            <div class="mt-2">
                                <input type="text" name="name" id="name" value="{{ auth()->user() ? auth()->user()->name : '' }}" required class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700">Email address</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" value="{{ auth()->user() ? auth()->user()->email : '' }}" required class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-base border-gray-300 rounded-lg py-3 px-4 border bg-gray-50">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-4">Areas of Interest</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach(['Event Organization', 'Fundraising', 'Field Work', 'Administrative'] as $interest)
                            <label class="relative flex items-start p-4 cursor-pointer rounded-lg border border-gray-200 hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input name="interests[]" type="checkbox" value="{{ $interest }}" class="focus:ring-primary-500 h-5 w-5 text-primary-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-bold text-gray-900 block">{{ $interest }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" {{ !auth()->check() ? 'disabled' : '' }} class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-extrabold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition transform hover:-translate-y-1 {{ !auth()->check() ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Register as Volunteer
                        </button>
                        <p class="text-center text-sm text-gray-500 mt-4">By registering, you agree to our volunteer terms and code of conduct.</p>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

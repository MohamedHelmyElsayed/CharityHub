@extends('layouts.app')
@section('title', 'Sign In')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-white to-blue-50 flex items-center justify-center py-16 px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402 0-3.791 3.068-5.191 5.281-5.191 1.312 0 4.151.501 5.719 4.457 1.59-3.968 4.464-4.447 5.726-4.447 2.54 0 5.274 1.621 5.274 5.181 0 4.069-5.136 8.625-11 14.402z"/></svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Welcome back</h1>
            <p class="text-gray-500 mt-2">Sign in to your CharityHub account</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all @error('email') border-red-300 @enderror"
                           placeholder="your@email.com">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
                           placeholder="••••••••">
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-600">Remember me for 30 days</span>
                </label>

                <button type="submit"
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-500 text-sm">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Create one free</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

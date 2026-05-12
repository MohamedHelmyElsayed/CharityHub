@extends('layouts.app')

@section('title', 'Account Profile')

@section('content')
<div class="min-h-screen bg-slate-50/50 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Account Profile</h1>
            <p class="text-slate-500 font-medium mt-1">Manage your personal details and security settings.</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold text-sm">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- ── Sidebar ── --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    {{-- Avatar --}}
                    <div class="p-6 border-b border-slate-50 text-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EFF6FF&color=1D4ED8&size=128"
                             alt="Avatar"
                             class="w-16 h-16 rounded-2xl mx-auto border-4 border-slate-50 shadow-sm mb-3">
                        <h3 class="font-bold text-slate-900 text-sm">{{ $user->name }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Member</p>
                    </div>
                    {{-- Nav --}}
                    <nav class="p-3 space-y-1">
                        <a href="{{ route('user.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Overview
                        </a>
                        <a href="{{ route('donor.donations.history') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Donation History
                        </a>
                        <a href="{{ route('user.profile') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold bg-blue-600 text-white shadow-md shadow-blue-500/25 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Account Profile
                        </a>
                        <div class="pt-2 mt-2 border-t border-slate-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </nav>
                </div>
            </div>

            {{-- ── Main Content ── --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Personal Information --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Personal Information</h2>
                    </div>
                    <form action="{{ route('user.profile.update') }}" method="POST" class="p-8">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                                <input type="text" name="name" id="name"
                                       value="{{ old('name', $user->name) }}"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 font-medium focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                @error('name')<p class="text-rose-500 text-xs font-bold mt-1.5">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                                <input type="email" name="email" id="email"
                                       value="{{ old('email', $user->email) }}"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 font-medium focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                                @error('email')<p class="text-rose-500 text-xs font-bold mt-1.5">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center gap-4">
                            <button type="submit"
                                    class="px-7 py-3 bg-blue-600 text-white rounded-2xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                                Save Changes
                            </button>
                            <span class="text-xs text-slate-400 font-medium">Member since {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Change Password</h2>
                    </div>
                    <form action="{{ route('user.profile.password') }}" method="POST" class="p-8">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label for="current_password" class="block text-sm font-bold text-slate-700 mb-2">Current Password</label>
                                <input type="password" name="current_password" id="current_password"
                                       placeholder="••••••••"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 font-medium focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                @error('current_password')<p class="text-rose-500 text-xs font-bold mt-1.5">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-bold text-slate-700 mb-2">New Password</label>
                                <input type="password" name="password" id="password"
                                       placeholder="Min. 8 chars"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 font-medium focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                @error('password')<p class="text-rose-500 text-xs font-bold mt-1.5">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       placeholder="Repeat password"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 font-medium focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            </div>
                        </div>
                        <div class="mt-6 pt-6 border-t border-slate-50">
                            <button type="submit"
                                    class="px-7 py-3 bg-indigo-600 text-white rounded-2xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Danger Zone --}}
                <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden"
                     x-data="{ showDeleteModal: false }">
                    <div class="px-8 py-5 border-b border-rose-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-rose-700">Danger Zone</h2>
                    </div>
                    <div class="p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div>
                            <h3 class="font-bold text-slate-900">Delete Account</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Once deleted, all your data will be permanently removed. This cannot be undone.</p>
                        </div>
                        <button @click="showDeleteModal = true"
                                class="flex-shrink-0 px-6 py-3 bg-rose-600 text-white rounded-2xl font-bold text-sm hover:bg-rose-700 transition-all shadow-lg shadow-rose-500/20 whitespace-nowrap">
                            Delete My Account
                        </button>
                    </div>

                    {{-- Delete Modal --}}
                    <div x-show="showDeleteModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="fixed inset-0 z-50 flex items-center justify-center px-4"
                         style="display:none;">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                        <div class="relative bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl border border-slate-100 z-10"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">
                            <div class="text-center mb-6">
                                <div class="w-14 h-14 bg-rose-50 rounded-full flex items-center justify-center text-rose-600 mx-auto mb-4">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </div>
                                <h3 class="text-xl font-black text-slate-900">Are you sure?</h3>
                                <p class="text-slate-500 text-sm mt-2 leading-relaxed">This will permanently delete your account and all data. <strong>Cannot be undone.</strong></p>
                            </div>
                            <form action="{{ route('user.profile.delete') }}" method="POST" class="space-y-4">
                                @csrf
                                @method('DELETE')
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">
                                        Type <span class="text-rose-600 font-black">DELETE</span> to confirm:
                                    </label>
                                    <input type="text" name="confirm_delete" required
                                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-center font-mono font-bold uppercase tracking-widest focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all"
                                           placeholder="DELETE">
                                    @error('confirm_delete')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click="showDeleteModal = false"
                                            class="flex-1 px-5 py-3 bg-slate-100 text-slate-700 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="flex-1 px-5 py-3 bg-rose-600 text-white rounded-2xl font-bold text-sm hover:bg-rose-700 transition-all shadow-lg shadow-rose-500/20">
                                        Delete Forever
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

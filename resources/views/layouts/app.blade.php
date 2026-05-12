<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CharityHub') — Empower Change</title>
    <meta name="description" content="@yield('meta_description', 'Join CharityHub to donate to impactful campaigns, volunteer your time, and track your real-world impact.')">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'CharityHub — Empowering Global Change')">
    <meta property="og:description" content="@yield('og_description', 'Donate, volunteer, and track impact with transparent charitable giving.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('og_title', 'CharityHub — Empowering Global Change')">
    <meta property="twitter:description" content="@yield('og_description', 'Donate, volunteer, and track impact with transparent charitable giving.')">
    <meta property="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900 selection:bg-blue-200 selection:text-blue-900">

{{-- Navigation --}}
<nav class="sticky top-0 z-50 glass-nav border-b border-slate-200/50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 group-hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402 0-3.791 3.068-5.191 5.281-5.191 1.312 0 4.151.501 5.719 4.457 1.59-3.968 4.464-4.447 5.726-4.447 2.54 0 5.274 1.621 5.274 5.181 0 4.069-5.136 8.625-11 14.402z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-slate-900">CharityHub</span>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center space-x-10">
                <a href="{{ route('campaigns.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors {{ request()->routeIs('campaigns*') ? 'text-blue-600' : '' }}">
                    Campaigns
                </a>
                <a href="{{ route('volunteering.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors {{ request()->routeIs('volunteering*') ? 'text-blue-600' : '' }}">
                    Volunteering
                </a>
                <a href="{{ route('impact.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors {{ request()->routeIs('impact*') ? 'text-blue-600' : '' }}">
                    Impact
                </a>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center space-x-6">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
                        <a href="{{ route('custom_admin.dashboard') }}" class="text-slate-600 hover:text-blue-600 text-sm font-semibold transition-colors">
                            Admin Panel
                        </a>
                    @endif
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-slate-700 hover:text-blue-600 font-semibold text-sm transition-colors focus:outline-none">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=EFF6FF&color=1D4ED8" alt="Avatar" class="w-8 h-8 rounded-full border border-slate-200">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute right-0 pt-3 w-56 opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-200 transform origin-top-right scale-95 group-hover:scale-100 z-50">
                            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 py-2">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-sm leading-5 font-medium text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs leading-5 font-medium text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">My Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-blue-600 font-semibold text-sm transition-colors">Sign in</a>
                @endauth
                <a href="{{ route('donate') }}"
                   class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-semibold text-sm hover:bg-blue-600 transform hover:-translate-y-0.5 transition-all duration-300 shadow-md hover:shadow-blue-500/30">
                    Donate Now
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="fixed top-24 right-4 z-50 bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl shadow-slate-900/20 flex items-center space-x-3 animate-slide-in border border-slate-700">
        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error') || $errors->any())
    <div class="fixed top-24 right-4 z-50 bg-white text-slate-900 px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-3 animate-slide-in border border-red-100">
        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <span class="font-medium">{{ session('error') ?? $errors->first() }}</span>
    </div>
@endif

{{-- Main Content --}}
<main class="min-h-[70vh]">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-white border-t border-slate-200 mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-md shadow-blue-500/20">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402 0-3.791 3.068-5.191 5.281-5.191 1.312 0 4.151.501 5.719 4.457 1.59-3.968 4.464-4.447 5.726-4.447 2.54 0 5.274 1.621 5.274 5.181 0 4.069-5.136 8.625-11 14.402z"/></svg>
                    </div>
                    <span class="text-slate-900 font-bold text-xl tracking-tight">CharityHub</span>
                </div>
                <p class="text-base leading-relaxed mb-6 text-slate-500 max-w-md">Empowering change through transparent, trustworthy, and auditable charitable giving. Every donation is tracked, verified, and makes a real difference.</p>
                <div class="flex space-x-4">
                    {{-- Social Icons --}}
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                </div>
            </div>
            <div>
                <h4 class="text-slate-900 font-bold mb-6">Platform</h4>
                <ul class="space-y-3 text-sm font-medium">
                    <li><a href="{{ route('campaigns.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">Explore Campaigns</a></li>
                    <li><a href="{{ route('volunteering.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">Volunteering Opportunities</a></li>
                    <li><a href="{{ route('impact.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">Impact Reports</a></li>
                    <li><a href="{{ route('donate') }}" class="text-slate-500 hover:text-blue-600 transition-colors">Make a Donation</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-slate-900 font-bold mb-6">Trust & Safety</h4>
                <ul class="space-y-3 text-sm font-medium">
                    <li><span class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg> PCI-DSS Compliant</span></li>
                    <li><span class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> GDPR Compliant</span></li>
                    <li><span class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Full Audit Trail</span></li>
                    <li><a href="/verify/example" class="text-slate-500 hover:text-blue-600 transition-colors flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Verify a Certificate</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-16 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm font-medium text-slate-500">© {{ date('Y') }} CharityHub. All rights reserved.</p>
            <div class="flex space-x-6 text-sm font-medium text-slate-500">
                <a href="#" class="hover:text-slate-900 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-slate-900 transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

@livewireScripts

<script>
setTimeout(() => {
    document.querySelectorAll('.animate-slide-in').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        el.style.transition = 'all 0.5s ease-out';
        setTimeout(() => el.remove(), 500);
    });
}, 4000);
</script>

@stack('scripts')
</body>
</html>

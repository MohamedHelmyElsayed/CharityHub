<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'CharityHub'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
            :root {
                --primary-50: #f0f9ff;
                --primary-100: #e0f2fe;
                --primary-500: #0ea5e9;
                --primary-600: #0284c7;
                --primary-700: #0369a1;
            }
            .bg-primary-50 { background-color: var(--primary-50); }
            .bg-primary-500 { background-color: var(--primary-500); }
            .bg-primary-600 { background-color: var(--primary-600); }
            .bg-primary-700 { background-color: var(--primary-700); }
            .text-primary-600 { color: var(--primary-600); }
            .text-primary-700 { color: var(--primary-700); }
            .border-primary-100 { border-color: var(--primary-100); }
            .focus\:ring-primary-500:focus { --tw-ring-color: var(--primary-500); }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-100 py-12 mt-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </div>
                            <span class="text-xl font-black text-gray-900 tracking-tight">CharityHub</span>
                        </div>
                        <div class="text-sm text-gray-500 font-medium">
                            &copy; {{ date('Y') }} CharityHub Organization. All rights reserved.
                        </div>
                        <div class="flex gap-6">
                            <a href="#" class="text-gray-400 hover:text-primary-600 transition">Twitter</a>
                            <a href="#" class="text-gray-400 hover:text-primary-600 transition">Instagram</a>
                            <a href="#" class="text-gray-400 hover:text-primary-600 transition">LinkedIn</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

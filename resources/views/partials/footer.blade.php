<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                    <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="font-bold text-2xl tracking-tight">CharityHub</span>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed">Empowering communities and supporting causes that matter around the world. Every action counts.</p>
            </div>
            <div>
                <h3 class="text-xs font-semibold tracking-wider uppercase mb-4 text-gray-300">Platform</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('campaigns.index') }}" class="text-gray-400 hover:text-white text-sm transition">Browse Campaigns</a></li>
                    <li><a href="{{ route('volunteer.index') }}" class="text-gray-400 hover:text-white text-sm transition">Become a Volunteer</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition">Success Stories</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-semibold tracking-wider uppercase mb-4 text-gray-300">Legal</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition">Terms of Service</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm transition">Cookie Policy</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xs font-semibold tracking-wider uppercase mb-4 text-gray-300">Connect</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition bg-gray-800 p-2 rounded-full hover:bg-primary-600">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-gray-800 text-center flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm mb-4 md:mb-0">&copy; {{ date('Y') }} CharityHub. All rights reserved.</p>
            <div class="flex items-center space-x-2 text-gray-500 text-sm">
                <span>Made with</span>
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                <span>for a better world.</span>
            </div>
        </div>
    </div>
</footer>

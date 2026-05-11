<div class="w-64 flex-shrink-0 hidden lg:block">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-extrabold text-gray-900 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Admin Panel
            </h3>
        </div>
        <nav class="p-3 space-y-1">
            {{-- Overview --}}
            <p class="px-3 pt-2 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Overview</p>
            <a href="{{ route('custom_admin.dashboard') }}"
               class="{{ request()->routeIs('custom_admin.dashboard') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Dashboard
            </a>
            <a href="{{ route('custom_admin.donations.index') }}"
               class="{{ request()->routeIs('custom_admin.donations.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Donations Ledger
            </a>
            <a href="{{ route('custom_admin.campaigns.index') }}"
               class="{{ request()->routeIs('custom_admin.campaigns.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Campaigns
            </a>
            <a href="{{ route('custom_admin.donors') }}"
               class="{{ request()->routeIs('custom_admin.donors*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Donors
            </a>

            {{-- Volunteer Management --}}
            <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Volunteers</p>
            <a href="{{ route('custom_admin.volunteers.index') }}"
               class="{{ request()->routeIs('custom_admin.volunteers.*') ? 'bg-violet-50 text-violet-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition flex items-center justify-between">
                <span>Volunteers</span>
            </a>
            <a href="{{ route('custom_admin.schedules.index') }}"
               class="{{ request()->routeIs('custom_admin.schedules.*') ? 'bg-violet-50 text-violet-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Event Schedules
            </a>
            <a href="{{ route('custom_admin.volunteer-slots.index') }}"
               class="{{ request()->routeIs('custom_admin.volunteer-slots.*') ? 'bg-violet-50 text-violet-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Slot Requests
            </a>
            <a href="{{ route('custom_admin.volunteer-hours.index') }}"
               class="{{ request()->routeIs('custom_admin.volunteer-hours.*') ? 'bg-violet-50 text-violet-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Hour Logs
            </a>

            {{-- Filament Panel --}}
            <p class="px-3 pt-4 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Advanced</p>
            <a href="/admin" target="_blank"
               class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold block px-4 py-2.5 rounded-lg text-sm transition flex items-center gap-2">
                <span>Filament Panel</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="{{ route('custom_admin.impact-reports.index') }}"
               class="{{ request()->routeIs('custom_admin.impact-reports.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-semibold' }} block px-4 py-2.5 rounded-lg text-sm transition">
                Impact Reports
            </a>
        </nav>
    </div>
</div>

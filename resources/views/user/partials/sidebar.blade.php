<div class="w-full lg:w-64 flex-shrink-0">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
        {{-- User Avatar & Info --}}
        <div class="p-6 border-b border-slate-50 text-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=EFF6FF&color=1D4ED8&size=128"
                 alt="Avatar"
                 class="w-20 h-20 rounded-2xl mx-auto border-4 border-slate-50 shadow-sm mb-3">
            <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ auth()->user()->name }}</h3>
            <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Member</p>
        </div>

        {{-- Navigation --}}
        <nav class="p-3 space-y-1">
            <a href="{{ route('user.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold transition-all
                      {{ request()->routeIs('user.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Overview
            </a>

            <a href="{{ route('donor.donations.history') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold transition-all
                      {{ request()->routeIs('donor.donations*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Donation History
            </a>

            <a href="{{ route('user.profile') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold transition-all
                      {{ request()->routeIs('user.profile') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Account Profile
            </a>

            <div class="pt-2 mt-2 border-t border-slate-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-rose-600 hover:bg-rose-50 transition-all">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </nav>
    </div>

    {{-- Help Card --}}
    <div class="mt-5 bg-gradient-to-br from-slate-900 to-blue-900 rounded-3xl p-5 text-white relative overflow-hidden shadow-xl shadow-blue-900/20">
        <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
        <h4 class="font-bold text-sm mb-1 relative z-10">Need Help?</h4>
        <p class="text-[11px] text-blue-200/80 leading-relaxed mb-4 relative z-10">Our team is here for any donation or account questions.</p>
        <a href="mailto:support@charityhub.org"
           class="inline-block px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-bold border border-white/10 transition-all relative z-10">
            Contact Support
        </a>
    </div>
</div>

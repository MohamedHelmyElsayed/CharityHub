<div wire:poll.8s class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
    <form wire:submit.prevent="$refresh" class="p-8 border-b border-slate-100 flex flex-wrap gap-6 items-end bg-slate-50/50">
        <div class="flex-1 min-w-[250px]">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Search Donor</label>
            <input type="text" wire:model="search" class="w-full border-slate-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-3 px-4 border text-sm font-medium" placeholder="Name, email, or TXN ID...">
        </div>
        <div class="w-56">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status</label>
            <select wire:model="status" class="w-full border-slate-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-3 px-4 border bg-white text-sm font-medium">
                <option value="All Statuses">All Statuses</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">Filter</button>
            @if(!empty($search) || ($status !== 'All Statuses' && !empty($status)))
                <button type="button" wire:click="$set('search', ''); $set('status', 'All Statuses');" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition">Clear</button>
            @endif
        </div>
        <!-- Livewire Loading Indicator -->
        <div wire:loading wire:target="search, status, $refresh" class="flex items-center gap-3 ml-auto">
            <span class="text-sm font-bold text-indigo-600 animate-pulse">Updating...</span>
        </div>
    </form>
    
    <div class="overflow-x-auto relative">
        <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 transition-all duration-300"></div>
        <table class="w-full">
            <thead class="bg-slate-50/50">
                <tr>
                    <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Transaction Info</th>
                    <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Donor Details</th>
                    <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Amount & Gateway</th>
                    <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status & Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($donations as $don)
                <tr class="hover:bg-slate-50/50 transition-colors" wire:key="donation-{{ $don->id }}">
                    <td class="px-8 py-6 whitespace-nowrap">
                        <div class="text-sm font-mono font-bold text-slate-900">{{ $don->idempotency_key ?? 'TXN-'.$don->id }}</div>
                        <div class="text-xs font-medium text-slate-400 mt-1">{{ $don->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="px-8 py-6 whitespace-nowrap">
                        <div class="text-sm font-bold text-slate-900">{{ $don->donor ? $don->donor->name : ($don->user ? $don->user->name : 'Anonymous') }}</div>
                        <div class="text-xs font-medium text-slate-500 mt-1">{{ $don->donor ? $don->donor->email : ($don->user ? $don->user->email : 'N/A') }}</div>
                        <div class="text-xs font-bold text-indigo-600 mt-1">To: {{ $don->campaign ? $don->campaign->title : 'General Fund' }}</div>
                    </td>
                    <td class="px-8 py-6 whitespace-nowrap">
                        <div class="text-lg font-extrabold text-slate-900">EGP {{ number_format($don->amount, 2) }}</div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 uppercase tracking-widest">{{ $don->type === 'recurring' ? 'Recurring' : 'One-time' }}</span>
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-md {{ strtolower($don->gateway) === 'stripe' ? 'bg-indigo-100 text-indigo-700' : 'bg-rose-100 text-rose-700' }} uppercase tracking-widest">{{ $don->gateway }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 whitespace-nowrap flex flex-col items-start gap-3">
                        @if($don->status === 'completed')
                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-emerald-100 text-emerald-700 uppercase tracking-widest">
                                Completed
                            </span>
                            @if(!$don->isRefunded())
                                <div x-data="{ openModal: false }">
                                    <button @click="openModal = true" class="text-[10px] font-bold text-rose-600 hover:text-rose-800 underline uppercase tracking-widest transition-colors">Make Refund</button>
                                    
                                    <!-- Refund Modal -->
                                    <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="openModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" aria-hidden="true" @click="openModal = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="openModal" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                                                <form action="{{ route('custom_admin.donations.refund', $don->id) }}" method="POST">
                                                    @csrf
                                                    <div class="bg-white p-8">
                                                        <div class="text-center">
                                                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-50 mb-6">
                                                                <svg class="h-8 w-8 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                </svg>
                                                            </div>
                                                            <h3 class="text-2xl font-bold text-slate-900" id="modal-title">Refund Donation</h3>
                                                            <div class="mt-6 text-left">
                                                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Refund Amount (EGP)</label>
                                                                <input type="number" name="refund_amount" step="0.01" value="{{ $don->amount }}" max="{{ $don->amount }}" required class="w-full border-slate-200 rounded-xl shadow-sm focus:ring-rose-500 focus:border-rose-500 py-3 px-4 border text-sm font-medium">
                                                                
                                                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-4">Reason for Refund</label>
                                                                <textarea name="reason" required class="w-full border-slate-200 rounded-xl shadow-sm focus:ring-rose-500 focus:border-rose-500 py-3 px-4 border text-sm font-medium resize-none h-24" placeholder="Briefly explain why this is being refunded..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-slate-50/80 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-100">
                                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-rose-600 text-sm font-bold text-white hover:bg-rose-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">Process Refund</button>
                                                        <button type="button" @click="openModal = false" class="w-full inline-flex justify-center rounded-xl border border-slate-200 px-6 py-3 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif($don->status === 'refunded')
                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-slate-100 text-slate-600 uppercase tracking-widest">
                                Refunded
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-amber-100 text-amber-700 uppercase tracking-widest">
                                {{ $don->status }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-slate-400 font-bold">No donations found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($donations, 'links') && $donations->hasPages())
    <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
        {{ $donations->links() }}
    </div>
    @endif
</div>

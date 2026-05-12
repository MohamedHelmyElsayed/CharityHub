@extends('layouts.app')

@section('title', 'Donors List — Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Donors Directory</h1>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <form action="{{ route('custom_admin.donors') }}" method="GET" class="flex-1 max-w-md relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" 
                               placeholder="Search by name or email...">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Donor</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total Donations</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Last Activity</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($donors as $donor)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600">
                                            {{ substr($donor->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $donor->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $donor->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $donor->donations->count() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-primary-600">
                                    ${{ number_format($donor->donations->sum('amount'), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-medium">
                                    {{ $donor->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No donors found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($donors->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $donors->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

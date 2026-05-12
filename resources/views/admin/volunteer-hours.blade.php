@extends('layouts.app')

@section('title', 'Volunteer Hours Approval — Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Admin Content -->
        <div class="flex-1">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Volunteer Hours Approval</h1>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Volunteer</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Event</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hours</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($hourLogs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-gray-900">{{ $log->volunteer->display_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->volunteer->email }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->attendanceLog?->shift?->event?->title ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->attendanceLog?->shift?->title ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-500">
                                    {{ $log->attendanceLog?->shift?->shift_date ? \Carbon\Carbon::parse($log->attendanceLog->shift->shift_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-5 text-sm font-black text-primary-600">
                                    {{ number_format($log->calculated_hours, 2) }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'approved' => 'bg-green-100 text-green-800',
                                            'adjusted' => 'bg-blue-100 text-blue-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'pending_review' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $statusLabels = [
                                            'approved' => 'Approved',
                                            'adjusted' => 'Adjusted',
                                            'rejected' => 'Declined',
                                            'pending_review' => 'Pending Review',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-[10px] font-bold rounded-full uppercase tracking-widest {{ $statusColors[$log->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$log->status] ?? str_replace('_', ' ', $log->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                    @if($log->status === 'pending_review')
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('custom_admin.volunteer-hours.decline', $log->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 font-bold text-xs shadow-sm transition">Decline</button>
                                        </form>
                                        <form action="{{ route('custom_admin.volunteer-hours.approve', $log->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <div class="relative">
                                                <input type="number" step="0.01" name="adjusted_hours" placeholder="Hours" value="{{ $log->calculated_hours }}"
                                                       class="w-20 pr-1 text-[11px] font-bold rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                                                       title="Adjust Calculated Hours">
                                            </div>
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 font-extrabold text-[10px] uppercase tracking-wider shadow-lg shadow-emerald-200 transition-all hover:-translate-y-0.5 active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                Approve
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No hour logs pending approval.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

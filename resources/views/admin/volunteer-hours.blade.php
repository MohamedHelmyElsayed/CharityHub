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
                                    <span class="px-3 py-1 inline-flex text-[10px] font-bold rounded-full uppercase tracking-widest {{ $log->status === 'approved' || $log->status === 'adjusted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ str_replace('_', ' ', $log->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                    @if($log->status === 'pending_review')
                                    <form action="{{ route('custom_admin.volunteer-hours.approve', $log->id) }}" method="POST" class="flex items-center justify-end gap-2">
                                        @csrf
                                        <input type="number" step="0.01" name="adjusted_hours" placeholder="Override Hours" value="{{ $log->calculated_hours }}" class="w-24 text-xs rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" title="Adjust Calculated Hours">
                                        <button type="submit" class="px-3 py-1.5 bg-primary-600 text-white rounded-md hover:bg-primary-700 font-bold text-xs shadow-sm transition">Approve</button>
                                    </form>
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

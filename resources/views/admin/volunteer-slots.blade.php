@extends('layouts.app')

@section('title', 'Volunteer Slot Requests - Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1 space-y-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Custom Volunteer Slot Requests</h1>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Volunteer</th>
                                <th class="px-6 py-4">Date / Time</th>
                                <th class="px-6 py-4">Campaign</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($slotRequests as $req)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $req->volunteer?->name ?? $req->volunteer?->user?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $req->volunteer?->email ?? $req->volunteer?->user?->email ?? '' }}</div>
                                    @if($req->notes)
                                        <div class="text-xs text-gray-600 mt-2 bg-gray-100 p-2 rounded">
                                            <strong>Notes:</strong> {{ $req->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($req->requested_date)->format('M d, Y') }}</div>
                                    <div class="text-gray-500">{{ substr($req->requested_start_time, 0, 5) }} - {{ substr($req->requested_end_time, 0, 5) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $req->campaign?->title ?? 'General' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($req->status === 'approved')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Approved</span>
                                        @if($req->completed_at)
                                            <span class="block mt-2 text-xs text-green-600 font-bold">Completed</span>
                                        @endif
                                    @elseif($req->status === 'rejected')
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Rejected</span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-y-2">
                                    @if($req->status === 'pending')
                                        <form action="{{ route('custom_admin.volunteer-slots.approve', $req->id) }}" method="POST" class="inline-block w-full">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-emerald-500 text-white text-xs font-bold rounded hover:bg-emerald-600 transition shadow-sm">Approve</button>
                                        </form>
                                        <form action="{{ route('custom_admin.volunteer-slots.reject', $req->id) }}" method="POST" class="inline-block w-full">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-white border border-red-200 text-red-500 text-xs font-bold rounded hover:bg-red-50 transition shadow-sm" onclick="return confirm('Are you sure you want to reject this request?')">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Processed</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No slot requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $slotRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

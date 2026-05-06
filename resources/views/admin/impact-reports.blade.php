@extends('layouts.app')

@section('title', 'Impact Reports — Admin')

@section('content')
<div class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-8">
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Admin Content -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 class="text-3xl font-extrabold text-gray-900">Impact Reports</h1>
                <a href="{{ route('custom_admin.impact-reports.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Report
                </a>
            </div>

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
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Report</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Campaign</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Beneficiaries</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($reports as $report)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-gray-900">{{ $report->title }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $report->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600 font-medium">
                                    {{ $report->campaign->title }}
                                </td>
                                <td class="px-6 py-5 text-sm font-bold text-primary-600">
                                    {{ number_format($report->beneficiary_count) }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $report->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('custom_admin.impact-reports.edit', $report->id) }}" class="text-blue-600 hover:text-blue-800 mr-4 font-bold">Edit</a>
                                    <form action="{{ route('custom_admin.impact-reports.destroy', $report->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-bold" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No impact reports found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($reports->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $reports->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

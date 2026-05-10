@extends('layouts.app')

@section('title', 'Manage Donations')

@section('content')
<div class="bg-slate-50/50 min-h-screen font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex gap-8">
        
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')

        <div class="flex-1">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Donations Ledger</h1>
                    <p class="text-slate-500 font-medium mt-2">Manage and review all financial contributions.</p>
                </div>
            </div>

            <livewire:admin-donation-ledger />
        </div>
    </div>
</div>
@endsection

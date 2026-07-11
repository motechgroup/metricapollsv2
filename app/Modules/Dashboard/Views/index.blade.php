@extends('Dashboard::admin-layout')

@section('title', 'Admin Dashboard')
@section('page_title', 'System Dashboard')

@section('content')
<!-- Dashboard Welcome Header -->
<div class="mb-8 border-b border-gray-200 pb-5">
    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Welcome back, {{ auth()->user()->name }}</h1>
    <p class="mt-2 text-sm text-gray-500">Here's a summary of the Metrica Polls platform metrics and operations today.</p>
</div>

<!-- Statistics Widgets Grid -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-10">
    <!-- Stat 1: Panelists -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Registered Panelists</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-brand-navy">{{ $stats['total_panelists'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">Active research respondents</div>
    </div>
    <!-- Stat 2: Clients -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Corporate Clients</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-brand-navy">{{ $stats['total_clients'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">Organizations &amp; agencies</div>
    </div>
    <!-- Stat 3: Projects -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Active Projects</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-brand-navy">{{ $stats['total_projects'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">CRM campaigns in progress</div>
    </div>
    <!-- Stat 4: Surveys -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Active Paid Surveys</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-brand-navy">{{ $stats['active_surveys'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">Campaigns &amp; training tests</div>
    </div>
    <!-- Stat 5: Billed -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Total Billed Revenue</dt>
        <dd class="mt-1 text-2xl font-bold tracking-tight text-brand-blue">KES {{ number_format($stats['total_billed'], 2) }}</dd>
        <div class="mt-2 text-xs text-gray-500">Total invoiced amount</div>
    </div>
    <!-- Stat 6: Collections -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Collected Income</dt>
        <dd class="mt-1 text-2xl font-bold tracking-tight text-green-700">KES {{ number_format($stats['total_paid'], 2) }}</dd>
        <div class="mt-2 text-xs text-gray-500">Paid and cleared client invoices</div>
    </div>
</div>

<!-- Administration Quick Actions -->
<div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Administration Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @can('manage users')
        <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-brand-navy transition text-sm font-semibold text-gray-900 bg-white">
            <span>Manage Users &amp; Panelists</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endcan
        @can('manage roles')
        <a href="{{ route('admin.roles') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-brand-navy transition text-sm font-semibold text-gray-900 bg-white">
            <span>Configure RBAC Roles</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endcan
        
        <a href="{{ route('admin.surveys') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-brand-navy transition text-sm font-semibold text-gray-900 bg-white">
            <span>Manage Surveys &amp; Tests</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <a href="{{ route('admin.finances') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-brand-navy transition text-sm font-semibold text-gray-900 bg-white">
            <span>Company Finances &amp; Invoices</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <a href="{{ route('admin.projects') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-brand-navy transition text-sm font-semibold text-gray-900 bg-white">
            <span>Track Client Projects</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <a href="{{ route('admin.research-requests') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-brand-navy transition text-sm font-semibold text-gray-900 bg-white">
            <span>Review Quote Requests</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        @can('manage settings')
        <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-brand-navy transition text-sm font-semibold text-gray-900 bg-white">
            <span>Update Platform Settings</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endcan
    </div>
</div>
@endsection

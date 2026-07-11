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
    <!-- Stat 1 -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Total Registered Users</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_users'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">System user profiles</div>
    </div>
    <!-- Stat 2 -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Security Roles</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total_roles'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">Spatie RBAC Roles</div>
    </div>
    <!-- Stat 3 -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Active Surveys</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['active_surveys'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">Live response collection</div>
    </div>
    <!-- Stat 4 -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Average Response Rate</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['response_rate'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">Overall cohort submission rate</div>
    </div>
    <!-- Stat 5 -->
    <div class="overflow-hidden rounded-lg bg-white border border-gray-200 p-6 shadow-sm">
        <dt class="truncate text-sm font-medium text-gray-500">Research Projects</dt>
        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['active_projects'] }}</dd>
        <div class="mt-2 text-xs text-gray-500">Active CRM operations</div>
    </div>
</div>

<!-- Administration Quick Actions -->
<div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Administration Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @can('manage users')
        <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-gray-950 transition text-sm font-semibold text-gray-900 bg-white">
            <span>Manage System Users</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endcan
        @can('manage roles')
        <a href="{{ route('admin.roles') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-gray-950 transition text-sm font-semibold text-gray-900 bg-white">
            <span>Configure RBAC Roles</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endcan
        @can('manage settings')
        <a href="{{ route('admin.settings') }}" class="inline-flex items-center justify-between p-4 rounded-md border border-gray-200 hover:border-gray-950 transition text-sm font-semibold text-gray-900 bg-white">
            <span>Update Platform Settings</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @endcan
    </div>
</div>
@endsection

@php
    $theme = \App\Models\Setting::getValue('site_theme', 'Light');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full {{ $theme === 'Dark' ? 'bg-brand-navy dark' : ($theme === 'Glassmorphism' ? 'bg-brand-navy' : 'bg-gray-50') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal') - {{ \App\Models\Setting::getValue('site_title', 'Metrica Polls') }}</title>
    <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::getValue('site_favicon', 'favicon.png')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @if($theme === 'Dark')
    <style>
        body { background-color: #13254A !important; color: #f3f4f6 !important; }
        .bg-white { background-color: #1b2e5a !important; color: #f3f4f6 !important; border-color: #2b3f72 !important; }
        .text-gray-900 { color: #f3f4f6 !important; }
        .text-gray-600 { color: #d1d5db !important; }
        .text-gray-700 { color: #e5e7eb !important; }
        .text-gray-800 { color: #f3f4f6 !important; }
        .text-gray-500 { color: #9ca3af !important; }
        .border-gray-200, .border-gray-100 { border-color: #2b3f72 !important; }
        input, select, textarea { background-color: #13254A !important; border-color: #2b3f72 !important; color: #ffffff !important; }
        .bg-gray-50 { background-color: #13254A !important; }
        aside { background-color: #1b2e5a !important; border-color: #2b3f72 !important; }
        nav a { color: #d1d5db !important; }
        nav a:hover, nav a.bg-gray-50 { background-color: #2b3f72 !important; color: #ffffff !important; }
    </style>
    @elseif($theme === 'Glassmorphism')
    <style>
        body { 
            background: linear-gradient(135deg, #13254A 0%, #0A58CA 50%, #00D2C4 100%) !important; 
            color: #ffffff !important;
            background-attachment: fixed !important;
        }
        .bg-white { 
            background-color: rgba(255, 255, 255, 0.08) !important; 
            backdrop-filter: blur(12px) !important; 
            -webkit-backdrop-filter: blur(12px) !important; 
            border: 1px solid rgba(255, 255, 255, 0.15) !important; 
            color: #ffffff !important; 
        }
        .text-gray-900, .text-gray-800, .text-gray-700 { color: #ffffff !important; }
        .text-gray-600, .text-gray-500 { color: rgba(255, 255, 255, 0.75) !important; }
        .border-gray-200, .border-gray-100 { border-color: rgba(255, 255, 255, 0.15) !important; }
        input, select, textarea { 
            background-color: rgba(0, 0, 0, 0.2) !important; 
            border: 1px solid rgba(255, 255, 255, 0.15) !important; 
            color: #ffffff !important; 
        }
        .bg-gray-50 { background-color: rgba(0, 0, 0, 0.1) !important; }
        aside { 
            background-color: rgba(19, 37, 74, 0.6) !important; 
            backdrop-filter: blur(16px) !important; 
            border-color: rgba(255, 255, 255, 0.15) !important; 
        }
        nav a { color: rgba(255, 255, 255, 0.8) !important; }
        nav a:hover, nav a.bg-gray-50 { 
            background-color: rgba(255, 255, 255, 0.15) !important; 
            color: #ffffff !important; 
        }
    </style>
    @elseif($theme === 'Light')
    <style>
        body { background-color: #f9fafb !important; color: #111827 !important; }
        aside { background-color: #13254A !important; border-color: #0A58CA !important; }
        aside a { color: #e5e7eb !important; }
        aside a:hover, aside a.bg-gray-50 { background-color: #0A58CA !important; color: #ffffff !important; }
        aside span { background-color: #00D2C4 !important; color: #13254A !important; }
        header { background-color: #ffffff !important; border-color: #e5e7eb !important; }
    </style>
    @endif
</head>
<body class="h-full font-sans antialiased {{ $theme === 'Dark' ? 'text-gray-100 bg-brand-navy' : ($theme === 'Glassmorphism' ? 'text-white bg-transparent' : 'text-gray-900 bg-gray-50') }}" x-data="{ mobileMenuOpen: false }">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col border-r border-gray-200 bg-white">
        <div class="flex h-16 shrink-0 items-center border-b border-gray-100 px-6">
            <a href="{{ route('admin.index') }}" class="flex items-center gap-2">
                <img src="{{ asset(\App\Models\Setting::getValue('site_logo', 'images/logo.png')) }}" alt="Metrica Polls Logo" class="h-6 w-auto" onerror="this.onerror=null; this.src='/favicon.png';">
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">PORTAL</span>
            </a>
        </div>
        <nav class="flex flex-1 flex-col px-4 py-6">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <li>
                            <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Dashboard
                            </a>
                        </li>
                        @role('Super Admin|Admin')
                        <li>
                            <a href="{{ route('admin.crm') }}" class="{{ request()->routeIs('admin.crm') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Client Directory (CRM)
                            </a>
                        </li>
                        @endrole
                        @role('Super Admin|Admin|Project Manager|Field Manager')
                        <li>
                            <a href="{{ route('admin.research-requests') }}" class="{{ request()->routeIs('admin.research-requests') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Review Requests
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.projects') }}" class="{{ request()->routeIs('admin.projects') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Project Operations
                            </a>
                        </li>
                        @endrole
                        @role('Super Admin|Admin|Field Agent')
                        <li>
                            <a href="{{ route('agent.assignments') }}" class="{{ request()->routeIs('agent.assignments') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                My Field Assignments
                            </a>
                        </li>
                        @endrole
                        @role('Super Admin|Admin|Project Manager')
                        <li>
                            <a href="{{ route('admin.polls.index') }}" class="{{ (request()->routeIs('admin.polls.index') || request()->routeIs('admin.polls.create')) ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                AI Poll Builder
                            </a>
                        </li>
                        @endrole
                        @can('manage users')
                        <li>
                            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                User Management
                            </a>
                        </li>
                        @endcan
                        @can('manage roles')
                        <li>
                            <a href="{{ route('admin.roles') }}" class="{{ request()->routeIs('admin.roles') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Roles & Permissions
                            </a>
                        </li>
                        @endcan
                        @can('manage settings')
                        <li>
                            <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Settings
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="{{ route('admin.finances') }}" class="{{ request()->routeIs('admin.finances') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Finances & Gateways
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.surveys') }}" class="{{ request()->routeIs('admin.surveys') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Surveys & Tests
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="mt-auto border-t border-gray-100 pt-4">
                    <a href="{{ route('corporate.index') }}" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                        Exit to Website
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Mobile Drawer/Sidebar -->
    <div x-show="mobileMenuOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/80" @click="mobileMenuOpen = false"></div>
        <div class="fixed inset-0 flex">
            <div class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4">
                <div class="flex shrink-0 items-center px-6 pb-4 border-b border-gray-100 gap-2">
                    <img src="{{ asset(\App\Models\Setting::getValue('site_logo', 'images/logo.png')) }}" alt="Metrica Polls Logo" class="h-6 w-auto" onerror="this.onerror=null; this.src='/favicon.png';">
                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">PORTAL</span>
                </div>
                <nav class="mt-6 flex flex-1 flex-col px-4">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <a href="{{ route('admin.index') }}" class="text-gray-900 bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6">
                                        Dashboard
                                    </a>
                                </li>
                                @role('Super Admin|Admin')
                                <li>
                                    <a href="{{ route('admin.crm') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Client Directory (CRM)
                                    </a>
                                </li>
                                @endrole
                                @role('Super Admin|Admin|Project Manager|Field Manager')
                                <li>
                                    <a href="{{ route('admin.research-requests') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Review Requests
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.projects') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Project Operations
                                    </a>
                                </li>
                                @endrole
                                @role('Super Admin|Admin|Field Agent')
                                <li>
                                    <a href="{{ route('agent.assignments') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        My Field Assignments
                                    </a>
                                </li>
                                @endrole
                                @role('Super Admin|Admin|Project Manager')
                                <li>
                                    <a href="{{ route('admin.polls.index') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        AI Poll Builder
                                    </a>
                                </li>
                                @endrole
                                @can('manage users')
                                <li>
                                    <a href="{{ route('admin.users') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        User Management
                                    </a>
                                </li>
                                @endcan
                                @can('manage roles')
                                <li>
                                    <a href="{{ route('admin.roles') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Roles & Permissions
                                    </a>
                                </li>
                                @endcan
                                @can('manage settings')
                                <li>
                                    <a href="{{ route('admin.settings') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Settings
                                    </a>
                                </li>
                                @endcan
                                <li>
                                    <a href="{{ route('admin.finances') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Finances & Gateways
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.surveys') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Surveys & Tests
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Section -->
    <div class="lg:pl-64 flex flex-col min-h-full">
        <!-- Top Nav -->
        <header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
            <!-- Mobile Menu Toggle Button -->
            <button @click="mobileMenuOpen = true" type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                <span class="sr-only">Open sidebar</span>
                <!-- Hamburger Icon -->
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Breadcrumbs -->
            <div class="flex-1 flex items-center text-sm font-medium text-gray-500">
                <span class="hover:text-gray-700 cursor-pointer">Portal</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900">@yield('page_title', 'Dashboard')</span>
            </div>

            <!-- Profile Menu Dropdown -->
            <div class="flex items-center gap-x-4 lg:gap-x-6" x-data="{ open: false }">
                <div class="relative">
                    <button @click="open = !open" type="button" class="-m-1.5 flex items-center p-1.5 focus:outline-none" id="user-menu-button">
                        <span class="hidden lg:flex lg:items-center">
                            <span class="text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">{{ auth()->user()->name }}</span>
                            <!-- Role Badge -->
                            <span class="ml-2 inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                {{ auth()->user()->roles->first()->name ?? 'User' }}
                            </span>
                            <!-- Chevron -->
                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                    <!-- Dropdown Panel -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="display: none;">
                        <a href="#" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50 pointer-events-none" role="menuitem">Your Profile</a>
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50" role="menuitem">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="py-10 flex-grow">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>

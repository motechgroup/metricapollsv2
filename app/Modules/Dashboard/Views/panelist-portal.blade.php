<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Dashboard') - Metrica Panelist Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50" x-data="{ mobileMenuOpen: false }">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col border-r border-gray-200 bg-white">
        <div class="flex h-16 shrink-0 items-center border-b border-gray-100 px-6">
            <a href="{{ route('panelist.dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Metrica Polls Logo" class="h-6 w-auto">
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">PANEL</span>
            </a>
        </div>
        <nav class="flex flex-1 flex-col px-4 py-6">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <li>
                            <a href="{{ route('panelist.dashboard') }}" class="{{ request()->routeIs('panelist.dashboard') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Profile & Verification
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('panelist.wallet') }}" class="{{ request()->routeIs('panelist.wallet') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Wallet & Payouts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('panelist.qualifications') }}" class="{{ request()->routeIs('panelist.qualifications') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Qualification Tests
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('panelist.academy') }}" class="{{ request()->routeIs('panelist.academy') ? 'bg-gray-50 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                Academy Courses
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
                    <img src="{{ asset('images/logo.png') }}" alt="Metrica Polls Logo" class="h-6 w-auto">
                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">PANEL</span>
                </div>
                <nav class="mt-6 flex flex-1 flex-col px-4">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <a href="{{ route('panelist.dashboard') }}" class="text-gray-900 bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6">
                                        Profile & Verification
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('panelist.wallet') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Wallet & Payouts
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('panelist.qualifications') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Qualification Tests
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('panelist.academy') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex gap-x-3 rounded-md p-2 text-sm leading-6">
                                        Academy Courses
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
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Breadcrumbs -->
            <div class="flex-1 flex items-center text-sm font-medium text-gray-500">
                <span class="hover:text-gray-700 cursor-pointer">Panelist Hub</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-900">@yield('page_title', 'Dashboard')</span>
            </div>

            <!-- Profile Menu Dropdown -->
            <div class="flex items-center gap-x-4 lg:gap-x-6" x-data="{ open: false }">
                <div class="relative">
                    <button @click="open = !open" type="button" class="-m-1.5 flex items-center p-1.5 focus:outline-none" id="user-menu-button">
                        <span class="hidden lg:flex lg:items-center">
                            <span class="text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">{{ auth()->user()->name }}</span>
                            <span class="ml-2 inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                Panelist
                            </span>
                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                    <!-- Dropdown Panel -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="display: none;">
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

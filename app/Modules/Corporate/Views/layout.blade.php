<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', \App\Models\Setting::getValue('site_title', 'Metrica Polls')) - Marketing & Research Firm</title>
    <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::getValue('site_favicon', 'favicon.png')) }}">
    <meta name="description" content="{{ \App\Models\Setting::getValue('site_description', '') }}">
    <meta name="keywords" content="{{ \App\Models\Setting::getValue('site_seo_keywords', '') }}">
    {!! \App\Models\Setting::getValue('analytics_code', '') !!}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex flex-col min-h-full font-sans antialiased text-gray-950 bg-white">
    <!-- Header/Navigation -->
    <header class="border-b border-gray-100 bg-white sticky top-0 z-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex flex-shrink-0 items-center">
                    <a href="{{ route('corporate.index') }}" class="flex items-center">
                        <img src="{{ asset(\App\Models\Setting::getValue('site_logo', 'images/logo.png')) }}" alt="Metrica Polls Logo" class="h-8 w-auto" onerror="this.onerror=null; this.src='/favicon.png';">
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex space-x-8 text-sm font-medium">
                    <a href="{{ route('corporate.features') }}" class="text-gray-600 hover:text-gray-900 py-2">Features</a>
                    <a href="{{ route('corporate.pricing') }}" class="text-gray-600 hover:text-gray-900 py-2">Pricing</a>
                    <a href="{{ route('public.marketplace') }}" class="text-gray-600 hover:text-gray-900 py-2">Marketplace</a>
                    <a href="{{ route('public.opinion') }}" class="text-gray-600 hover:text-gray-900 py-2">Public Opinion</a>
                    <a href="{{ route('public.reports') }}" class="text-gray-600 hover:text-gray-900 py-2">AI Reports</a>
                    <a href="{{ route('corporate.about') }}" class="text-gray-600 hover:text-gray-900 py-2">About</a>
                    <a href="{{ route('corporate.contact') }}" class="text-gray-600 hover:text-gray-900 py-2">Contact</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center rounded-md bg-brand-navy px-4 py-2 text-sm font-medium text-white hover:bg-brand-blue transition duration-150">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Log out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Sign in</a>
                        <a href="{{ route('auth.register') }}" class="inline-flex items-center justify-center rounded-md bg-brand-navy px-4 py-2 text-sm font-medium text-white hover:bg-brand-blue transition duration-150">
                            Get started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-100">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <img src="{{ asset(\App\Models\Setting::getValue('site_logo', 'images/logo.png')) }}" alt="Metrica Polls Logo" class="h-6 w-auto" onerror="this.onerror=null; this.src='/favicon.png';">
                    <p class="text-sm text-gray-500">
                        {{ \App\Models\Setting::getValue('site_description', '') }}
                    </p>
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Platform</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="{{ route('corporate.features') }}" class="text-gray-600 hover:text-gray-900">Features</a></li>
                        <li><a href="{{ route('corporate.pricing') }}" class="text-gray-600 hover:text-gray-900">Pricing</a></li>
                        <li><a href="{{ route('public.marketplace') }}" class="text-gray-600 hover:text-gray-900">Marketplace</a></li>
                        <li><a href="{{ route('public.opinion') }}" class="text-gray-600 hover:text-gray-900">Public Opinion</a></li>
                        <li><a href="{{ route('public.reports') }}" class="text-gray-600 hover:text-gray-900">AI Reports</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Company</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="{{ route('corporate.about') }}" class="text-gray-600 hover:text-gray-900">About Us</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900">Careers</a></li>
                        <li><a href="{{ route('corporate.contact') }}" class="text-gray-600 hover:text-gray-900">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Compliance & Legal</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="#" class="text-gray-600 hover:text-gray-900">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-gray-900">Data Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-200 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-400">
                    {!! \App\Models\Setting::getValue('site_footer', '') !!}
                </p>
                <div class="flex space-x-6 text-sm text-gray-400">
                    <span>ISO 27001 Certified</span>
                    <span>GDPR Compliant</span>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>

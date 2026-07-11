@php
    $theme = \App\Models\Setting::getValue('site_theme', 'Light');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full {{ $theme === 'Dark' ? 'bg-brand-navy dark' : ($theme === 'Glassmorphism' ? 'bg-brand-navy' : 'bg-white') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', \App\Models\Setting::getValue('site_title', 'Metrica Polls')) - Marketing &amp; Research Firm</title>
    <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::getValue('site_favicon', 'favicon.png')) }}">
    <meta name="description" content="{{ \App\Models\Setting::getValue('site_description', '') }}">
    <meta name="keywords" content="{{ \App\Models\Setting::getValue('site_seo_keywords', '') }}">
    {!! \App\Models\Setting::getValue('analytics_code', '') !!}
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
        header { background-color: #1b2e5a !important; border-color: #2b3f72 !important; }
        footer { background-color: #13254A !important; border-color: #2b3f72 !important; }
        header a, footer a { color: #d1d5db !important; }
        header a:hover, footer a:hover { color: #ffffff !important; }
        section { background-color: #13254A !important; border-color: #2b3f72 !important; }
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
        header { 
            background-color: rgba(19, 37, 74, 0.6) !important; 
            backdrop-filter: blur(16px) !important; 
            border-color: rgba(255, 255, 255, 0.15) !important; 
        }
        footer { 
            background-color: rgba(19, 37, 74, 0.8) !important; 
            backdrop-filter: blur(16px) !important; 
            border-color: rgba(255, 255, 255, 0.15) !important; 
        }
        header a, footer a { color: rgba(255, 255, 255, 0.8) !important; }
        header a:hover, footer a:hover { color: #ffffff !important; }
        section { background-color: transparent !important; }
    </style>
    @endif
</head>
<body class="flex flex-col min-h-full font-sans antialiased {{ $theme === 'Dark' ? 'text-gray-100 bg-brand-navy' : ($theme === 'Glassmorphism' ? 'text-white bg-transparent' : 'text-gray-950 bg-white') }}">
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

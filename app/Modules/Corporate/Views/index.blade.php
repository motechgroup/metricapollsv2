@extends('Corporate::layout')

@section('title', 'Marketing & Research Firm')

@php
    $currentCountry = session('mock_geo_country', \App\Services\GeoLocationService::getCountryFromIp(request()->ip()));
    $currencyDetails = \App\Services\GeoLocationService::getCurrencyForCountry($currentCountry);
@endphp

@section('content')
<!-- Hero Section -->
<section class="relative py-24 sm:py-32 bg-white overflow-hidden border-b border-gray-100">
    <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-8">
                <!-- Location Indicator Badge -->
                <div class="inline-flex items-center gap-2 bg-gray-550/5 border border-gray-200 px-3 py-1.5 rounded-full text-xs font-semibold text-gray-700 animate-pulse">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
                    <span>Active Gateway: <strong class="text-gray-900">{{ $currentCountry }}</strong> (Local rewards paid in {{ $currencyDetails['code'] }})</span>
                </div>

                <h1 class="text-4xl font-extrabold tracking-tight text-brand-navy sm:text-6xl leading-tight">
                    Enterprise Marketing &amp; <span class="bg-clip-text text-transparent bg-gradient-to-r from-gray-900 via-gray-700 to-gray-555">Research Firm</span>
                </h1>
                <p class="text-lg text-gray-600 leading-relaxed max-w-2xl">
                    Scale your field operations, public opinion polls, and market research across millions of respondents. Metrica Polls combines offline field collection with real-time analytics to deliver executive quality insights.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('auth.register') }}" class="rounded-md bg-gray-900 px-6 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition duration-150 transform hover:-translate-y-0.5">
                        Create Research Account
                    </a>
                    <a href="{{ route('corporate.features') }}" class="rounded-md border border-gray-300 px-6 py-3.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition duration-150">
                        Explore Platform Capabilities
                    </a>
                </div>
            </div>
            
            <div class="lg:col-span-5 hidden lg:block">
                <!-- Graphic Hero Feature Card -->
                <div class="border border-gray-200 p-8 rounded-2xl shadow-lg bg-gray-50/50 backdrop-blur-sm space-y-6 relative">
                    <div class="absolute -top-3 -right-3 bg-gray-900 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow">
                        Live Analytics
                    </div>
                    <div class="space-y-2">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-widest">Active Census</span>
                        <h3 class="text-lg font-bold text-gray-900">Regional FMCG Preferences</h3>
                        <p class="text-xs text-gray-500">Continuous cohort tracking across East Africa &amp; Nigeria.</p>
                    </div>
                    <!-- Mock Data Progress -->
                    <div class="space-y-3.5 pt-2">
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-gray-700">
                                <span>Kenya (KES)</span>
                                <span>88% Quota</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-gray-900 h-full rounded-full" style="width: 88%"></div>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-gray-700">
                                <span>Nigeria (NGN)</span>
                                <span>74% Quota</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-gray-900 h-full rounded-full" style="width: 74%"></div>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold text-gray-700">
                                <span>Tanzania / Uganda</span>
                                <span>91% Quota</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-gray-900 h-full rounded-full" style="width: 91%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Logo Cloud (Trust Elements) -->
<section class="py-10 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs font-bold uppercase tracking-wider text-gray-400 mb-8">Trusted by leading organizations across the globe</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8 items-center justify-center opacity-60 filter grayscale hover:opacity-90 transition">
            <div class="flex justify-center text-sm font-extrabold text-gray-400 uppercase tracking-widest font-mono">World Bank</div>
            <div class="flex justify-center text-sm font-extrabold text-gray-400 uppercase tracking-widest font-mono">UNICEF</div>
            <div class="flex justify-center text-sm font-extrabold text-gray-400 uppercase tracking-widest font-mono">Safaricom</div>
            <div class="flex justify-center text-sm font-extrabold text-gray-400 uppercase tracking-widest font-mono">MTN Group</div>
            <div class="flex justify-center text-sm font-extrabold text-gray-400 uppercase tracking-widest font-mono">Airtel Africa</div>
        </div>
    </div>
</section>

<!-- Stats Grid -->
<section class="py-16 bg-gray-50 border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <dl class="grid grid-cols-1 gap-y-8 sm:grid-cols-3 text-center sm:text-left border border-gray-200 bg-white rounded-lg p-8 shadow-sm">
            <div class="flex flex-col gap-y-2 border-gray-100 sm:border-r sm:pr-8">
                <dt class="text-sm font-medium text-gray-500">Respondents Profiled</dt>
                <dd class="text-4xl font-bold tracking-tight text-gray-900 font-mono">10M+</dd>
            </div>
            <div class="flex flex-col gap-y-2 border-gray-100 sm:border-r sm:px-8">
                <dt class="text-sm font-medium text-gray-500">Offline Field Agents</dt>
                <dd class="text-4xl font-bold tracking-tight text-gray-900 font-mono">15,000+</dd>
            </div>
            <div class="flex flex-col gap-y-2 sm:pl-8">
                <dt class="text-sm font-medium text-gray-500">Data Synchronization Rate</dt>
                <dd class="text-4xl font-bold tracking-tight text-gray-900 font-mono">99.9%</dd>
            </div>
        </dl>
    </div>
</section>

<!-- Specialized Marketing & Research Services -->
<section class="py-20 sm:py-28 bg-gray-50 border-b border-gray-150">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16 space-y-2">
            <span class="text-xs font-bold text-gray-550 uppercase tracking-widest">Our Capabilities</span>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Specialized Marketing &amp; Research Services</h2>
            <p class="text-base text-gray-600">Tailored research methodologies, field studies, and outsourcing resources designed for enterprises, NGOs, and governments.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- 1. Public Policy & Social Research -->
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 uppercase tracking-tight">Public Policy &amp; Social Research</h3>
                    <p class="text-xs text-gray-550 mt-3 leading-relaxed">
                        Learn about people and societies to design products and social services that cater to various community needs.
                    </p>
                    <ul class="mt-6 space-y-3.5 text-xs font-semibold text-gray-600 border-t border-gray-100 pt-6">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Monitoring &amp; Evaluation
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Impact Assessment
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            KAP Study
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Longitudinal Study
                        </li>
                    </ul>
                </div>
                <a href="{{ auth()->check() && auth()->user()->hasRole('Client') ? route('client.requests.create', ['service' => 'Public Policy']) : (auth()->check() ? route('client.requests.create') : route('login')) }}" class="mt-8 block text-center rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Request for Quote
                </a>
            </div>

            <!-- 2. Consumer & Market Research -->
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between relative overflow-hidden">
                <span class="absolute top-3 right-3 bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                    Popular
                </span>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 uppercase tracking-tight">Consumer &amp; Market Research</h3>
                    <p class="text-xs text-gray-555 mt-3 leading-relaxed">
                        Evaluate communications and examine relationships among media endeavors and target audiences.
                    </p>
                    <ul class="mt-6 space-y-3.5 text-xs font-semibold text-gray-600 border-t border-gray-100 pt-6">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            The market
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            The Consumer
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Products or Services
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Target Market
                        </li>
                    </ul>
                </div>
                <a href="{{ auth()->check() && auth()->user()->hasRole('Client') ? route('client.requests.create', ['service' => 'Consumer Research']) : (auth()->check() ? route('client.requests.create') : route('login')) }}" class="mt-8 block text-center rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Request for Quote
                </a>
            </div>

            <!-- 3. Omnibus -->
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 uppercase tracking-tight">Omnibus</h3>
                    <p class="text-xs text-gray-555 mt-3 leading-relaxed">
                        Quickly gain insights from the general public without the overhead of custom tool development.
                    </p>
                    <ul class="mt-6 space-y-3.5 text-xs font-semibold text-gray-600 border-t border-gray-100 pt-6">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Face to face and CATI
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            High response rate
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Data collection tool design
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Cost effective studies
                        </li>
                    </ul>
                </div>
                <a href="{{ auth()->check() && auth()->user()->hasRole('Client') ? route('client.requests.create', ['service' => 'Omnibus']) : (auth()->check() ? route('client.requests.create') : route('login')) }}" class="mt-8 block text-center rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Request for Quote
                </a>
            </div>

            <!-- 4. Research Outsourcing -->
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between relative overflow-hidden">
                <span class="absolute top-3 right-3 bg-gray-100 text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">
                    Efficient
                </span>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 uppercase tracking-tight">Research Outsourcing</h3>
                    <p class="text-xs text-gray-555 mt-3 leading-relaxed">
                        Evaluate communication processes, relationships, and scale resources using our specialized CATI center.
                    </p>
                    <ul class="mt-6 space-y-3.5 text-xs font-semibold text-gray-600 border-t border-gray-100 pt-6">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Call center with 50+ executives
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Record time research delivery
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Products or Services
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Ad-hoc custom studies
                        </li>
                    </ul>
                </div>
                <a href="{{ auth()->check() && auth()->user()->hasRole('Client') ? route('client.requests.create', ['service' => 'Research Outsourcing']) : (auth()->check() ? route('client.requests.create') : route('login')) }}" class="mt-8 block text-center rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Request for Quote
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Research Workflow & Methodology Section -->
<section class="py-20 sm:py-28 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16 space-y-2">
            <span class="text-xs font-bold text-gray-550 uppercase tracking-widest">Our Methodology</span>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Data Quality Assurance Pipeline</h2>
            <p class="text-base text-gray-600">How Metrica guarantees verified, auditable research insights for enterprise decision making.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <div class="space-y-4">
                <div class="w-10 h-10 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">01</div>
                <h3 class="text-base font-bold text-gray-900">Dynamic Profiling</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Respondents undergo detailed demographic mapping and phone verification to prevent duplicate accounts and synthetic bots.</p>
            </div>
            <div class="space-y-4">
                <div class="w-10 h-10 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">02</div>
                <h3 class="text-base font-bold text-gray-900">Double Auditing</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Every study is paired with offline field coordinators logging verified coordinates and capturing local device signatures.</p>
            </div>
            <div class="space-y-4">
                <div class="w-10 h-10 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">03</div>
                <h3 class="text-base font-bold text-gray-900">Anti-Fraud Engine</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Algorithms run attention checks, evaluate response consistency, and detect click speeds to discard untrustworthy inputs.</p>
            </div>
            <div class="space-y-4">
                <div class="w-10 h-10 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-sm">04</div>
                <h3 class="text-base font-bold text-gray-900">Interactive Reporting</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Client portals update in real-time, matching raw CSV downloads with AI-generated research methodologies.</p>
            </div>
        </div>
    </div>
</section>

<!-- Available Paid Surveys & Qualification Tests -->
<section class="py-20 sm:py-28 bg-gray-50 border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16 space-y-2">
            <span class="text-xs font-bold text-green-700 bg-green-50 px-2 py-1 rounded-md uppercase tracking-wider">Earn with Metrica Panel</span>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Available Paid Surveys &amp; Training</h2>
            <p class="text-base text-gray-500">Sign up via Google Login to access verified consumer surveys and get paid directly to your local account.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($surveys as $survey)
            <div class="border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition bg-white flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        @if($survey->is_qualification)
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">TRAINING TEST</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-700/10">PAID SURVEY</span>
                        @endif
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-widest">Requires: {{ $survey->min_badge_level }} Badge</span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 leading-snug">{{ $survey->title }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $survey->description }}</p>
                </div>

                <div class="border-t border-gray-100 pt-6 flex items-center justify-between gap-4">
                    <div>
                        @if($survey->is_paid)
                            <p class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Estimated Payout</p>
                            @php
                                $localPayout = $survey->payout_amount * ($currencyDetails['rate'] / 100);
                            @endphp
                            <p class="text-lg font-extrabold text-green-600 font-mono">{{ $currencyDetails['symbol'] }} {{ number_format($localPayout, 2) }}</p>
                        @else
                            <p class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Estimated Payout</p>
                            <p class="text-lg font-extrabold text-blue-600">Verification &amp; Badges</p>
                        @endif
                    </div>
                    
                    <a href="{{ auth()->check() ? route('surveys.respond', $survey->id) : route('login') }}" class="rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow hover:bg-gray-800 transition">
                        {{ auth()->check() ? 'Take Survey' : 'Register to Start' }}
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-2 text-center py-12 bg-white border border-gray-200 rounded-lg">
                <p class="text-sm text-gray-500 italic">No survey campaigns are currently open to new respondents in {{ $currentCountry }}.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Call to Action Section (Premium Contrast Background) -->
<section class="py-24 bg-gray-950 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(55,65,81,0.3),transparent_60%)]"></div>
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 text-center space-y-8 relative z-10">
        <h2 class="text-3xl font-extrabold tracking-tight sm:text-5xl leading-tight">Ready to gather actionable insights?</h2>
        <p class="text-base text-gray-300 max-w-2xl mx-auto leading-relaxed">
            Metrica provides the tools, verified participant cohorts, and offline resources necessary to gather authentic data and inform strategic actions.
        </p>
        <div class="flex flex-wrap justify-center gap-4 pt-4">
            <a href="{{ route('auth.register') }}" class="rounded-md bg-white px-6 py-3.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 transition duration-150">
                Register as Client
            </a>
            <a href="{{ route('auth.register') }}?role=panelist" class="rounded-md border border-gray-700 px-6 py-3.5 text-sm font-semibold text-gray-300 hover:border-gray-500 hover:text-white transition duration-150">
                Join Panelist Network
            </a>
        </div>
    </div>
</section>

<!-- Interactive FAQ Section -->
<section class="py-20 sm:py-28 bg-white">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center space-y-2">
            <span class="text-xs font-bold text-gray-550 uppercase tracking-widest">FAQ</span>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Frequently Asked Questions</h2>
        </div>

        <div class="space-y-6 divide-y divide-gray-200">
            <div class="pt-6">
                <h3 class="text-base font-bold text-gray-900">How do panelists get paid?</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Payouts are calculated in points (100 Points = $1.00 USD) and converted dynamically to your geolocated country currency. Payout channels include M-Pesa, MTN Mobile Money, Airtel Money, and Bank Transfers.</p>
            </div>
            <div class="pt-6">
                <h3 class="text-base font-bold text-gray-900">How does Metrica ensure data quality?</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Our system uses strict anti-fraud checks, including timing traps, attention checking questions, and device fingerprinting. Panelists must graduate from Metrica Academy courses to qualify for higher paying campaigns.</p>
            </div>
            <div class="pt-6">
                <h3 class="text-base font-bold text-gray-900">Can I take surveys from any country?</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Metrica Polls operates exclusively in Kenya, Rwanda, Tanzania, Uganda, and Nigeria. Connections routed from VPNs or outside these regions are restricted automatically.</p>
            </div>
        </div>
    </div>
</section>
@endsection

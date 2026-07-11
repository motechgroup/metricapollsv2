@extends('Corporate::layout')

@section('title', 'Marketing & Research Firm')

@section('content')
<!-- Hero Section -->
<section class="py-20 sm:py-28 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <h1 class="text-4xl font-extrabold tracking-tight text-brand-navy sm:text-6xl">
                Enterprise Marketing & Research Firm
            </h1>
            <p class="mt-6 text-xl text-gray-600 leading-relaxed">
                Scale your field operations, public opinion polls, and market research across millions of respondents. Metrica Polls combines offline field collection with real-time analytics to deliver executive quality insights.
            </p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('auth.register') }}" class="rounded-md bg-brand-navy px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue transition duration-150">
                    Create Research Account
                </a>
                <a href="{{ route('corporate.features') }}" class="rounded-md border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition duration-150">
                    Explore Platform Capabilities
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Grid -->
<section class="py-16 bg-gray-50 border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <dl class="grid grid-cols-1 gap-y-8 sm:grid-cols-3 text-center sm:text-left border border-gray-200 bg-white rounded-lg p-8 shadow-sm">
            <div class="flex flex-col gap-y-2 border-gray-100 sm:border-r sm:pr-8">
                <dt class="text-sm font-medium text-gray-500">Respondents Profiled</dt>
                <dd class="text-4xl font-bold tracking-tight text-gray-900">10M+</dd>
            </div>
            <div class="flex flex-col gap-y-2 border-gray-100 sm:border-r sm:px-8">
                <dt class="text-sm font-medium text-gray-500">Offline Field Agents</dt>
                <dd class="text-4xl font-bold tracking-tight text-gray-900">15,000+</dd>
            </div>
            <div class="flex flex-col gap-y-2 sm:pl-8">
                <dt class="text-sm font-medium text-gray-500">Data Synchronization Rate</dt>
                <dd class="text-4xl font-bold tracking-tight text-gray-900">99.9%</dd>
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

<!-- Available Paid Surveys & Qualification Tests -->
<section class="py-20 sm:py-28 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16 space-y-2">
            <span class="text-xs font-bold text-green-700 bg-green-50 px-2 py-1 rounded-md uppercase tracking-wider">Earn with Metrica Panel</span>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Available Paid Surveys &amp; Training</h2>
            <p class="text-base text-gray-500">Sign up via Google Login to access verified consumer surveys and get paid directly to your M-Pesa account.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($surveys as $survey)
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
                            <p class="text-lg font-extrabold text-green-600">KES {{ number_format($survey->payout_amount, 2) }}</p>
                        @else
                            <p class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Estimated Payout</p>
                            <p class="text-lg font-extrabold text-blue-600">Verification &amp; Badges</p>
                        @endif
                    </div>
                    
                    <a href="{{ auth()->check() ? route('surveys.respond', $survey->id) : route('login') }}" class="rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow hover:bg-gray-800 transition">
                        {{ auth()->check() ? 'Take Survey' : 'Register via Google to Start' }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Core Modules Overview -->
<section class="py-20 sm:py-28 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                A Unified Research Ecosystem
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Run surveys, manage panels, track payments, orchestrate field operations, and access AI-assisted analytics from one corporate portal.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition duration-150 bg-white">
                <div class="text-gray-900 font-bold text-lg mb-2">Research Management</div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Set up complex research projects, target specific cohorts, manage quotas, and coordinate stakeholders from a unified portal.
                </p>
            </div>
            <!-- Card 2 -->
            <div class="border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition duration-150 bg-white">
                <div class="text-gray-900 font-bold text-lg mb-2">Offline Data Collection</div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Arm field agents with PWA applications capable of working without internet connection, featuring offline sync, GPS validation, and media support.
                </p>
            </div>
            <!-- Card 3 -->
            <div class="border border-gray-200 p-8 rounded-lg shadow-sm hover:shadow-md transition duration-150 bg-white">
                <div class="text-gray-900 font-bold text-lg mb-2">Enterprise Analytics</div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Real-time Chart.js-powered reports, custom data filters, and AI-generated executive summaries ready for PowerPoint and Board presentation.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('Corporate::layout')

@section('title', 'Platform Pricing')

@section('content')
<section class="py-16 sm:py-24 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
            Simple, Transparent Corporate Pricing
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
            Tailored pricing configurations for local research agencies and multinational enterprise operations.
        </p>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Standard</h3>
                    <p class="text-sm text-gray-500 mt-1">For single research projects</p>
                    <div class="mt-4">
                        <span class="text-4xl font-extrabold text-gray-900">$290</span>
                        <span class="text-gray-500 text-sm">/month</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-gray-600">
                        <li>• Up to 5,000 respondents</li>
                        <li>• 5 Field Agent accounts</li>
                        <li>• Standard Survey Engine</li>
                        <li>• Basic reporting outputs</li>
                    </ul>
                </div>
                <a href="{{ route('auth.register') }}" class="mt-8 block text-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Get Started
                </a>
            </div>

            <!-- Card 2 (Featured) -->
            <div class="bg-white border-2 border-gray-900 p-8 rounded-lg shadow-md flex flex-col justify-between relative">
                <span class="absolute top-0 right-6 -translate-y-1/2 bg-gray-900 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">
                    Recommended
                </span>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Professional</h3>
                    <p class="text-sm text-gray-500 mt-1">For active research agencies</p>
                    <div class="mt-4">
                        <span class="text-4xl font-extrabold text-gray-900">$890</span>
                        <span class="text-gray-500 text-sm">/month</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-gray-600">
                        <li>• Up to 50,000 respondents</li>
                        <li>• 50 Field Agent accounts</li>
                        <li>• Advanced Logic & PWA Offline</li>
                        <li>• Custom Excel/PDF Exports</li>
                    </ul>
                </div>
                <a href="{{ route('auth.register') }}" class="mt-8 block text-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Start Agency Free Trial
                </a>
            </div>

            <!-- Card 3 -->
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Enterprise</h3>
                    <p class="text-sm text-gray-500 mt-1">For multinational companies</p>
                    <div class="mt-4">
                        <span class="text-4xl font-extrabold text-gray-900">Custom</span>
                    </div>
                    <ul class="mt-6 space-y-3 text-sm text-gray-600">
                        <li>• Unlimited respondents</li>
                        <li>• Unlimited Field Agents</li>
                        <li>• Complete White-Label Option</li>
                        <li>• Dedicated Account Manager & AI SLA</li>
                    </ul>
                </div>
                <a href="{{ route('corporate.contact') }}" class="mt-8 block text-center rounded-md border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Contact Sales
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Specialized Marketing & Research Services -->
<section class="py-20 sm:py-28 bg-gray-50 border-t border-gray-150">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16 space-y-2 text-center sm:text-left">
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
@endsection

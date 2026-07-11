@extends('Corporate::layout')

@section('title', 'Platform Features')

@section('content')
<section class="py-16 sm:py-24 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                Built for High-Scale Research Operations
            </h1>
            <p class="mt-4 text-lg text-gray-600">
                Explore the modular architecture supporting the collection and processing of research data at scale.
            </p>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">1. Survey Builder & Engine</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Create complex questionnaire paths using skip logic, validation rules, multi-language translation, and support for rich media uploads.
                </p>
            </div>
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">2. Offline PWA Client</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Designed for remote African rural setups. Allows survey completion offline, storing entries securely in IndexedDB and syncing automatically when back online.
                </p>
            </div>
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">3. Spatie Role-Based Security</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Robust role configuration ensuring that Super Admins, Project Managers, Clients, and Field Agents access only their authorized views and records.
                </p>
            </div>
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">4. AI-Assisted Analytics</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Leverage advanced data analysis tools to automatically draft executive summaries, highlight outliers, and generate distribution reports in seconds.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

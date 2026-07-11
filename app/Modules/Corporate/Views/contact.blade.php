@extends('Corporate::layout')

@section('title', 'Contact Us')

@section('content')
<section class="py-16 sm:py-24 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
            Contact Our Enterprise Sales & Support
        </h1>
        <p class="mt-4 text-lg text-gray-600">
            Have a project or partnership in mind? Get in touch with our team.
        </p>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Form -->
            <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="#" method="POST" class="space-y-6" onsubmit="event.preventDefault(); alert('Thank you for contacting us. We will get back to you shortly.');">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="name" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Work Email</label>
                        <input type="email" id="email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    </div>
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" id="company" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message / Inquiry Details</label>
                        <textarea id="message" rows="4" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"></textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Submit Inquiry
                    </button>
                </form>
            </div>

            <!-- Side Info -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400">Headquarters</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Metrica Polls Ltd.<br>
                        Capital Business Center, Suite 402<br>
                        Nairobi, Kenya
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400">Direct Contact</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Sales: <a href="mailto:sales@metricapolls.com" class="text-gray-900 underline">sales@metricapolls.com</a><br>
                        Support: <a href="mailto:support@metricapolls.com" class="text-gray-900 underline">support@metricapolls.com</a>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-400">Response SLA</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Our corporate support agents respond to inquiries within 4 business hours.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

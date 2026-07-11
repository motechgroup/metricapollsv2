@extends('Corporate::layout')

@section('title', 'About Us')

@section('content')
<section class="py-16 sm:py-24 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                Our Mission & Vision
            </h1>
            <p class="mt-4 text-lg text-gray-600">
                Metrica Polls is dedicated to bridging the information gap across Africa by providing enterprise-grade, reliable, and accessible research technologies.
            </p>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="prose max-w-3xl text-gray-700 space-y-6">
            <p>
                Founded in 2026, Metrica Polls was established to modernize how consumer insight, social research, and health metrics are collected in sub-Saharan Africa. Conventional tools fail in low-bandwidth, high-latency environments. We created an offline-first platform capable of capturing structured inputs in remote settlements, and seamlessly synchronizing with our secure cloud database.
            </p>
            <p>
                Our compliance framework ensures that we respect and enforce strict data protections including GDPR and local data protection regulations, keeping panelist information anonymous, and encrypting all sensitive responses at rest.
            </p>
            <p>
                Whether you are a global brand looking for consumer behavior metrics, a non-profit studying health access, or a government office measuring public opinion, Metrica Polls is designed to scale with your ambitions.
            </p>
        </div>
    </div>
</section>
@endsection

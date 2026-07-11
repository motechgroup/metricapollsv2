@extends('Corporate::layout')

@section('title', 'Terms of Service')

@section('content')
<div class="py-16 sm:py-24 bg-white border-b border-gray-100">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold tracking-tight text-brand-navy sm:text-5xl mb-6">Terms of Service</h1>
        <p class="text-sm text-gray-500 mb-8 border-b border-gray-100 pb-4">Last updated: March 2026</p>
        
        <div class="prose prose-slate max-w-none text-gray-700 leading-relaxed space-y-6">
            {!! $content !!}
        </div>
    </div>
</div>
@endsection

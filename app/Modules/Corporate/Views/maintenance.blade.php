<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Maintenance - Metrica Polls</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center font-sans antialiased text-gray-950 bg-gray-50">
    <div class="max-w-md w-full mx-auto px-6 text-center space-y-8 animate-fade-in">
        <!-- Logo -->
        <div class="flex justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="Metrica Polls Logo" class="h-10 w-auto" onerror="this.onerror=null; this.src='/favicon.png';">
        </div>

        <!-- Maintenance Card -->
        <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm space-y-6">
            <div class="space-y-2">
                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20 uppercase tracking-wide">
                    System Operations
                </span>
                <h1 class="text-xl font-bold tracking-tight text-gray-900">Scheduled System Upgrades</h1>
                <p class="text-sm text-gray-500 leading-relaxed">We are currently executing core database optimizations and security compliance audits to improve platform performance for our government, corporate, and NGO partners.</p>
            </div>

            <div class="border-t border-gray-100 pt-4 flex flex-col items-center gap-2">
                <span class="text-xxs text-gray-400 font-semibold font-mono tracking-wider">ISO 27001 AUDITING IN PROGRESS</span>
                <span class="text-xxs text-gray-400 font-semibold font-mono tracking-wider">GDPR COMPLIANCE SECURED</span>
            </div>

            <!-- Backdoor Login for Admins -->
            <div class="border-t border-gray-100 pt-4">
                <a href="{{ route('login') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 underline">
                    Authorized Staff Sign-In &rarr;
                </a>
            </div>
        </div>

        <div class="text-xs text-gray-400">
            &copy; {{ date('Y') }} Metrica Polls. All rights reserved. Registered Enterprise Research Entity.
        </div>
    </div>
</body>
</html>

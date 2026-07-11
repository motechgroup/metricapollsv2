@php
    $activeMethods = [];
    if ($login_google_enabled) $activeMethods[] = 'google';
    if ($login_email_enabled) $activeMethods[] = 'email';
    if ($login_sms_enabled) $activeMethods[] = 'sms';
    
    $defaultTab = count($activeMethods) > 0 ? $activeMethods[0] : 'google';
@endphp

<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="w-full max-w-md bg-white border border-gray-200 p-8 rounded-lg shadow-sm space-y-8 animate-fade-in" 
         x-data="{ 
            showPicker: false,
            activeTab: '{{ $defaultTab }}'
         }">
        
        @if($isAdminLogin)
            <!-- Staff Sign In Form (Always Email/Password) -->
            <div class="text-center">
                <h2 class="text-2xl font-bold tracking-tight text-brand-navy font-sans">Metrica Staff Login</h2>
                <p class="mt-2 text-xs text-gray-500">Access the administrative and research manager workspace.</p>
            </div>

            @if (session()->has('error'))
                <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-4">
                <div>
                    <label for="email" class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Email Address</label>
                    <input wire:model="email" type="email" id="email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                    @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Password</label>
                        <a href="{{ route('auth.forgot-password') }}" class="text-xxs text-gray-650 hover:underline">Forgot?</a>
                    </div>
                    <input wire:model="password" type="password" id="password" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                    @error('password') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input wire:model="remember" type="checkbox" id="remember" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 bg-white">
                    <label for="remember" class="ml-2 block text-xs text-gray-600">Remember this device</label>
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-brand-navy px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue transition">
                    Sign In as Staff
                </button>
            </form>

            <div class="mt-6 text-center border-t border-gray-150 pt-6">
                <p class="text-xs text-gray-500">
                    Looking for panelist login? 
                    <a href="{{ route('login') }}" class="font-bold text-brand-navy hover:underline">Continue with Google</a>
                </p>
            </div>
        @else
            <!-- Regular Panelist Sign In -->
            <div class="text-center">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 font-sans">Sign In to Metrica Polls</h2>
                <p class="mt-2 text-xs text-gray-500">Access your panel wallet, learning academy, and paid survey dashboard.</p>
            </div>

            @if (session()->has('error'))
                <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md text-center">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tabs Navigation (Only if multiple methods are allowed) -->
            @if(count($activeMethods) > 1)
                <div class="flex border-b border-gray-200">
                    @if($login_google_enabled)
                        <button type="button" @click="activeTab = 'google'" :class="{ 'border-gray-900 text-gray-900 font-semibold': activeTab === 'google', 'border-transparent text-gray-500': activeTab !== 'google' }" class="flex-1 pb-3 text-xs border-b-2 text-center transition">
                            Google Login
                        </button>
                    @endif
                    @if($login_email_enabled)
                        <button type="button" @click="activeTab = 'email'" :class="{ 'border-gray-900 text-gray-900 font-semibold': activeTab === 'email', 'border-transparent text-gray-500': activeTab !== 'email' }" class="flex-1 pb-3 text-xs border-b-2 text-center transition">
                            Email &amp; Password
                        </button>
                    @endif
                    @if($login_sms_enabled)
                        <button type="button" @click="activeTab = 'sms'" :class="{ 'border-gray-900 text-gray-900 font-semibold': activeTab === 'sms', 'border-transparent text-gray-500': activeTab !== 'sms' }" class="flex-1 pb-3 text-xs border-b-2 text-center transition">
                            SMS OTP
                        </button>
                    @endif
                </div>
            @endif

            <!-- Tab Content: Google SSO -->
            @if($login_google_enabled)
                <div x-show="activeTab === 'google'" class="space-y-4">
                    <!-- Google Sign In Button -->
                    <button type="button" @click="showPicker = !showPicker" class="w-full inline-flex items-center justify-center gap-3 rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                            <g transform="matrix(1, 0, 0, 1, 0, 0)">
                                <path d="M21.35,11.1H12v2.7h5.38C16.88,15.22,14.73,16.5,12,16.5c-3.03,0-5.61-2.05-6.52-4.82c-0.23-0.69-0.36-1.42-0.36-2.18s0.13-1.49,0.36-2.18C6.39,4.55,8.97,2.5,12,2.5c1.65,0,3.13,0.59,4.3,1.57l2.02-2.02C16.78,0.77,14.53,0,12,0C7.24,0,3.2,2.72,1.3,6.72C0.89,7.6,0.58,8.54,0.37,9.52c-0.12,0.57-0.18,1.15-0.18,1.75s0.06,1.18,0.18,1.75c0.21,0.98,0.52,1.92,0.93,2.8c1.9,4,5.94,6.72,10.7,6.72c3.24,0,5.97-1.07,7.96-2.91c2.14-1.97,3.39-4.87,3.39-8.1C23.37,12.16,23.3,11.62,21.35,11.1z" fill="#4285F4"/>
                            </g>
                        </svg>
                        <span>Continue with Google</span>
                    </button>

                    <!-- Warning Notice -->
                    <div class="rounded-md bg-blue-50 p-3 border border-blue-150">
                        <p class="text-xxs text-blue-700 leading-relaxed font-semibold">
                            💡 SECURITY NOTICE: Panelist registrations and payouts are restricted to authenticated Google Accounts only under Google mode to prevent fraud.
                        </p>
                    </div>

                    <!-- Google Account Picker (Mock OAuth popup) -->
                    <div x-show="showPicker" x-transition class="border border-gray-200 rounded-lg p-4 bg-gray-50 space-y-3" style="display: none;">
                        <h3 class="text-xxs font-bold uppercase tracking-wider text-gray-400">Choose a Google Account</h3>
                        <div class="space-y-2">
                            <button wire:click="loginWithGoogle('alice.awino@gmail.com', 'Alice Awino')" class="w-full text-left bg-white border border-gray-150 p-2.5 rounded-md hover:bg-gray-100 transition flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-gray-900 block">Alice Awino</span>
                                    <span class="text-gray-500">alice.awino@gmail.com</span>
                                </div>
                                <span class="text-xxs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded uppercase">Active Panelist</span>
                            </button>
                            <button wire:click="loginWithGoogle('ben.otieno@gmail.com', 'Ben Otieno')" class="w-full text-left bg-white border border-gray-150 p-2.5 rounded-md hover:bg-gray-100 transition flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-gray-900 block">Ben Otieno</span>
                                    <span class="text-gray-500">ben.otieno@gmail.com</span>
                                </div>
                                <span class="text-xxs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded uppercase">New Panelist</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tab Content: Email & Password -->
            @if($login_email_enabled)
                <div x-show="activeTab === 'email'" class="space-y-4" style="display: none;">
                    <form wire:submit.prevent="login" class="space-y-4">
                        <div>
                            <label for="email_panel" class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Email Address</label>
                            <input wire:model="email" type="email" id="email_panel" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                            @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="password_panel" class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Password</label>
                                <a href="{{ route('auth.forgot-password') }}" class="text-xxs text-gray-650 hover:underline">Forgot?</a>
                            </div>
                            <input wire:model="password" type="password" id="password_panel" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                            @error('password') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center">
                            <input wire:model="remember" type="checkbox" id="remember_panel" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 bg-white">
                            <label for="remember_panel" class="ml-2 block text-xs text-gray-600">Remember this device</label>
                        </div>

                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Send OTP Code
                        </button>
                    </form>
                </div>
            @endif

            <!-- Tab Content: SMS OTP -->
            @if($login_sms_enabled)
                <div x-show="activeTab === 'sms'" class="space-y-4" style="display: none;">
                    <form wire:submit.prevent="loginWithPhone" class="space-y-4">
                        <div>
                            <label for="phone" class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Registered Phone Number</label>
                            <input wire:model="phone" type="text" id="phone" placeholder="e.g. +254700000000" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                            @error('phone') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Send SMS OTP Code
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-6 text-center border-t border-gray-150 pt-6">
                <p class="text-xs text-gray-500">
                    Are you a staff member? 
                    <a href="{{ route('admin.login') }}" class="font-bold text-brand-navy hover:underline">Staff Sign In</a>
                </p>
            </div>
        @endif

    </div>
</div>

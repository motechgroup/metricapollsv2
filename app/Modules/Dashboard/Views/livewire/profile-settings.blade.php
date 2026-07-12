@section('page_title', 'My Profile')

<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Profile Settings</h1>
        <p class="text-sm text-gray-500">View and update your personal account details, phone number, and security password.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <form wire:submit.prevent="save" class="divide-y divide-gray-250">
            <!-- Account Information -->
            <div class="p-6 space-y-6">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Account Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input wire:model="name" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                        @error('name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input wire:model="email" type="email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                        @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number (Optional)</label>
                        <input wire:model="phone" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white" placeholder="e.g. +254700000000">
                        @error('phone') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Password Updates -->
            <div class="p-6 space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Change Password</h2>
                    <p class="text-xs text-gray-400 mt-1">Leave these fields blank if you do not wish to update your password.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">New Password</label>
                        <input wire:model="password" type="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                        @error('password') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input wire:model="password_confirmation" type="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-6 bg-gray-50 flex justify-end gap-4">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Code Overlay/Modal -->
    @if($showCodeConfirmation)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white border border-gray-200 rounded-lg shadow-xl max-w-md w-full overflow-hidden">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-50 text-blue-600 mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                </div>
                <div class="text-center space-y-1">
                    <h3 class="text-base font-bold text-gray-900">Security Verification Required</h3>
                    <p class="text-xs text-gray-500">To confirm updates to your username or password, enter the 6-digit verification code sent to your email.</p>
                </div>

                @if (session()->has('info'))
                    <div class="p-3 bg-blue-50 border border-blue-200 text-blue-700 text-xxs font-semibold rounded-md text-center">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="space-y-2">
                    <input wire:model="enteredCode" type="text" maxlength="6" placeholder="Enter 6-digit code" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-center text-lg font-bold tracking-widest focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                    @error('enteredCode') <span class="text-xs text-red-600 mt-1 block text-center">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end">
                <button type="button" wire:click="cancelConfirmation" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" wire:click="confirmSave" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Verify &amp; Save
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

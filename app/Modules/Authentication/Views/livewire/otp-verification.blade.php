<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="w-full max-w-md bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Enter OTP Verification Code</h2>
            <p class="mt-2 text-sm text-gray-600">We've generated a 6-digit OTP code to verify your device/email. Please enter it below.</p>
        </div>

        @if (session()->has('info'))
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-md font-mono">
                {{ session('info') }}
            </div>
        @endif

        <form wire:submit.prevent="verify" class="space-y-6">
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700 text-center mb-2">6-Digit Code</label>
                <input wire:model="otp" type="text" id="otp" maxlength="6" placeholder="000000" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-3 text-center text-xl font-mono tracking-widest focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                @error('otp') <span class="text-xs text-red-600 mt-1 block text-center">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                Verify & Sign In
            </button>
        </form>

        <div class="mt-6 text-center border-t border-gray-100 pt-6 flex flex-col gap-2">
            <button wire:click="resend" type="button" class="text-sm font-medium text-gray-950 hover:underline">
                Resend OTP Verification Code
            </button>
            <a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-gray-900">
                Back to Login
            </a>
        </div>
    </div>
</div>

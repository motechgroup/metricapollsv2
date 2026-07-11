<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="w-full max-w-md bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Reset Your Password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your registered email address and we'll send you a password reset link.</p>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md break-all">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="sendResetLink" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input wire:model="email" type="email" id="email" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center border-t border-gray-100 pt-6">
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-900 hover:underline">
                Back to Sign In
            </a>
        </div>
    </div>
</div>

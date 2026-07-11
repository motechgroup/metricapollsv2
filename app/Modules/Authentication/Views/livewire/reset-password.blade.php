<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="w-full max-w-md bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Define New Password</h2>
            <p class="mt-2 text-sm text-gray-600">Please enter your new password below.</p>
        </div>

        <form wire:submit.prevent="resetPassword" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="text" disabled value="{{ $email }}" class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input wire:model="password" type="password" id="password" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                @error('password') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input wire:model="password_confirmation" type="password" id="password_confirmation" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                @error('password_confirmation') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                Reset Password
            </button>
        </form>
    </div>
</div>

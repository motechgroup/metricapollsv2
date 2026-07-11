@section('page_title', 'Profile & Verification')

<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Profile & Verification</h1>
            <p class="text-sm text-gray-500">Provide demographic variables to verify your account and qualify for targeted consumer surveys.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Account Status:</span>
            @if($is_verified)
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Verified Respondent</span>
            @else
                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Unverified</span>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Balance Card -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm h-fit">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Available Earnings</h2>
            <div class="mt-2 flex items-baseline gap-x-2">
                <span class="text-4xl font-bold tracking-tight text-gray-900 font-mono">{{ number_format($points_balance) }}</span>
                <span class="text-sm font-semibold text-gray-500">Points</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Monetary valuation: <span class="font-semibold text-gray-900 font-mono">${{ number_format($points_balance / 100, 2) }} USD</span></p>
            
            <div class="border-t border-gray-100 mt-6 pt-6">
                <a href="{{ route('panelist.wallet') }}" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Go to Wallet / Cash Out
                </a>
            </div>
        </div>

        <!-- Profiling Form -->
        <div class="lg:col-span-2 bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Demographic Profile Form</h2>
            <form wire:submit.prevent="saveProfile" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                        <select wire:model="gender" id="gender" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('gender') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input wire:model="date_of_birth" type="date" id="date_of_birth" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        @error('date_of_birth') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700">Highest Education Attained</label>
                        <select wire:model="education_level" id="education_level" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            <option value="">Select Education</option>
                            <option value="High School">High School</option>
                            <option value="Diploma">Diploma / Associate</option>
                            <option value="Bachelors Degree">Bachelor's Degree</option>
                            <option value="Masters or Higher">Master's / Ph.D.</option>
                        </select>
                        @error('education_level') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="income_bracket" class="block text-sm font-medium text-gray-700">Monthly Income Bracket</label>
                        <select wire:model="income_bracket" id="income_bracket" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            <option value="">Select Income Bracket</option>
                            <option value="Under $500">Under $500</option>
                            <option value="$500 - $1,500">$500 - $1,500</option>
                            <option value="$1,500 - $3,500">$1,500 - $3,500</option>
                            <option value="Over $3,500">Over $3,500</option>
                        </select>
                        @error('income_bracket') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="location_region" class="block text-sm font-medium text-gray-700">Primary Resident Location / Region</label>
                        <input wire:model="location_region" type="text" id="location_region" placeholder="e.g. Nairobi Central, Kenya" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        @error('location_region') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 text-right">
                    <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save Demographic Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

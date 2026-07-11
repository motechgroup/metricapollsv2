@section('page_title', 'Panelist Dashboard')

<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard & Verification</h1>
            <p class="text-sm text-gray-500">Provide demographic variables and verify your phone number to qualify for high-paying consumer surveys.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Demographic Status:</span>
            @if($is_verified)
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Profile Completed</span>
            @else
                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Incomplete Profile</span>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Grid Panel -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <!-- wallet points balance -->
        <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
            <span class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Wallet Balance</span>
            <span class="block text-xl font-bold text-gray-900 font-mono mt-1">{{ number_format($points_balance) }} pts</span>
            <span class="block text-xxs text-gray-500 mt-1">Valued at ${{ number_format($points_balance / 100, 2) }} USD</span>
        </div>

        <!-- experience points -->
        <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
            <span class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Experience (EXP)</span>
            <span class="block text-xl font-bold text-gray-900 font-mono mt-1">{{ number_format($experience_points) }} XP</span>
            <div class="w-full bg-gray-150 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, ($experience_points / 300) * 100) }}%"></div>
            </div>
        </div>

        <!-- badge level -->
        <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
            <span class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Badge Rating</span>
            @if($badge_level === 'Gold')
                <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20 mt-2">
                    🏆 Gold Elite
                </span>
            @elseif($badge_level === 'Silver')
                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-800 ring-1 ring-inset ring-slate-600/20 mt-2">
                    🥈 Silver Analyst
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-md bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-700 ring-1 ring-inset ring-orange-600/20 mt-2">
                    🥉 Bronze Panelist
                </span>
            @endif
        </div>

        <!-- completed surveys -->
        <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
            <span class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Completed Surveys</span>
            <span class="block text-xl font-bold text-gray-900 font-mono mt-1">{{ $completedSurveysCount }} studies</span>
            <span class="block text-xxs text-gray-500 mt-1">Verified submission logs</span>
        </div>

        <!-- available surveys -->
        <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm col-span-2 md:col-span-1">
            <span class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Available Audits</span>
            <span class="block text-xl font-bold text-blue-600 font-mono mt-1">{{ $availableSurveysCount }} campaigns</span>
            <span class="block text-xxs text-gray-500 mt-1">Matching your badge level</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side Cards: Earnings & Phone Verification -->
        <div class="space-y-6">
            
            <!-- Earnings Wallet Action -->
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Earnings Wallet</h2>
                <div class="mt-2 flex items-baseline gap-x-2">
                    <span class="text-3xl font-bold tracking-tight text-gray-900 font-mono">{{ number_format($points_balance) }}</span>
                    <span class="text-xs font-semibold text-gray-500">Points</span>
                </div>
                
                <div class="border-t border-gray-150 mt-4 pt-4">
                    <a href="{{ route('panelist.wallet') }}" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Go to Wallet / Cash Out
                    </a>
                </div>
            </div>

            <!-- Phone Number Verification Card -->
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-4">
                <div class="flex justify-between items-start">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Phone Verification</h2>
                    @if($is_phone_verified)
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xxs font-bold text-green-700 ring-1 ring-inset ring-green-600/20">Verified</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xxs font-bold text-red-700 ring-1 ring-inset ring-red-600/20">Unverified</span>
                    @endif
                </div>
                
                <p class="text-xxs text-gray-500 leading-relaxed">
                    Verify your phone number via SMS OTP to confirm your regional demographics and secure your payouts.
                </p>

                @if(session()->has('phone_status'))
                    <div class="p-2.5 bg-blue-50 border border-blue-150 text-blue-700 text-xxs rounded-md">
                        {{ session('phone_status') }}
                    </div>
                @endif

                <div class="space-y-3">
                    <div>
                        <label for="phone" class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Mobile Phone Number</label>
                        <div class="mt-1 flex gap-2">
                            <input wire:model="phone" type="text" id="phone" placeholder="e.g. +254700000000" class="block w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs bg-white focus:border-gray-900 focus:outline-none" {{ $is_phone_verified ? 'disabled' : '' }}>
                            @if(!$is_phone_verified)
                                <button type="button" wire:click="sendPhoneOtp" class="inline-flex justify-center items-center rounded-md bg-gray-900 px-3 py-1.5 text-xxs font-semibold text-white shadow-sm hover:bg-gray-800 transition whitespace-nowrap">
                                    {{ $otpSent ? 'Resend OTP' : 'Send Code' }}
                                </button>
                            @endif
                        </div>
                        @error('phone') <span class="text-xxs text-red-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    @if($otpSent && !$is_phone_verified)
                        <div class="border-t border-gray-150 pt-3 space-y-2">
                            <label for="otpCodeInput" class="block text-xxs font-bold uppercase tracking-wider text-gray-400">Enter 6-Digit OTP</label>
                            <div class="flex gap-2">
                                <input wire:model="otpCodeInput" type="text" id="otpCodeInput" placeholder="e.g. 123456" class="block w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs bg-white focus:border-gray-900 focus:outline-none font-mono tracking-widest text-center">
                                <button type="button" wire:click="verifyPhoneOtp" class="inline-flex justify-center items-center rounded-md bg-green-700 px-3 py-1.5 text-xxs font-semibold text-white shadow-sm hover:bg-green-800 transition whitespace-nowrap">
                                    Verify Code
                                </button>
                            </div>
                            @error('otpCodeInput') <span class="text-xxs text-red-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Demographic Form -->
        <div class="lg:col-span-2 bg-white border border-gray-200 p-6 rounded-lg shadow-sm h-fit">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-6">Demographic Profile Data</h2>
            <form wire:submit.prevent="saveProfile" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-gray-400">Gender</label>
                        <select wire:model="gender" id="gender" required class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('gender') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-xs font-bold uppercase tracking-wider text-gray-400">Date of Birth</label>
                        <input wire:model="date_of_birth" type="date" id="date_of_birth" required class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                        @error('date_of_birth') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="education_level" class="block text-xs font-bold uppercase tracking-wider text-gray-400">Highest Education Attained</label>
                        <select wire:model="education_level" id="education_level" required class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            <option value="">Select Education</option>
                            <option value="High School">High School</option>
                            <option value="Diploma">Diploma / Associate</option>
                            <option value="Bachelors Degree">Bachelor's Degree</option>
                            <option value="Masters or Higher">Master's / Ph.D.</option>
                        </select>
                        @error('education_level') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="income_bracket" class="block text-xs font-bold uppercase tracking-wider text-gray-400">Monthly Income Bracket</label>
                        <select wire:model="income_bracket" id="income_bracket" required class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                            <option value="">Select Income Bracket</option>
                            <option value="Under $500">Under $500</option>
                            <option value="$500 - $1,500">$500 - $1,500</option>
                            <option value="$1,500 - $3,500">$1,500 - $3,500</option>
                            <option value="Over $3,500">Over $3,500</option>
                        </select>
                        @error('income_bracket') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="location_region" class="block text-xs font-bold uppercase tracking-wider text-gray-400">Primary Resident Location / Region</label>
                        <input wire:model="location_region" type="text" id="location_region" placeholder="e.g. Nairobi Central, Kenya" required class="mt-1.5 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white focus:border-gray-900 focus:outline-none">
                        @error('location_region') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-gray-150 pt-6 text-right">
                    <button type="submit" class="rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Save Demographic Profile
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

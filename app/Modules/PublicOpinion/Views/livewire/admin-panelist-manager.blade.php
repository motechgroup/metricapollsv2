@section('page_title', 'Manage Panelists')

<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manage Panelists</h1>
        <p class="text-sm text-gray-500">Audit panelists, adjust wallet balances, upgrade badge ratings, and override demographic or phone verification status.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Inline Editor Panel -->
    @if($editingProfileId)
        <div class="bg-gray-50 border border-gray-200 p-6 rounded-lg shadow-sm space-y-6 max-w-2xl">
            <div class="flex justify-between items-center border-b border-gray-250 pb-3">
                <h2 class="text-sm font-bold text-gray-900">Editing Panelist: <span class="text-gray-600 font-normal">{{ $editingProfile->user->name }}</span></h2>
                <button type="button" wire:click="closeEdit" class="text-xs font-semibold text-gray-500 hover:text-gray-900">Cancel</button>
            </div>

            <!-- Form 1: Upgrade/Change Badge -->
            <form wire:submit.prevent="saveBadge" class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Upgrade Qualification Badge</h3>
                <div class="flex items-center gap-4">
                    <div class="flex-grow">
                        <label for="badgeLevel" class="sr-only">Badge Level</label>
                        <select wire:model="badgeLevel" id="badgeLevel" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            <option value="Bronze">Bronze Panelist</option>
                            <option value="Silver">Silver Analyst</option>
                            <option value="Gold">Gold Elite</option>
                        </select>
                    </div>
                    <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-gray-800 transition">
                        Update Badge
                    </button>
                </div>
            </form>

            <hr class="border-gray-200" />

            <!-- Form 2: Adjust Points -->
            <form wire:submit.prevent="awardPoints" class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Award or Deduct Wallet Points</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pointsToAward" class="block text-xs font-medium text-gray-700">Points Value (Use negative to deduct)</label>
                        <input wire:model="pointsToAward" type="number" id="pointsToAward" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
                        @error('pointsToAward') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pointsAwardReason" class="block text-xs font-medium text-gray-700">Adjustment Reason</label>
                        <input wire:model="pointsAwardReason" type="text" id="pointsAwardReason" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-900">
                        @error('pointsAwardReason') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="rounded-md bg-green-650 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-green-700 transition">
                        Confirm Wallet Adjustment
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Panelists Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Panelist Profile</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Wallet Details</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Rating Badge</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Profile Verified</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Phone Verified</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($profiles as $profile)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $profile->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $profile->user->email ?? 'N/A' }}</div>
                                @if($profile->user->phone)
                                    <div class="text-xxs text-gray-400 mt-0.5">Phone: {{ $profile->user->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ number_format($profile->points_balance) }} pts (${{ number_format($profile->points_balance / 100, 2) }} USD)</div>
                                <div class="text-xxs text-gray-450 font-mono">{{ $profile->experience_points }} EXP points</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($profile->badge_level === 'Gold')
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20">🏆 Gold Elite</span>
                                @elseif($profile->badge_level === 'Silver')
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-800 ring-1 ring-inset ring-slate-600/20">🥈 Silver Analyst</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-0.5 text-xs font-bold text-orange-700 ring-1 ring-inset ring-orange-600/20">🥉 Bronze Panelist</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" wire:click="toggleProfileVerification({{ $profile->id }})" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $profile->is_verified ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $profile->is_verified ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    {{ $profile->is_verified ? 'Verified' : 'Unverified' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" wire:click="togglePhoneVerification({{ $profile->user_id }})" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ ($profile->user->phone_verified ?? false) ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ ($profile->user->phone_verified ?? false) ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    {{ ($profile->user->phone_verified ?? false) ? 'Verified' : 'Unverified' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" wire:click="editPanelist({{ $profile->id }})" class="rounded-md bg-gray-900 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-gray-800 transition">
                                    Manage
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-lg font-bold">No Panelist Profiles Found</div>
                                <p class="text-sm mt-1">Users will show up here once they complete their profiling on the dashboard.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

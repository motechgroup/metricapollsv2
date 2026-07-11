@section('page_title', 'Wallet & Earnings')

<div class="space-y-8 animate-fade-in">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 font-sans">Wallet &amp; Payouts</h1>
        <p class="text-sm text-gray-500">Track your completed survey rewards, view transaction histories, and redeem points to your local wallet account.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Payout Redemption Form -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm h-fit">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Redeem Points</h2>
            <form wire:submit.prevent="redeem" class="space-y-6">
                <div>
                    <label for="pointsToRedeem" class="block text-sm font-medium text-gray-700">Points to Redeem</label>
                    <input wire:model="pointsToRedeem" type="number" step="100" min="100" id="pointsToRedeem" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    <p class="text-xxs text-gray-400 mt-1.5">100 Points = $1.00 USD (equivalent to {{ $currencySymbol }} {{ number_format($exchangeRate, 2) }}). Minimum redemption is 100 points.</p>
                    @error('pointsToRedeem') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="payoutMethod" class="block text-sm font-medium text-gray-700">Payout Channel</label>
                    <select wire:model="payoutMethod" id="payoutMethod" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                        @foreach($payoutMethods as $val => $lbl)
                            <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('payoutMethod') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phoneNumber" class="block text-sm font-medium text-gray-700">Recipient Account / Phone</label>
                    <input wire:model="phoneNumber" type="text" id="phoneNumber" placeholder="e.g. +254700000000 or Account No." required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('phoneNumber') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Request Payout
                </button>
            </form>
        </div>

        <!-- Wallet History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Points Display Card -->
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm flex justify-between items-center">
                <div>
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Current points balance</h2>
                    <p class="text-3xl font-bold text-gray-900 font-mono mt-1">{{ number_format($profile->points_balance ?? 0) }} pts</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Local Valuation ({{ $currencyCode }})</h2>
                    <p class="text-3xl font-bold text-gray-900 font-mono mt-1">{{ $currencySymbol }} {{ number_format(($profile->points_balance ?? 0) * ($exchangeRate / 100), 2) }}</p>
                </div>
            </div>

            <!-- Transactions Log -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900">Transaction History Log</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Transaction</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Ref Code</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Points</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Valuation</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($transactions as $txn)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 leading-snug">{{ $txn->description }}</span>
                                        <span class="text-xxs text-gray-400 mt-0.5">{{ $txn->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-xs font-semibold font-mono text-gray-650">
                                    {{ $txn->reference ?? 'SYSTEM' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-bold font-mono {{ $txn->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $txn->points > 0 ? '+' : '' }}{{ number_format($txn->points) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium font-mono text-gray-900">
                                    {{ $txn->amount > 0 ? '+' : '' }}${{ number_format(abs($txn->amount), 2) }} USD
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-xs">
                                    @if($txn->status === 'pending')
                                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-0.5 font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">Pending Review</span>
                                    @elseif($txn->status === 'completed')
                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-0.5 font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Success</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-0.5 font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">Failed / Refunded</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No wallet transactions recorded yet. Complete surveys to start earning rewards.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

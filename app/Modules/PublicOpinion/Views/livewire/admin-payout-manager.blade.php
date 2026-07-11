@section('page_title', 'Manage Payouts')

<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manage Panelist Payouts</h1>
        <p class="text-sm text-gray-500">Review pending withdrawal requests, process mobile money transfers, or reject requests with automatic wallet refunds.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-md shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Payouts Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Panelist</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Points &amp; USD Amount</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Redemption Details</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Requested At</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $payout->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $payout->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono">
                                <div class="text-sm font-bold text-gray-900">{{ abs($payout->points) }} pts</div>
                                <div class="text-xs text-gray-500">${{ number_format(abs($payout->amount), 2) }} USD</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-950 font-medium">{{ $payout->description }}</div>
                                <div class="text-xxs text-gray-400 font-mono mt-0.5">Ref: {{ $payout->reference }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $payout->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($payout->status === 'pending')
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20">Pending Audit</span>
                                @elseif($payout->status === 'completed')
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Completed</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">Rejected &amp; Refunded</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($payout->status === 'pending')
                                    <button type="button" wire:click="approve({{ $payout->id }})" wire:confirm="Approve this payout? This will release mobile money and send SMS confirmation." class="rounded-md bg-green-650 px-2.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-green-700 transition">
                                        Approve
                                    </button>
                                    <button type="button" wire:click="reject({{ $payout->id }})" wire:confirm="Reject and refund points to user?" class="rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-red-700 transition">
                                        Reject
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">No actions</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-lg font-bold">No Withdrawal Requests Found</div>
                                <p class="text-sm mt-1">Panelists' requests will appear here once they redeem points from their wallets.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

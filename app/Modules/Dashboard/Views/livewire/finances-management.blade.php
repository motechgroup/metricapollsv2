@section('page_title', 'Corporate Finances')

<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 font-sans">Company Finances & Gateway Settings</h1>
            <p class="text-sm text-gray-500">Monitor enterprise client billing, payouts history, and configure payments routes.</p>
        </div>
        @if(!$isInvoiceFormOpen && !$selectedGatewayId)
        <button wire:click="openInvoiceForm" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
            Generate Invoice
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- 1. Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Billed (CRM)</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($totalBilled, 2) }}</h3>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Collected Revenue</p>
            <h3 class="text-2xl font-bold text-green-600 mt-2">${{ number_format($totalPaid, 2) }}</h3>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Unpaid Accounts Receivables</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-2">${{ number_format($totalPending, 2) }}</h3>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Panelist Payouts Disbursed</p>
            <h3 class="text-2xl font-bold text-gray-900 mt-2">KES {{ number_format($totalPayouts, 2) }}</h3>
        </div>
    </div>

    <!-- 2. Gateway Settings Edit -->
    @if($selectedGatewayId)
    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
        <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Configure Payment Gateway Credentials</h2>
        
        <form wire:submit.prevent="saveGatewaySettings" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($gatewayCredentials as $key => $val)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 uppercase tracking-wide text-xxs">{{ str_replace('_', ' ', $key) }}</label>
                        @if($key === 'status')
                            <select wire:model="gatewayCredentials.status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        @elseif($key === 'environment')
                            <select wire:model="gatewayCredentials.environment" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                                <option value="sandbox">Sandbox / Testing</option>
                                <option value="production">Production / Live</option>
                            </select>
                        @else
                            <input wire:model="gatewayCredentials.{{ $key }}" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-800 transition">
                    Save Gateway Credentials
                </button>
                <button type="button" wire:click="closeGatewayForm" class="rounded border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- 3. New Invoice Form -->
    @if($isInvoiceFormOpen)
    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
        <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 font-sans">Generate Project Invoice</h2>
        
        <form wire:submit.prevent="saveInvoice" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Invoice Number</label>
                    <input wire:model="invoice_number" type="text" readonly class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-gray-50 text-gray-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Billed Amount ($ USD)</label>
                    <input wire:model="amount" type="number" step="0.01" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                    @error('amount') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Client Organization</label>
                    <select wire:model="client_organization_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                        <option value="">Select Organization</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                    @error('client_organization_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Assigned Research Campaign</label>
                    <select wire:model="project_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                        <option value="">Select Project</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                    <select wire:model="status" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-800 transition">
                    Create Invoice
                </button>
                <button type="button" wire:click="closeInvoiceForm" class="rounded border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- 4. Gateways & Invoices layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Gateways Column -->
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-2">Active Gateways</h2>
                
                <div class="space-y-4">
                    @foreach($gateways as $gw)
                    <div class="border border-gray-100 p-4 rounded-lg flex items-center justify-between gap-4 bg-gray-50">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-sm text-gray-900">{{ $gw->name }}</h3>
                                @if(($gw->credentials['status'] ?? '') === 'active')
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xxs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">LIVE</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-gray-150 px-1.5 py-0.5 text-xxs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">DISABLED</span>
                                @endif
                            </div>
                            <p class="text-xxs text-gray-500 mt-1">Environment: {{ $gw->credentials['environment'] ?? 'sandbox' }}</p>
                        </div>
                        <button wire:click="selectGatewayForEdit({{ $gw->id }})" class="text-xs font-semibold text-gray-900 underline hover:text-black">
                            Configure
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Invoices List Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Project Invoicing & Billing</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-900">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Invoice</th>
                                <th class="px-6 py-3">Client & Project</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($invoices as $inv)
                            <tr>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-900">{{ $inv->invoice_number }}</span>
                                    <span class="block text-xxs text-gray-400 mt-0.5">{{ $inv->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium block text-gray-950">{{ $inv->clientOrganization->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500 block">{{ $inv->project->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 font-semibold">
                                    ${{ number_format($inv->amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($inv->status === 'paid')
                                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Paid</span>
                                    @elseif($inv->status === 'pending')
                                        <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">Pending</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-650/20">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    @if($inv->status === 'pending')
                                        <button wire:click="updateInvoiceStatus({{ $inv->id }}, 'paid')" class="text-xxs font-bold text-green-600 border border-green-200 px-2 py-1 rounded bg-green-50 hover:bg-green-100">Collect</button>
                                    @endif
                                    <button onclick="confirm('Delete invoice record?') || event.stopImmediatePropagation()" wire:click="deleteInvoice({{ $inv->id }})" class="text-xxs font-bold text-red-600 border border-red-200 px-2 py-1 rounded bg-red-50 hover:bg-red-100">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-xs text-gray-400 italic">No invoice records generated.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

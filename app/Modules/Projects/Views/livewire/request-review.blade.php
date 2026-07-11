@section('page_title', 'Review Research Requests')

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Review Research Requests</h1>
        <p class="text-sm text-gray-500">Audit submitted client briefs, perform feasibility reviews, and approve requests to spawn research projects.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/4">Client Representative / Org</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/3">Survey Brief Details</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Target Quota</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Est. Budget</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/6">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($requests as $req)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-900">{{ $req->user->name }}</span>
                            <span class="text-xs text-gray-500">{{ $req->user->clientOrganization->name ?? 'Individual' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-900">{{ $req->title }}</span>
                            <span class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $req->description }}</span>
                            @if($req->target_audience)
                                <span class="text-xxs text-gray-400 mt-1">Target Cohort: {{ $req->target_audience }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                        {{ number_format($req->sample_size) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 font-mono">
                        {{ $req->estimated_budget ? '$' . number_format($req->estimated_budget, 2) : 'TBD' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        @if($req->status === 'approved')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Approved</span>
                        @elseif($req->status === 'rejected')
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Rejected</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Pending Review</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-3">
                        @if($req->status === 'pending')
                            <button wire:click="approve({{ $req->id }})" class="text-green-600 hover:text-green-900">Approve</button>
                            <button wire:click="reject({{ $req->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                        @else
                            <span class="text-gray-400 text-xs italic">Reviewed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                        No client research briefs submitted yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

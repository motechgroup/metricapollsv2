@section('page_title', 'My Research Requests')

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">My Research Requests</h1>
            <p class="text-sm text-gray-500">Track and manage your submitted survey requests and feasibility audits.</p>
        </div>
        <a href="{{ route('client.requests.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
            Request New Research
        </a>
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
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Research Topic / Title</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Target Sample Size</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Est. Budget</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Submitted Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($requests as $req)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-900">{{ $req->title }}</span>
                            <span class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $req->description }}</span>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                        {{ number_format($req->sample_size) }} respondents
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 font-mono">
                        {{ $req->estimated_budget ? '$' . number_format($req->estimated_budget, 2) : 'TBD' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        @if($req->status === 'approved' || $req->status === 'in_progress')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Approved</span>
                        @elseif($req->status === 'rejected')
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Rejected</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Pending Review</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                        {{ $req->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                        You have not submitted any research requests yet. Click "Request New Research" to send your first brief.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

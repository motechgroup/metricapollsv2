@section('page_title', 'Manage AI Polls & Reports')

<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manage AI Polls & Reports</h1>
            <p class="text-sm text-gray-500">View, audit, toggle visibility, and delete generated marketing research polls and AI intelligence reports.</p>
        </div>
        <a href="{{ route('admin.polls.create') }}" class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
            ➕ Create New Poll
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Polls List Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Poll Topic / Title</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Category</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Region & Sample</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Period</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400">Visibility</th>
                        <th scope="col" class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($polls as $poll)
                        <tr x-data="{ expanded: false }" class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $poll->title }}</div>
                                <div class="text-xxs text-gray-400 font-mono mt-0.5">ID: #{{ $poll->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xxs font-bold text-gray-800">{{ $poll->category }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-semibold">{{ $poll->region }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ number_format($poll->sample_size) }} respondents</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-900">Release: {{ $poll->release_date ? \Carbon\Carbon::parse($poll->release_date)->format('M d, Y') : 'N/A' }}</div>
                                <div class="text-xxs text-gray-400">Research: {{ $poll->research_date ? \Carbon\Carbon::parse($poll->research_date)->format('M d, Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" wire:click="togglePublic({{ $poll->id }})" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $poll->is_public ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $poll->is_public ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    {{ $poll->is_public ? 'Public' : 'Private' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <button type="button" @click="expanded = !expanded" class="text-xs font-semibold text-blue-600 hover:text-blue-900 underline">
                                    <span x-text="expanded ? 'Hide Report' : 'View Report'"></span>
                                </button>
                                <button type="button" wire:click="deletePoll({{ $poll->id }})" wire:confirm="Are you sure you want to delete this poll and its AI report?" class="text-xs font-semibold text-red-600 hover:text-red-950 underline">
                                    Delete
                                </button>
                                
                                <!-- Collapsible Expanded AI Report Content -->
                                <template x-if="expanded">
                                    <tr class="bg-gray-50">
                                        <td colspan="6" class="px-6 py-6 border-t border-gray-100">
                                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-inner space-y-4 max-w-4xl text-left">
                                                <div class="flex justify-between items-center border-b border-gray-150 pb-3">
                                                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Generated AI Report Markdown</h3>
                                                    <button type="button" @click="expanded = false" class="text-xxs text-gray-500 hover:text-gray-900 font-bold uppercase">Close</button>
                                                </div>
                                                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed font-mono whitespace-pre-wrap text-xs bg-gray-50 p-4 rounded border border-gray-150 overflow-x-auto">{{ $poll->ai_report }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-lg font-bold">No Polls Created Yet</div>
                                <p class="text-sm mt-1">Use the "Create New Poll" button above to build your first AI-driven research poll.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

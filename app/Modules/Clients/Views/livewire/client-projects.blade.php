@section('page_title', 'Active Projects')

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Active Research Projects</h1>
        <p class="text-sm text-gray-500">Monitor the live progress of your active research campaigns, target quotas, and timelines.</p>
    </div>

    <div class="space-y-6">
        @forelse($projects as $project)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $project->name }}</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Project Manager: <span class="text-gray-900 font-medium">{{ $project->projectManager->name ?? 'Unassigned' }}</span> | 
                        Timeline: <span class="text-gray-900 font-medium">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'TBD' }} to {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : 'TBD' }}</span>
                    </p>
                </div>
                <div>
                    @if($project->status === 'live')
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Live Survey Collection</span>
                    @elseif($project->status === 'completed')
                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">Completed</span>
                    @elseif($project->status === 'paused')
                        <span class="inline-flex items-center rounded-md bg-yellow-50 px-2.5 py-1 text-xs font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Paused</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20">Planning / Setup</span>
                    @endif
                </div>
            </div>

            <!-- Progress quota section -->
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm font-semibold">
                    <span class="text-gray-600">Respondent Quota Completion</span>
                    <span class="text-gray-900 font-mono">{{ $project->progress_percent }}% ({{ number_format($project->current_responses) }} / {{ number_format($project->target_quota) }})</span>
                </div>
                <!-- Flat corporate progress bar -->
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-gray-900 h-full rounded-full transition-all duration-500" style="width: {{ $project->progress_percent }}%"></div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-sm text-gray-500 shadow-sm">
            You do not have any active research projects tracking. Submit a research request to begin operations.
        </div>
        @endforelse
    </div>
</div>

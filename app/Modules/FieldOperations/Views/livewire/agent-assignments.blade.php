@section('page_title', 'My Field Assignments')

<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Field Operations Dashboard</h1>
            <p class="text-sm text-gray-500">Manage assigned field collection targets, log responses offline in remote regions, and sync logs back to central servers.</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Offline Browser Cache Control Panel -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
        <div class="space-y-1">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Simulated Device Cache Storage</h2>
            <div class="flex items-center gap-3">
                <span class="text-3xl font-bold font-mono text-gray-900">{{ count($localOfflineCache) }}</span>
                <span class="text-sm font-semibold text-gray-500">Survey responses stored offline</span>
            </div>
            <p class="text-xs text-gray-400">Offline responses are held in browser storage. Click "Sync Cache" once network connectivity is restored.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            @if(count($localOfflineCache) > 0)
                <button wire:click="syncCache" class="flex-1 sm:flex-none justify-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Sync Offline Cache
                </button>
                <button wire:click="discardCache" class="flex-1 sm:flex-none justify-center rounded-md border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Discard Cache
                </button>
            @else
                <button disabled class="flex-1 sm:flex-none justify-center rounded-md bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-400 cursor-not-allowed">
                    Sync Cache (No Cache)
                </button>
            @endif
        </div>
    </div>

    <!-- Active Offline Interview Simulator -->
    @if($activeAssignmentId)
    <div class="bg-gray-150 border border-gray-200 p-6 rounded-lg shadow-inner max-w-lg mx-auto bg-gray-50 space-y-6">
        <!-- Mock Phone Layout -->
        <div class="border-4 border-gray-900 rounded-2xl bg-white p-6 shadow-2xl relative">
            <!-- Notch -->
            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-32 h-4 bg-gray-900 rounded-b-xl"></div>
            
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mt-2 mb-6">
                <div>
                    <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-0.5 text-xxs font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Offline mode</span>
                    <h3 class="text-sm font-bold text-gray-950 mt-1">{{ $activeAssignment->project->name }}</h3>
                </div>
                <button wire:click="cancelOffline" class="text-xs font-semibold text-red-600">Exit</button>
            </div>

            @if($activeAssignment->project->survey)
            <form wire:submit.prevent="saveOfflineResponse" class="space-y-6">
                @foreach($activeAssignment->project->survey->questions as $index => $q)
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-900">
                        {{ $index + 1 }}. {{ $q->question_text }}
                    </label>

                    @if($q->type === 'text')
                        <input wire:model="offlineAnswers.{{ $q->id }}" type="text" placeholder="Respond here..." required class="block w-full rounded-md border border-gray-300 px-3 py-2 text-xs focus:border-gray-900 focus:outline-none bg-white">
                    @elseif($q->type === 'number')
                        <input wire:model="offlineAnswers.{{ $q->id }}" type="number" placeholder="Enter number..." required class="block w-full rounded-md border border-gray-300 px-3 py-2 text-xs focus:border-gray-900 focus:outline-none bg-white">
                    @elseif($q->type === 'single_choice')
                        <div class="space-y-1">
                            @foreach($q->options as $optIndex => $opt)
                            <div class="flex items-center">
                                <input wire:model="offlineAnswers.{{ $q->id }}" value="{{ $opt }}" type="radio" id="off_opt_{{ $q->id }}_{{ $optIndex }}" name="off_q_{{ $q->id }}" class="h-3 w-3 border-gray-300 text-gray-950 focus:ring-gray-950">
                                <label for="off_opt_{{ $q->id }}_{{ $optIndex }}" class="ml-2 block text-xs text-gray-700">{{ $opt }}</label>
                            </div>
                            @endforeach
                        </div>
                    @endif
                    @error("offlineAnswers.{$q->id}") <span class="text-xxs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endforeach

                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Save to Local Cache
                </button>
            </form>
            @else
            <p class="text-xs text-red-500 italic">Error: No survey configured for this campaign yet.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Assignments List -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Current Assigned Campaigns</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Project / Campaign</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Target Submissions</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Completed</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/6">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($assignments as $assign)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-900">{{ $assign->project->name }}</span>
                            <span class="text-xs text-gray-500 mt-1">Survey: {{ $assign->project->survey->title ?? 'Unconfigured' }}</span>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-950 font-mono">
                        {{ number_format($assign->target_submissions) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-950 font-mono">
                        {{ number_format($assign->completed_submissions) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        @if($assign->status === 'completed')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Target Met</span>
                        @elseif($assign->status === 'active')
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">Pending</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold">
                        @if($assign->project->survey && $assign->status !== 'completed')
                            <button wire:click="selectAssignmentForOffline({{ $assign->id }})" class="text-gray-900 hover:text-gray-600 underline">Collect (Offline Mode)</button>
                        @else
                            <span class="text-gray-400 text-xs italic">Closed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                        No active field assignments assigned to your profile.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

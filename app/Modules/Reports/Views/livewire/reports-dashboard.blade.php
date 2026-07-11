@section('page_title', 'Reports & Analytics')

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Reports & AI Analytics</h1>
            <p class="text-sm text-gray-500">Aggregate survey response datasets, track quota target stats, and trigger AI-generated executive summaries.</p>
        </div>
        <div>
            <!-- Project Selector Dropdown -->
            <select wire:change="selectProject($event.target.value)" class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                <option value="">Choose Project Campaign...</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if(!$selectedProjectId)
        <!-- Empty State -->
        <div class="bg-white border border-gray-200 rounded-lg p-12 text-center text-sm text-gray-500 shadow-sm">
            Please select a project campaign from the top-right dropdown to inspect data metrics.
        </div>
    @else
        <!-- Active Project Report Details -->
        <div class="space-y-8">
            <!-- Project Card & Quotas -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4 mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $activeProject->name }} Report</h2>
                        <p class="text-xs text-gray-500 mt-1">Status: <span class="font-semibold text-gray-900 uppercase">{{ $activeProject->status }}</span></p>
                    </div>
                    
                    @if($survey)
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                            <button wire:click="exportCsv({{ $survey->id }})" class="flex-1 sm:flex-none justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:border-gray-950 transition">
                                Export CSV
                            </button>
                            <button wire:click="generateAiReport({{ $survey->id }})" class="flex-1 sm:flex-none justify-center rounded-md bg-gray-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                                {{ $isGeneratingAi ? 'Analyzing Data...' : 'Generate AI Summary' }}
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Overall Progress Quota -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="text-gray-500 uppercase tracking-wider">Overall Respondent Quotas</span>
                        <span class="text-gray-900 font-mono">{{ $activeProject->progress_percent }}% ({{ number_format($activeProject->current_responses) }} / {{ number_format($activeProject->target_quota) }})</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gray-900 h-full rounded-full transition-all duration-300" style="width: {{ $activeProject->progress_percent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- AI Summary Card -->
            @if($aiSummary)
                <div class="bg-gray-900 border border-gray-950 rounded-lg p-6 text-white shadow-lg space-y-4">
                    <div class="flex items-center gap-2 border-b border-gray-800 pb-3">
                        <!-- AI Brand icon -->
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-gray-950 text-xs font-bold font-mono">AI</span>
                        <h3 class="text-sm font-bold tracking-wider uppercase">Metrica AI™ Analysis</h3>
                    </div>
                    <div class="text-sm text-gray-300 space-y-4 leading-relaxed font-sans">
                        {!! nl2br(e($aiSummary)) !!}
                    </div>
                </div>
            @endif

            <!-- Question Aggregates -->
            @if($survey)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($stats as $qId => $stat)
                        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-4">
                            <div>
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 uppercase tracking-wide">
                                    {{ str_replace('_', ' ', $stat['type']) }}
                                </span>
                                <h3 class="text-sm font-bold text-gray-900 mt-2">{{ $stat['question'] }}</h3>
                            </div>

                            @if(in_array($stat['type'], ['single_choice', 'multiple_choice']))
                                <!-- Horizontal Charts -->
                                <div class="space-y-3 pt-2">
                                    @foreach($stat['data'] as $choice)
                                        <div class="space-y-1">
                                            <div class="flex justify-between items-center text-xs font-semibold">
                                                <span class="text-gray-700">{{ $choice['option'] }}</span>
                                                <span class="text-gray-900 font-mono">{{ $choice['count'] }} answers ({{ $choice['percentage'] }}%)</span>
                                            </div>
                                            <!-- Mini progress bar representing visual chart -->
                                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-gray-900 h-full rounded-full transition-all duration-300" style="width: {{ $choice['percentage'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Text/Numeric lists -->
                                <div class="space-y-2 pt-2 border-t border-gray-50">
                                    <h4 class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Recent Raw Responses</h4>
                                    <ul class="divide-y divide-gray-100">
                                        @forelse($stat['recent'] as $answer)
                                            <li class="py-2 text-xs font-medium text-gray-700 italic">"{{ $answer }}"</li>
                                        @empty
                                            <li class="py-2 text-xs text-gray-400 italic">No responses logged yet.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-sm text-gray-500 shadow-sm">
                    No survey has been designed for this project campaign yet.
                </div>
            @endif
        </div>
    @endif
</div>

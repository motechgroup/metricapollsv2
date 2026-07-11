@section('page_title', 'Qualification Tests')

<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Qualification Tests</h1>
            <p class="text-sm text-gray-500">Qualify for highly-rewarding research surveys by verifying your purchasing behaviors and lifestyle demographics.</p>
        </div>
        @if(!$activeTestId)
            <button type="button" wire:click="refreshTests" class="inline-flex items-center gap-2 rounded-md bg-white border border-gray-300 px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                🔄 Refresh Tests Pool
            </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-md shadow-sm">
            {{ session('info') }}
        </div>
    @endif

    @if($activeTestId)
        <!-- Active Test Wizard -->
        <div class="bg-white border border-gray-200 p-8 rounded-lg shadow-sm max-w-xl mx-auto space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <div>
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xxs font-bold text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-1">{{ $activeTest->level }}</span>
                    <h2 class="text-lg font-bold text-gray-900">{{ $activeTest->title }}</h2>
                </div>
                <button wire:click="cancelTest" class="text-xs font-semibold text-gray-500 hover:text-gray-900">Cancel Test</button>
            </div>

            <!-- Progress tracker -->
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-gray-500 uppercase">
                    <span>Progress</span>
                    <span>Question {{ $currentQuestionIndex + 1 }} of {{ count($activeTest->questions) }}</span>
                </div>
                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gray-900 h-full rounded-full transition-all duration-300" style="width: {{ (($currentQuestionIndex) / count($activeTest->questions)) * 100 }}%"></div>
                </div>
            </div>

            <!-- Question prompt -->
            <div class="space-y-4 pt-4">
                <h3 class="text-base font-bold text-gray-900">{{ $activeTest->questions[$currentQuestionIndex]['text'] }}</h3>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($activeTest->questions[$currentQuestionIndex]['options'] as $option)
                    <button wire:click="answerQuestion('{{ $option }}')" class="w-full text-left rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:border-gray-900 hover:bg-gray-50 transition">
                        {{ $option }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- System unpredictable alert banner -->
        <div class="rounded-md bg-slate-50 p-4 border border-slate-200">
            <h4 class="text-xxs font-bold text-slate-800 uppercase tracking-wider mb-1">🔐 Dynamic Anti-Predictability Protocol</h4>
            <p class="text-xxs text-slate-600 leading-relaxed font-medium">
                To guarantee audit integrity, qualification tests are drawn at random from a comprehensive pool of 150 verified demographic checklists. Multiple concurrent sessions receive independent subsets. Fresh batches can be loaded at any time using the <strong>Refresh Tests Pool</strong> action.
            </p>
        </div>

        <!-- Available Tests List -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($tests as $test)
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xxs font-bold text-indigo-700 ring-1 ring-inset ring-indigo-700/10 mb-1.5">{{ $test->level }}</span>
                            <h2 class="text-base font-bold text-gray-900">{{ $test->title }}</h2>
                        </div>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-900 font-mono whitespace-nowrap">+{{ $test->reward_points }} pts</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">{{ $test->description }}</p>
                </div>

                <div class="border-t border-gray-150 mt-6 pt-4 flex justify-between items-center">
                    @if(in_array($test->id, $completedIds))
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Completed / Qualified</span>
                        <button disabled class="text-xs font-bold text-gray-400 cursor-not-allowed">Passed</button>
                    @else
                        <span class="text-xxs text-gray-400 font-medium">Est: ~1 min</span>
                        <button wire:click="selectTest({{ $test->id }})" class="rounded-md bg-gray-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Take Test
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-sm text-gray-500 shadow-sm col-span-3">
                No active qualification tests are available at this moment. You have successfully completed all eligible tests for your current badge level!
            </div>
            @endforelse
        </div>
    @endif
</div>

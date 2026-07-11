@section('page_title', 'Survey Questionnaire Designer')

<div class="space-y-8" x-data="{ qType: @entangle('questionType') }">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Survey Designer</h1>
            <p class="text-sm text-gray-500">Configure questionnaire meta details and build custom structured fields for campaign: <span class="font-semibold text-gray-950">{{ $project->name }}</span></p>
        </div>
        <a href="{{ route('admin.projects') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            Back to Projects
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Side: Survey Configurations and Designer Form -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Survey Details Metadata Form -->
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Survey Metadata Details</h2>
                <form wire:submit.prevent="saveSurveyMeta" class="space-y-6">
                    <div class="space-y-4">
                        <div>
                            <label for="surveyTitle" class="block text-sm font-medium text-gray-700">Survey Title</label>
                            <input wire:model="surveyTitle" type="text" id="surveyTitle" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            @error('surveyTitle') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="surveyDescription" class="block text-sm font-medium text-gray-700">Survey Instructions / Introduction</label>
                            <textarea wire:model="surveyDescription" id="surveyDescription" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"></textarea>
                            @error('surveyDescription') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="surveyStatus" class="block text-sm font-medium text-gray-700">Publication Status</label>
                            <select wire:model="surveyStatus" id="surveyStatus" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <option value="draft">Draft (Private)</option>
                                <option value="published">Published (Open for collection)</option>
                                <option value="archived">Archived (Closed)</option>
                            </select>
                            @error('surveyStatus') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="border-t border-gray-100 pt-4 text-right">
                        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Save Survey Meta
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Questions List -->
            <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Structured Questions</h2>
                <div class="space-y-4">
                    @forelse($questions as $index => $question)
                    <div class="border border-gray-100 rounded-md p-4 bg-gray-50 flex justify-between items-start gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-400 font-mono">Q{{ $index + 1 }}</span>
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-1.5 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 uppercase tracking-wide">
                                    {{ str_replace('_', ' ', $question->type) }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 mt-1">{{ $question->question_text }}</h3>
                            
                            @if(in_array($question->type, ['single_choice', 'multiple_choice']))
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($question->options as $opt)
                                <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-2 py-0.5 text-xs text-gray-700 font-medium">{{ $opt }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <button onclick="confirm('Are you sure you want to remove this question?') || event.stopImmediatePropagation()" wire:click="deleteQuestion({{ $question->id }})" class="text-xs font-semibold text-red-600 hover:underline">
                            Remove
                        </button>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 italic text-center py-6">No questions added yet. Use the sidebar to construct questionnaire items.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Side: Add Question Form -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm h-fit">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Add Questionnaire Item</h2>
            <form wire:submit.prevent="addQuestion" class="space-y-6">
                <div>
                    <label for="questionType" class="block text-sm font-medium text-gray-700">Question Input Type</label>
                    <select wire:model="questionType" id="questionType" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        <option value="text">Text response (Single line)</option>
                        <option value="single_choice">Single Choice (Radio button)</option>
                        <option value="multiple_choice">Multiple Choice (Checkboxes)</option>
                        <option value="number">Numeric response</option>
                    </select>
                    @error('questionType') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="questionText" class="block text-sm font-medium text-gray-700">Question Text / Prompt</label>
                    <textarea wire:model="questionText" id="questionText" rows="3" placeholder="e.g. How often do you buy carbonated soft drinks?" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"></textarea>
                    @error('questionText') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Choice Options (Visible only when Choice type is selected) -->
                <div x-show="qType === 'single_choice' || qType === 'multiple_choice'" style="display: none;">
                    <label for="questionOptionsText" class="block text-sm font-medium text-gray-700">Answer Options (Comma-separated)</label>
                    <input wire:model="questionOptionsText" type="text" id="questionOptionsText" placeholder="e.g. Daily, Weekly, Monthly, Never" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    <p class="text-xxs text-gray-400 mt-1">Separate options with commas to populate check/radio selections.</p>
                    @error('questionOptionsText') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Add to Survey
                </button>
            </form>
        </div>
    </div>
</div>

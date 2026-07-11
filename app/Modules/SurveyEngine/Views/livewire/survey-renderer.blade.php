<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="w-full max-w-2xl bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
        
        @if($isSubmitted)
            <!-- Thank You Screen / Fraud Review -->
            @if($isFraudDetected)
                <div class="text-center py-10 space-y-6 animate-fade-in">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-2xl font-bold tracking-tight text-red-800">Submission Rejected</h2>
                        <p class="text-sm text-red-700 font-semibold">{{ $fraudReasonDetail }}</p>
                        <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed">Our automated anti-fraud security engine detects speed clicking and inconsistency. To secure survey data integrity for our parastatal and corporate clients, this response has been blocked from payouts.</p>
                    </div>
                    <div class="border-t border-gray-100 pt-6">
                        <a href="{{ route('corporate.index') }}" class="rounded-md bg-gray-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Back to Home
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-10 space-y-6">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-50">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Survey Completed Successfully</h2>
                        <p class="text-sm text-gray-600">Your verified responses have been saved in the secure research database.</p>
                        
                        @if($survey->is_paid)
                            <div class="mt-4 inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-bold text-green-800 ring-1 ring-inset ring-green-600/20">
                                + KES {{ number_format($survey->payout_amount, 2) }} Credited to M-Pesa Wallet
                            </div>
                        @endif

                        @if($survey->is_qualification)
                            <div class="mt-4 inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-bold text-blue-800 ring-1 ring-inset ring-blue-600/20">
                                Qualification Test Passed &amp; 150 points Awarded!
                            </div>
                        @endif
                    </div>
                    @if (session()->has('success'))
                        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="border-t border-gray-100 pt-6">
                        <a href="{{ route('corporate.index') }}" class="rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Back to Home
                        </a>
                    </div>
                </div>
            @endif
        @else
            <!-- Survey Brief & Header -->
            <div class="border-b border-gray-100 pb-6 mb-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @if($survey->is_qualification)
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-bold text-blue-800 ring-1 ring-inset ring-blue-600/20">
                            Training / Qualification Test
                        </span>
                    @endif
                    @if($survey->is_paid)
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-bold text-green-800 ring-1 ring-inset ring-green-600/20">
                            KES {{ number_format($survey->payout_amount, 2) }} M-Pesa Payout
                        </span>
                    @endif
                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-bold text-gray-800 ring-1 ring-inset ring-gray-500/10">
                        Requires: {{ $survey->min_badge_level }} Badge
                    </span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $survey->title }}</h1>
                @if($survey->description)
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $survey->description }}</p>
                @endif
            </div>

            @if($survey->status !== 'published')
                <!-- Warning for non-published surveys -->
                <div class="text-center py-8 space-y-4">
                    <p class="text-sm text-gray-500 italic">This survey campaign is currently not open to accepting respondent entries.</p>
                    <a href="{{ route('corporate.index') }}" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Back to Homepage
                    </a>
                </div>
            @else
                <!-- Dynamic Questionnaire Form -->
                <form wire:submit.prevent="submit" class="space-y-8">
                    @foreach($survey->questions as $index => $q)
                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-gray-900">
                            {{ $index + 1 }}. {{ $q->question_text }}
                        </label>

                        @if($q->type === 'text')
                            <!-- Text input -->
                            <input wire:model="answers.{{ $q->id }}" type="text" placeholder="Your response here..." required class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                        
                        @elseif($q->type === 'number')
                            <!-- Number input -->
                            <input wire:model="answers.{{ $q->id }}" type="number" placeholder="Enter number..." required class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                        
                        @elseif($q->type === 'single_choice')
                            <!-- Single choice (radio buttons) -->
                            <div class="space-y-2">
                                @foreach($q->options as $optIndex => $opt)
                                <div class="flex items-center">
                                    <input wire:model="answers.{{ $q->id }}" value="{{ $opt }}" type="radio" id="opt_{{ $q->id }}_{{ $optIndex }}" name="q_{{ $q->id }}" class="h-4 w-4 border-gray-300 text-gray-950 focus:ring-gray-950 bg-white">
                                    <label for="opt_{{ $q->id }}_{{ $optIndex }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $opt }}</label>
                                </div>
                                @endforeach
                            </div>

                        @elseif($q->type === 'multiple_choice')
                            <!-- Multiple choice (checkboxes) -->
                            <div class="space-y-2">
                                @foreach($q->options as $optIndex => $opt)
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input wire:model="answers.{{ $q->id }}" value="{{ $opt }}" type="checkbox" id="opt_{{ $q->id }}_{{ $optIndex }}" class="h-4 w-4 rounded border-gray-300 text-gray-950 focus:ring-gray-950 bg-white">
                                    </div>
                                    <label for="opt_{{ $q->id }}_{{ $optIndex }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $opt }}</label>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        @error("answers.{$q->id}")
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    @endforeach

                    <!-- Submit action -->
                    <div class="border-t border-gray-100 pt-6">
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Submit Survey Response
                        </button>
                    </div>
                </form>
            @endif
        @endif
    </div>
</div>

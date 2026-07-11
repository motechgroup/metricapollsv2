<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow animate-fade-in">
    <div class="w-full max-w-2xl bg-white border border-gray-200 p-8 rounded-lg shadow-sm">
        
        @if(!$isEligible)
            <!-- Custom Premium Ineligibility Warning and Help Guidance -->
            <div class="text-center py-6 space-y-6 animate-fade-in">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-50">
                    <svg class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                
                @if($ineligibilityReason === 'badge_low')
                    <div class="space-y-3">
                        <h2 class="text-xl font-extrabold text-gray-950 tracking-tight">🔒 Survey Locked: Higher Badge Required</h2>
                        <div class="max-w-md mx-auto text-sm text-gray-500 leading-relaxed space-y-2">
                            <p>This market study requires a <span class="font-bold text-gray-900 underline">{{ $requiredBadge }} Badge</span> to unlock. Your current account level is <span class="font-bold text-gray-900 underline">{{ $currentBadge }}</span>.</p>
                            <p class="text-xs text-gray-400 bg-gray-50 p-2.5 rounded border border-gray-150">To maintain high-quality data entries, our enterprise clients require panelists to graduate from training courses and successfully pass verification checks to level up.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        <h2 class="text-xl font-extrabold text-gray-950 tracking-tight">🌍 Region Restricted: Target Cohort Mismatch</h2>
                        <div class="max-w-md mx-auto text-sm text-gray-500 leading-relaxed space-y-2">
                            <p>This brand product survey is geofenced and open exclusively to respondents residing in <span class="font-bold text-gray-900 underline">{{ $requiredCountry }}</span>.</p>
                            <p>Your current geolocated connection is detected from <span class="font-bold text-red-600 underline">{{ $userCountry }}</span>.</p>
                            <p class="text-xs text-gray-400 bg-gray-50 p-2.5 rounded border border-gray-150">To ensure compliance with national consumer audits, Metrica blocks submissions routed from outside target territories.</p>
                        </div>
                    </div>
                @endif

                <div class="bg-gray-50 border border-gray-150 rounded-lg p-5 max-w-md mx-auto text-left space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">💡 How to qualify for this survey</h3>
                    <ul class="text-xs text-gray-600 space-y-2 list-disc pl-4 leading-relaxed">
                        @if($ineligibilityReason === 'badge_low')
                            <li><strong>Earn XP in Academy:</strong> Visit the <a href="{{ route('panelist.academy') }}" class="text-blue-600 hover:text-blue-800 underline font-semibold">Metrica Academy</a> to finish courses. Graduate to earn up to 200 EXP per course.</li>
                            <li><strong>Pass Qualification Tests:</strong> Head to <a href="{{ route('panelist.qualifications') }}" class="text-blue-600 hover:text-blue-800 underline font-semibold">Qualifications</a> to take level tests. Each pass grants matching badge EXP.</li>
                            <li><strong>Demographics Upgrade:</strong> Make sure your profile and phone number are fully verified on the dashboard to earn an instant 100 EXP boost!</li>
                        @else
                            <li><strong>Verify Region coordinates:</strong> If you are physically inside {{ $requiredCountry }}, ensure you turn off any active VPNs/Proxies and allow location access on your mobile browser.</li>
                            <li><strong>Check active region:</strong> Toggle or select your active region in the dashboard settings to ensure your phone country code matches.</li>
                        @endif
                    </ul>
                </div>

                <div class="flex justify-center gap-3 pt-4 border-t border-gray-100">
                    @if($ineligibilityReason === 'badge_low')
                        <a href="{{ route('panelist.qualifications') }}" class="rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Take Level Tests
                        </a>
                        <a href="{{ route('panelist.academy') }}" class="rounded-md border border-gray-300 px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Open Academy
                        </a>
                    @else
                        <a href="{{ route('dashboard.index') }}" class="rounded-md bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Return to Dashboard
                        </a>
                    @endif
                </div>
            </div>
        @else
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
                            <p class="text-xs text-gray-550 max-w-md mx-auto leading-relaxed">Our automated anti-fraud security engine detects speed clicking and inconsistency. To secure survey data integrity for our parastatal and corporate clients, this response has been blocked from payouts.</p>
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
                                    + KES {{ number_format($survey->payout_amount, 2) }} Credited to Wallet
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
                                KES {{ number_format($survey->payout_amount, 2) }} Payout
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
        @endif
    </div>
</div>

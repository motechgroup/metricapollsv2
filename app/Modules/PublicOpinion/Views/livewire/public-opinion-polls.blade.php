<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 w-full">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Public Opinion & Micro-Polls</h1>
            <p class="mt-2 text-sm text-gray-500 max-w-xl">Cast your vote on current socioeconomic topics in East Africa. Results aggregate instantly and are open for public audit.</p>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($polls as $poll)
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex justify-between items-start gap-4 border-b border-gray-100 pb-3 mb-4">
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xxs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Open Poll</span>
                        <span class="text-xs text-gray-400 font-mono">{{ number_format($poll->votes_count) }} votes cast</span>
                    </div>
                    <h2 class="text-sm font-bold text-gray-950 leading-relaxed">{{ $poll->topic }}</h2>
                </div>

                <!-- Voting Options / Results -->
                <div>
                    @if(in_array($poll->id, $votedPollIds))
                        <!-- Results Render -->
                        <div class="space-y-4">
                            @foreach($results[$poll->id] as $res)
                            <div class="space-y-1">
                                <div class="flex justify-between items-center text-xs font-semibold">
                                    <span class="text-gray-700">{{ $res['option'] }}</span>
                                    <span class="text-gray-950 font-mono">{{ $res['percentage'] }}% ({{ $res['count'] }})</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-gray-900 h-full rounded-full transition-all duration-350" style="width: {{ $res['percentage'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Voting Buttons -->
                        <div class="grid grid-cols-1 gap-2.5">
                            @foreach($poll->options as $option)
                            <button wire:click="vote({{ $poll->id }}, '{{ $option }}')" class="w-full text-left rounded-md border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold text-gray-700 hover:border-gray-950 hover:bg-gray-50 transition">
                                {{ $option }}
                            </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

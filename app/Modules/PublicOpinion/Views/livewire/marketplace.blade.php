<div class="py-16 sm:py-24 bg-gray-50 flex items-center justify-center flex-grow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 w-full">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Research Report Marketplace</h1>
            <p class="mt-2 text-sm text-gray-500 max-w-2xl">Purchase ready-made, high-quality consumer insight research reports compiled by Metrica Research Analysts.</p>
        </div>

        @if (session()->has('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md flex justify-between items-center">
                <span>{{ session('success') }}</span>
                @if($purchasedReportId)
                    <button wire:click="download({{ $purchasedReportId }})" class="rounded bg-green-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-green-700 transition">
                        Download Report File
                    </button>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($reports as $report)
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex justify-between items-start gap-4">
                        <h2 class="text-lg font-bold text-gray-900">{{ $report->title }}</h2>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-0.5 text-sm font-bold text-gray-900 font-mono">${{ number_format($report->price, 2) }}</span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $report->description }}</p>
                </div>

                <div class="border-t border-gray-100 mt-6 pt-4 flex justify-between items-center">
                    <span class="text-xs text-gray-400 font-medium">Format: PDF / Text document</span>
                    @if($purchasedReportId == $report->id)
                        <button wire:click="download({{ $report->id }})" class="rounded-md bg-green-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-green-700 transition">
                            Download Now
                        </button>
                    @else
                        <button wire:click="buy({{ $report->id }})" class="rounded-md bg-gray-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                            Purchase Insights
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

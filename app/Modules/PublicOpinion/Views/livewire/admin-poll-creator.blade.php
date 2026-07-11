@section('page_title', 'Create AI-Driven Poll & Report')

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">AI-Driven Poll & Report Builder</h1>
            <p class="text-sm text-gray-500">Construct custom polls for brands, political groups, or products. Feed demographic metrics to compile an automated AI market intelligence report.</p>
        </div>
        <a href="{{ route('admin.projects') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            Cancel
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="generateReport" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Poll Configurations -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Meta Info Card -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Campaign Settings</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Poll Topic / Title</label>
                            <input wire:model="title" type="text" id="title" placeholder="e.g. Q3 Kenya Carbonated Soft Drinks Popularity Audit" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Research Focus Category</label>
                            <select wire:model.live="category" id="category" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                                <option value="Brand Performance">Brand Performance</option>
                                <option value="Political Popularity">Political Popularity</option>
                                <option value="Product Feasibility">Product Feasibility</option>
                                <option value="Media Audience Measurement">Media Audience Measurement</option>
                            </select>
                            @error('category') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Report Visibility</label>
                            <div class="mt-3 flex items-center">
                                <input wire:model="isPublic" type="checkbox" id="isPublic" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <label for="isPublic" class="ml-2 text-sm text-gray-700">Publish report for public download (Visitors can access)</label>
                            </div>
                            @error('isPublic') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings Card -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
                    <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Advanced Research Parameters</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="researchDate" class="block text-sm font-medium text-gray-700">Research Date</label>
                            <input wire:model.live.debounce.250ms="researchDate" type="date" id="researchDate" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            @error('researchDate') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="releaseDate" class="block text-sm font-medium text-gray-700">Release Date</label>
                            <input wire:model.live.debounce.250ms="releaseDate" type="date" id="releaseDate" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            @error('releaseDate') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="sampleSize" class="block text-sm font-medium text-gray-700">Analyzed Sample Size</label>
                            <input wire:model.live.debounce.250ms="sampleSize" type="number" id="sampleSize" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            @error('sampleSize') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="region" class="block text-sm font-medium text-gray-700">Region / Covered Territory</label>
                            <input wire:model.live.debounce.250ms="region" type="text" id="region" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            @error('region') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="initialDownloads" class="block text-sm font-medium text-gray-700">Initial Download Count (Mock counter)</label>
                            <input wire:model="initialDownloads" type="number" id="initialDownloads" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            @error('initialDownloads') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="methodology" class="block text-sm font-medium text-gray-700">Detailed Research Methodology</label>
                            <textarea wire:model="methodology" id="methodology" rows="3" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"></textarea>
                            @error('methodology') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Options / Figures Builder -->
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <h2 class="text-base font-bold text-gray-900">Metrics & Options</h2>
                        <button type="button" wire:click="addOption" class="text-xs font-bold text-gray-900 hover:text-gray-600 underline">Add Option Row</button>
                    </div>

                    <div class="space-y-4">
                        @foreach($options as $index => $opt)
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center border border-gray-100 p-4 rounded-md bg-gray-50 relative">
                            <!-- Option Name -->
                            <div class="sm:col-span-5">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Option / Cohort Name</label>
                                <input wire:model="options.{{ $index }}.name" type="text" placeholder="e.g. Coca-Cola" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs focus:border-gray-900 focus:outline-none bg-white">
                                @error("options.{$index}.name") <span class="text-xxs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Figures/Votes -->
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Figure / Vote Value</label>
                                <input wire:model="options.{{ $index }}.votes" type="number" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs focus:border-gray-900 focus:outline-none bg-white">
                                @error("options.{$index}.votes") <span class="text-xxs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo upload -->
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Logo / Brand Image</label>
                                <input wire:model="options.{{ $index }}.logo" type="file" class="mt-1 block w-full text-xxs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xxs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                                @error("options.{$index}.logo") <span class="text-xxs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Remove Button -->
                            <div class="sm:col-span-1 text-right mt-4 sm:mt-0">
                                <button type="button" wire:click="removeOption({{ $index }})" class="text-red-600 hover:text-red-800 text-xs font-bold">Delete</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Side: AI summary teaser card -->
            <div class="space-y-6">
                <div class="bg-gray-900 border border-gray-950 p-6 rounded-lg shadow-lg text-white space-y-4 h-fit">
                    <div class="flex items-center gap-2 border-b border-gray-800 pb-3">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white text-gray-950 text-xs font-bold font-mono">AI</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider">Metrica AI™ Report Engine</h3>
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed">The AI Report Engine dynamically compiles entered values into custom market research briefs. Projections are parsed automatically based on categories:</p>
                    <ul class="text-xxs text-gray-400 space-y-2 list-disc pl-4 font-sans leading-relaxed">
                        <li><strong>Brands:</strong> Evaluates retail performance, competitor metrics, and market share trends.</li>
                        <li><strong>Politicians:</strong> Compiles voter consensus indicators and regional trackers.</li>
                        <li><strong>Products:</strong> Highlights consumer feasibility interest metrics and pilot launch suggestions.</li>
                        <li><strong>Media:</strong> Measures viewer share statistics and programming projections.</li>
                    </ul>
                </div>

                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-4">
                    <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                        Generate AI Report & Create Poll
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

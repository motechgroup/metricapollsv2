@section('page_title', 'Request Research')

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Request New Research</h1>
        <p class="text-sm text-gray-500">Provide the requirements and objectives of your research to obtain a feasibility review and quote.</p>
    </div>

    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
        <form wire:submit.prevent="submit" class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Survey Title / Research Topic</label>
                    <input wire:model="title" type="text" id="title" placeholder="e.g. Q3 Kenya FMCG Product Brand Awareness Study" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Detailed Description & Objectives</label>
                    <textarea wire:model="description" id="description" rows="5" placeholder="Detail the key metrics, regions to cover, demographics, or other core goals of this survey." required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"></textarea>
                    @error('description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="target_audience" class="block text-sm font-medium text-gray-700">Target Audience Demographics</label>
                        <input wire:model="target_audience" type="text" id="target_audience" placeholder="e.g. Females 18-35 in Nairobi" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        @error('target_audience') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sample_size" class="block text-sm font-medium text-gray-700">Target Sample Size (Quota)</label>
                        <input wire:model="sample_size" type="number" id="sample_size" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        @error('sample_size') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="estimated_budget" class="block text-sm font-medium text-gray-700">Estimated Budget ($ USD)</label>
                        <input wire:model="estimated_budget" type="number" id="estimated_budget" placeholder="e.g. 5000 (Optional)" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        @error('estimated_budget') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Submit Research Brief
                </button>
                <a href="{{ route('client.requests') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

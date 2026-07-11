@section('page_title', 'Manage Surveys')

<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 font-sans">Online Surveys &amp; Tests Manager</h1>
            <p class="text-sm text-gray-500">Register new marketing questionnaires, configure MPesa payout rates, set verification badges, and edit survey questions.</p>
        </div>
        @if(!$isFormOpen)
        <button wire:click="openForm" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
            Create Online Survey
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Panel -->
    @if($isFormOpen)
    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm space-y-6">
        <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">
            {{ $surveyId ? 'Modify Online Survey settings' : 'Configure New Online Survey' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Survey Title</label>
                    <input wire:model="title" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                    @error('title') <span class="text-xs text-red-650 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description / Respondent Instructions</label>
                    <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white"></textarea>
                    @error('description') <span class="text-xs text-red-650 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Project / Research Campaign</label>
                    <select wire:model="project_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                        <option value="">Select Project</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <span class="text-xs text-red-650 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Campaign Status</label>
                    <select wire:model="status" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                        <option value="draft">Draft</option>
                        <option value="published">Published / Open</option>
                        <option value="archived">Archived / Closed</option>
                    </select>
                    @error('status') <span class="text-xs text-red-650 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="border-t border-gray-100 pt-4 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Survey Type (Paid Monetization)</label>
                        <div class="mt-3 flex items-center">
                            <input wire:model="is_paid" type="checkbox" id="is_paid" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <label for="is_paid" class="ml-2 text-sm text-gray-700">This is a Paid Online Survey</label>
                        </div>
                        @error('is_paid') <span class="text-xs text-red-650 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payout Amount (KES to M-Pesa)</label>
                        <input wire:model="payout_amount" type="number" step="0.01" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white" {{ !$is_paid ? 'disabled bg-gray-50' : '' }}>
                        @error('payout_amount') <span class="text-xs text-red-655 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Survey Type (Training / Qualification)</label>
                        <div class="mt-3 flex items-center">
                            <input wire:model="is_qualification" type="checkbox" id="is_qualification" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <label for="is_qualification" class="ml-2 text-sm text-gray-700">This is a Mandatory Qualification/Training Test</label>
                        </div>
                        @error('is_qualification') <span class="text-xs text-red-655 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Required Minimum Badge Level</label>
                        <select wire:model="min_badge_level" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none bg-white">
                            <option value="Bronze">Bronze (Entry level)</option>
                            <option value="Silver">Silver (Experienced)</option>
                            <option value="Gold">Gold (Expert / High Yields)</option>
                        </select>
                        @error('min_badge_level') <span class="text-xs text-red-655 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-800 transition">
                    Save Survey Configuration
                </button>
                <button type="button" wire:click="closeForm" class="rounded border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Filtering & Listing -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex flex-wrap gap-2">
                <button wire:click="$set('filterType', 'all')" class="px-3 py-1 text-xs font-bold rounded {{ $filterType === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-650 hover:bg-gray-200' }}">All</button>
                <button wire:click="$set('filterType', 'paid')" class="px-3 py-1 text-xs font-bold rounded {{ $filterType === 'paid' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-650 hover:bg-gray-200' }}">Paid Campaigns</button>
                <button wire:click="$set('filterType', 'qualification')" class="px-3 py-1 text-xs font-bold rounded {{ $filterType === 'qualification' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-650 hover:bg-gray-200' }}">Training Tests</button>
            </div>

            <div class="w-full sm:w-64">
                <input wire:model.live="search" type="text" placeholder="Search surveys..." class="block w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs focus:border-gray-900 focus:outline-none bg-white">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-900">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Survey Title</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Reward Rate</th>
                        <th class="px-6 py-3">Criteria</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($surveys as $survey)
                    <tr>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-950 block">{{ $survey->title }}</span>
                            <span class="text-xxs text-gray-400 block mt-0.5">Project Scope: {{ $survey->project->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($survey->is_qualification)
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xxs font-semibold text-blue-750 ring-1 ring-inset ring-blue-700/10">TRAINING TEST</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xxs font-semibold text-green-750 ring-1 ring-inset ring-green-700/10">PAID</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-950">
                            @if($survey->is_paid)
                                KES {{ number_format($survey->payout_amount, 2) }}
                            @else
                                &mdash;
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-600">
                            {{ $survey->min_badge_level }} Badge
                        </td>
                        <td class="px-6 py-4">
                            @if($survey->status === 'published')
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xxs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                            @elseif($survey->status === 'draft')
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xxs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">Draft</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xxs font-semibold text-red-700 ring-1 ring-inset ring-red-650/20">Archived</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <!-- Link to Survey Designer -->
                            <a href="{{ route('admin.projects.survey-design', $survey->project_id) }}" class="text-xxs font-bold text-gray-900 border border-gray-300 px-2.5 py-1.5 rounded hover:bg-gray-50 transition">Edit Questions</a>
                            
                            <button wire:click="openForm({{ $survey->id }})" class="text-xxs font-bold text-gray-600 border border-gray-200 px-2 py-1 rounded bg-gray-50 hover:bg-gray-100">Settings</button>
                            <button onclick="confirm('Delete survey and all response logs?') || event.stopImmediatePropagation()" wire:click="delete({{ $survey->id }})" class="text-xxs font-bold text-red-600 border border-red-200 px-2 py-1 rounded bg-red-50 hover:bg-red-100">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-xs text-gray-400 italic">No online surveys or tests registered.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('page_title', 'Project Operations')

<div class="space-y-8">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Project Operations</h1>
            <p class="text-sm text-gray-500">Coordinate research timelines, assign project managers, adjust respondent quotas, and track completion progress.</p>
        </div>
        @if(!$isFormOpen)
        <button wire:click="openForm" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
            Create Project
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Section (Create / Edit) -->
    @if($isFormOpen)
    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-6">{{ $projectId ? 'Edit Project Profile' : 'Initiate New Project' }}</h2>
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Title / Name</label>
                    <input wire:model="name" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Client Organization</label>
                    <select wire:model="client_organization_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        <option value="">Select Organization</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                    @error('client_organization_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assigned Project Manager</label>
                    <select wire:model="project_manager_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        <option value="">Unassigned (Pending PM Assign)</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}">{{ $mgr->name }} ({{ $mgr->roles->first()->name ?? 'Manager' }})</option>
                        @endforeach
                    </select>
                    @error('project_manager_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target Respondent Quota</label>
                    <input wire:model="target_quota" type="number" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('target_quota') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Gathered Responses</label>
                    <input wire:model="current_responses" type="number" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('current_responses') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Budget ($ USD)</label>
                    <input wire:model="budget" type="number" step="0.01" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('budget') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Operational Start Date</label>
                    <input wire:model="start_date" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('start_date') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Operational End Date</label>
                    <input wire:model="end_date" type="date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                    @error('end_date') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Status</label>
                    <select wire:model="status" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        <option value="planning">Planning</option>
                        <option value="live">Live (Collecting)</option>
                        <option value="paused">Paused</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('status') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center gap-4 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Save Project
                </button>
                <button type="button" wire:click="closeForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Project List Cards -->
    <div class="space-y-6">
        @forelse($projects as $proj)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $proj->name }}</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Client: <span class="text-gray-950 font-semibold">{{ $proj->clientOrganization->name }}</span> | 
                        Manager: <span class="text-gray-950 font-semibold">{{ $proj->projectManager->name ?? 'Unassigned' }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    @if($proj->status === 'live')
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20">Live (Collecting)</span>
                    @elseif($proj->status === 'completed')
                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-500/10">Completed</span>
                    @elseif($proj->status === 'paused')
                        <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Paused</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20">Planning</span>
                    @endif
                    <div class="text-sm font-medium space-x-3">
                        <a href="{{ route('admin.projects.survey-design', ['projectId' => $proj->id]) }}" class="text-gray-900 hover:text-gray-600 underline font-semibold mr-1">Design Survey</a>
                        <button wire:click="openForm({{ $proj->id }})" class="text-gray-900 hover:text-gray-600">Edit</button>
                        <button onclick="confirm('Are you sure you want to delete this project?') || event.stopImmediatePropagation()" wire:click="delete({{ $proj->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                    </div>
                </div>
            </div>

            <!-- Progress Quota & Simulators -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center pt-2">
                <div class="md:col-span-2 space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="text-gray-500 uppercase tracking-wider">Cohort Quota Progress</span>
                        <span class="text-gray-900 font-mono">{{ $proj->progress_percent }}% ({{ number_format($proj->current_responses) }} / {{ number_format($proj->target_quota) }})</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gray-900 h-full rounded-full transition-all duration-300" style="width: {{ $proj->progress_percent }}%"></div>
                    </div>
                </div>
                <!-- Simulation Area (Phase 3 Engine demo) -->
                <div class="flex gap-2 items-center justify-end">
                    <button wire:click="simulateCollection({{ $proj->id }})" class="rounded-md border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-gray-950 transition">
                        Simulate Response (+{{ $simulatedIncrement }})
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-sm text-gray-500 shadow-sm">
            No active research campaigns initiated. Approve a client request or click "Create Project" to start one.
        </div>
        @endforelse
    </div>
</div>

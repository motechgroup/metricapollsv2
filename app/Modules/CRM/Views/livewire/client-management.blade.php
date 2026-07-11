@section('page_title', 'Client Directory')

<div class="space-y-8 animate-fade-in">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 font-sans">Client Directory (CRM)</h1>
            <p class="text-sm text-gray-500">Manage corporate, NGO, and government client organizations, link accounts, and assign research projects.</p>
        </div>
        @if(!$isFormOpen)
        <button wire:click="openForm" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
            Register Organization
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form Section -->
    @if($isFormOpen)
    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-6">{{ $orgId ? 'Edit Organization Details' : 'Register New Client Organization' }}</h2>
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Organization Name</label>
                    <input wire:model="name" type="text" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                    @error('name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Industry Sector</label>
                    <input wire:model="industry" type="text" placeholder="e.g. FMCG, Healthcare, Tech, Government" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                    @error('industry') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Physical Address</label>
                    <input wire:model="address" type="text" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                    @error('address') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Website URL</label>
                    <input wire:model="website" type="text" placeholder="https://example.com" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                    @error('website') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center gap-4 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Save Details
                </button>
                <button type="button" wire:click="closeForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Organizations List -->
    <div class="space-y-6">
        @forelse($organizations as $org)
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm space-y-6">
            <!-- Header details -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-900">{{ $org->name }}</h2>
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xxs font-medium text-blue-700 ring-1 ring-inset ring-blue-750/10">
                            {{ $org->industry ?? 'Enterprise Client' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($org->website) <a href="{{ $org->website }}" target="_blank" class="underline text-gray-600 hover:text-gray-900">{{ $org->website }}</a> | @endif
                        @if($org->address) {{ $org->address }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button wire:click="openForm({{ $org->id }})" class="text-xs font-semibold text-gray-900 hover:underline">Edit</button>
                    <button onclick="confirm('Are you sure you want to remove this client organization?') || event.stopImmediatePropagation()" wire:click="delete({{ $org->id }})" class="text-xs font-semibold text-red-600 hover:underline">Remove</button>
                </div>
            </div>

            <!-- Representatives and Projects columns -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- COLUMN 1: Representatives -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Linked Portal Users</h3>
                        <p class="text-xxs text-gray-400">These users can log in to view research results and index metrics for this organization.</p>
                    </div>

                    <ul class="divide-y divide-gray-100 border border-gray-100 rounded-md bg-gray-50 px-4 py-2 text-sm">
                        @forelse($org->representatives as $rep)
                        <li class="flex justify-between items-center py-2.5">
                            <div>
                                <span class="font-medium text-gray-900">{{ $rep->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $rep->email }})</span>
                            </div>
                            <button wire:click="removeRepresentative({{ $rep->id }})" class="text-xs text-red-600 hover:underline font-semibold">Unlink</button>
                        </li>
                        @empty
                        <li class="py-2.5 text-xs text-gray-400 italic">No representative accounts linked.</li>
                        @endforelse
                    </ul>

                    <!-- Management forms (Tabbed style) -->
                    <div class="bg-gray-50 border border-gray-100 rounded-md p-4 space-y-4">
                        <!-- Form A: Link Existing -->
                        <div class="space-y-2">
                            <h4 class="text-xxs font-bold uppercase tracking-wider text-gray-500">Link Existing Client User</h4>
                            <form wire:submit.prevent="assignRepresentative({{ $org->id }})" class="flex gap-2 items-end">
                                <div class="flex-grow">
                                    <select wire:model="selectedRepId" required class="block w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                                        <option value="">Select Representative User</option>
                                        @foreach($availableRepresentatives as $rep)
                                            <option value="{{ $rep->id }}">{{ $rep->name }} ({{ $rep->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="rounded bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-gray-800 transition">
                                    Link
                                </button>
                            </form>
                        </div>

                        <!-- Form B: Create New -->
                        <div class="space-y-2 border-t border-gray-200 pt-3">
                            <h4 class="text-xxs font-bold uppercase tracking-wider text-gray-500">Create & Link New Representative</h4>
                            <form wire:submit.prevent="createAndLinkRepresentative({{ $org->id }})" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <input wire:model="newRepName" type="text" placeholder="Name" required class="block w-full rounded-md border border-gray-300 px-2 py-1 text-xs focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                                </div>
                                <div>
                                    <input wire:model="newRepEmail" type="email" placeholder="Email Address" required class="block w-full rounded-md border border-gray-300 px-2 py-1 text-xs focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                                </div>
                                <div class="sm:col-span-2 flex justify-end pt-1">
                                    <button type="submit" class="rounded bg-gray-950 px-3 py-1 text-xs font-semibold text-white shadow hover:bg-gray-850 transition">
                                        Create & Link User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: Projects Assignment -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Assigned Research Projects</h3>
                        <p class="text-xxs text-gray-400">Projects linked to this organization. Representatives can view their campaign answers.</p>
                    </div>

                    <ul class="divide-y divide-gray-100 border border-gray-100 rounded-md bg-gray-50 px-4 py-2 text-sm">
                        @forelse($org->projects as $project)
                        <li class="flex justify-between items-center py-2.5">
                            <div>
                                <span class="font-medium text-gray-900">{{ $project->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">(Quota: {{ $project->target_quota }} surveys)</span>
                            </div>
                            <button wire:click="unlinkProject({{ $project->id }})" class="text-xs text-red-600 hover:underline font-semibold">Unlink</button>
                        </li>
                        @empty
                        <li class="py-2.5 text-xs text-gray-400 italic">No projects assigned.</li>
                        @endforelse
                    </ul>

                    <!-- Assign Project Form -->
                    <div class="bg-gray-50 border border-gray-100 rounded-md p-4 space-y-2">
                        <h4 class="text-xxs font-bold uppercase tracking-wider text-gray-500">Assign Existing Project</h4>
                        <form wire:submit.prevent="assignProject({{ $org->id }})" class="flex gap-2 items-end">
                            <div class="flex-grow">
                                <select wire:model="selectedProjectId" required class="block w-full rounded-md border border-gray-300 px-3 py-1.5 text-xs focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 bg-white">
                                    <option value="">Select Project</option>
                                    @foreach($availableProjects as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="rounded bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-gray-800 transition">
                                Assign Project
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        @empty
        <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-sm text-gray-500 shadow-sm">
            No client organizations registered in directory. Click "Register Organization" to get started.
        </div>
        @endforelse
    </div>
</div>

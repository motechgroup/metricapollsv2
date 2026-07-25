@section('page_title', 'Political Party Manager')

<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-sans">Political Party Directory</h1>
            <p class="text-sm text-gray-600">Create and manage registered political parties and alliance coalitions linked to candidates.</p>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="rounded-md bg-emerald-50 p-4 border border-emerald-200 text-emerald-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Party Form Panel -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <h2 class="text-lg font-bold text-gray-900">{{ $editingPartyId ? 'Edit Party' : 'Add New Political Party' }}</h2>
            <form wire:submit.prevent="saveParty" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Party / Alliance Name *</label>
                    <input type="text" wire:model="name" placeholder="e.g. United Democratic Alliance" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-brand-blue focus:border-brand-blue">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Abbreviation / Acronym</label>
                    <input type="text" wire:model="abbreviation" placeholder="e.g. UDA" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-brand-blue focus:border-brand-blue">
                    @error('abbreviation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Brand Theme Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" wire:model="party_color" class="h-9 w-12 rounded border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" wire:model="party_color" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-mono focus:ring-brand-blue focus:border-brand-blue">
                    </div>
                    @error('party_color') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Party Logo / Symbol</label>
                    <input type="file" wire:model="logo" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    @error('logo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Description / Manifesto Focus</label>
                    <textarea wire:model="description" rows="3" placeholder="Brief manifesto or alliance description..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-brand-blue focus:border-brand-blue"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-brand-navy hover:bg-brand-navy/90 text-white font-semibold py-2 px-4 rounded-md text-sm transition">
                        {{ $editingPartyId ? 'Save Changes' : 'Create Party' }}
                    </button>
                    @if($editingPartyId)
                        <button type="button" wire:click="resetForm" class="bg-gray-200 text-gray-700 font-semibold py-2 px-3 rounded-md text-sm hover:bg-gray-300">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Party List Panel -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-gray-200">
                <input type="text" wire:model.live="search" placeholder="Search parties by name or acronym..." class="w-full max-w-xs rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-brand-blue focus:border-brand-blue">
                <span class="text-xs font-medium text-gray-500">{{ $parties->count() }} Parties Listed</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($parties as $party)
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-3 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1.5" style="background-color: {{ $party->party_color }};"></div>
                        <div class="flex items-start justify-between gap-3 pt-1">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center font-bold text-xs shrink-0" style="color: {{ $party->party_color }};">
                                    @if($party->logo_path)
                                        <img src="{{ asset($party->logo_path) }}" alt="{{ $party->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='/favicon.png';">
                                    @else
                                        {{ $party->abbreviation ?: substr($party->name, 0, 3) }}
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $party->name }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold" style="background-color: {{ $party->party_color }}20; color: {{ $party->party_color }};">
                                        {{ $party->abbreviation ?: 'PARTY' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($party->description)
                            <p class="text-xs text-gray-600 line-clamp-2">{{ $party->description }}</p>
                        @endif

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs text-gray-500">
                            <span>{{ $party->politicians_count }} Candidate(s) Linked</span>
                            <div class="flex gap-3 font-semibold">
                                <button wire:click="editParty({{ $party->id }})" class="text-brand-blue hover:underline">Edit</button>
                                <button wire:click="deleteParty({{ $party->id }})" wire:confirm="Are you sure you want to delete this party?" class="text-red-600 hover:underline">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500 text-sm">
                        No political parties found. Create one using the form.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

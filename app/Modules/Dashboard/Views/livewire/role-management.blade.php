@section('page_title', 'Roles & Permissions')

<div class="space-y-8">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Roles & Permissions</h1>
            <p class="text-sm text-gray-500">Configure role security access, view capabilities, and customize direct permissions.</p>
        </div>
        @if(!$isFormOpen)
        <button wire:click="openForm" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
            Add New Role
        </button>
        @endif
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

    <!-- Form Section -->
    @if($isFormOpen)
    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-6">{{ $roleId ? 'Edit Security Role' : 'Create Security Role' }}</h2>
        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                <input wire:model="name" type="text" required {{ in_array($name, $systemRoles) ? 'readonly' : '' }} class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900 {{ in_array($name, $systemRoles) ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : '' }}">
                @if(in_array($name, $systemRoles))
                    <span class="text-xs text-gray-400 mt-1 block">System roles cannot have their name changed.</span>
                @endif
                @error('name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">Select Associated Permissions</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                    <div class="flex items-start">
                        <div class="flex h-5 items-center">
                            <input wire:model="selectedPermissions" value="{{ $permission->name }}" type="checkbox" id="perm_{{ $permission->id }}" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="perm_{{ $permission->id }}" class="font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $permission->name) }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('selectedPermissions') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-4 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition">
                    Save Role
                </button>
                <button type="button" wire:click="closeForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Roles Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/4">Role Title</th>
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Authorized Permissions</th>
                    <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/6">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($roles as $role)
                <tr>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900">{{ $role->name }}</span>
                            @if(in_array($role->name, $systemRoles))
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xxs font-medium text-gray-500 ring-1 ring-inset ring-gray-500/10">System</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($role->permissions as $permission)
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xxs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 capitalize">
                                    {{ str_replace(' ', '_', $permission->name) }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">No permissions assigned.</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium space-x-3">
                        <button wire:click="openForm({{ $role->id }})" class="text-gray-900 hover:text-gray-600">Edit</button>
                        @if(!in_array($role->name, $systemRoles))
                        <button onclick="confirm('Are you sure you want to delete this role?') || event.stopImmediatePropagation()" wire:click="delete({{ $role->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

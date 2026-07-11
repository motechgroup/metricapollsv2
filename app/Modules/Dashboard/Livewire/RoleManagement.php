<?php

namespace App\Modules\Dashboard\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Title;

#[Title('Roles & Permissions - Metrica Polls')]
class RoleManagement extends Component
{
    public $roleId = null;
    public $name = '';
    public $selectedPermissions = [];

    public $isFormOpen = false;

    // Default system roles that cannot be deleted
    protected $systemRoles = ['Super Admin', 'Admin', 'Project Manager', 'Field Manager', 'Field Agent', 'Client', 'Panelist'];

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->isFormOpen = true;

        if ($id) {
            $role = Role::findOrFail($id);
            $this->roleId = $role->id;
            $this->name = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        } else {
            $this->roleId = null;
            $this->name = '';
            $this->selectedPermissions = [];
        }
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name,' . ($this->roleId ?? 'NULL'),
            'selectedPermissions' => 'nullable|array',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];

        $this->validate($rules);

        if ($this->roleId) {
            // Update Role
            $role = Role::findOrFail($this->roleId);
            $role->update(['name' => $this->name]);
            $role->syncPermissions($this->selectedPermissions);

            session()->flash('success', "Role '{$this->name}' updated successfully.");
        } else {
            // Create Role
            $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);
            if (!empty($this->selectedPermissions)) {
                $role->syncPermissions($this->selectedPermissions);
            }

            session()->flash('success', "New role '{$this->name}' created successfully.");
        }

        // Reset Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->closeForm();
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, $this->systemRoles)) {
            session()->flash('error', "System role '{$role->name}' is locked and cannot be deleted.");
            return;
        }

        $role->delete();

        // Reset Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        session()->flash('success', "Role '{$role->name}' deleted successfully.");
    }

    public function render()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('Dashboard::livewire.role-management', [
            'roles' => $roles,
            'permissions' => $permissions,
            'systemRoles' => $this->systemRoles,
        ])->layout('Dashboard::admin-layout');
    }
}

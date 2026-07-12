<?php

namespace App\Modules\Dashboard\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;

#[Title('User Management - Metrica Polls')]
class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = '';
    public $filterStatus = '';

    // Form fields
    public $userId = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $client_organization_id = '';
    public $password = '';
    public $status = 'active';
    public $selectedRole = '';

    public $isFormOpen = false;
    public $clientOrganizations = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->isFormOpen = true;
        
        // Fetch all client organizations for Client role assignment
        $this->clientOrganizations = \App\Modules\CRM\Models\ClientOrganization::orderBy('name')->get()->toArray();

        if ($id) {
            $user = User::findOrFail($id);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
            $this->client_organization_id = $user->client_organization_id ?? '';
            $this->status = $user->status;
            $this->selectedRole = $user->roles->first()->name ?? '';
            $this->password = '';
        } else {
            $this->userId = null;
            $this->name = '';
            $this->email = '';
            $this->phone = '';
            $this->client_organization_id = '';
            $this->password = '';
            $this->status = 'active';
            $this->selectedRole = 'Panelist';
        }
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->userId ?? 'NULL'),
            'phone' => 'nullable|string|max:30|unique:users,phone,' . ($this->userId ?? 'NULL'),
            'status' => 'required|in:active,suspended,pending',
            'selectedRole' => 'required|exists:roles,name',
            'client_organization_id' => 'required_if:selectedRole,Client',
        ];

        if (!$this->userId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => !empty($this->phone) ? $this->phone : null,
            'status' => $this->status,
            'client_organization_id' => ($this->selectedRole === 'Client' && !empty($this->client_organization_id)) ? $this->client_organization_id : null,
        ];

        if ($this->userId) {
            // Update User
            $user = User::findOrFail($this->userId);
            $user->update($userData);

            if (!empty($this->password)) {
                $user->update([
                    'password' => Hash::make($this->password)
                ]);
            }

            $user->syncRoles([$this->selectedRole]);

            // Ensure profile exists if user is role Panelist
            if ($this->selectedRole === 'Panelist') {
                \App\Modules\Wallet\Models\PanelistProfile::firstOrCreate([
                    'user_id' => $user->id,
                ], [
                    'points_balance' => 0,
                    'is_verified' => false,
                ]);
            }

            session()->flash('success', 'User profile updated successfully.');
        } else {
            // Create User
            $userData['password'] = Hash::make($this->password);
            $userData['email_verified_at'] = now();

            $user = User::create($userData);
            $user->assignRole($this->selectedRole);

            // Ensure profile exists if user is role Panelist
            if ($this->selectedRole === 'Panelist') {
                \App\Modules\Wallet\Models\PanelistProfile::firstOrCreate([
                    'user_id' => $user->id,
                ], [
                    'points_balance' => 0,
                    'is_verified' => false,
                ]);
            }

            session()->flash('success', 'New user registered successfully.');
        }

        $this->closeForm();
    }

    public function delete($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();

        session()->flash('success', 'User profile removed successfully.');
    }

    public function render()
    {
        $query = User::with(['roles', 'clientOrganization'])
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        if (!empty($this->filterRole)) {
            $query->role($this->filterRole);
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $users = $query->paginate(10);
        $roles = Role::all();

        return view('Dashboard::livewire.user-management', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('Dashboard::admin-layout');
    }
}

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
    public $password = '';
    public $status = 'active';
    public $selectedRole = '';

    public $isFormOpen = false;

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

        if ($id) {
            $user = User::findOrFail($id);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->status = $user->status;
            $this->selectedRole = $user->roles->first()->name ?? '';
            $this->password = '';
        } else {
            $this->userId = null;
            $this->name = '';
            $this->email = '';
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
            'status' => 'required|in:active,suspended,pending',
            'selectedRole' => 'required|exists:roles,name',
        ];

        if (!$this->userId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        if ($this->userId) {
            // Update User
            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
            ]);

            if (!empty($this->password)) {
                $user->update([
                    'password' => Hash::make($this->password)
                ]);
            }

            $user->syncRoles([$this->selectedRole]);
            session()->flash('success', 'User profile updated successfully.');
        } else {
            // Create User
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'status' => $this->status,
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($this->selectedRole);
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
        $query = User::with('roles')
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

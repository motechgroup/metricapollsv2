<?php

namespace App\Modules\Dashboard\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;

#[Title('My Profile - Metrica Polls')]
class ProfileSettings extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function save()
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30|unique:users,phone,' . $user->id,
        ];

        if (!empty($this->password)) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $this->validate($rules);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => !empty($this->phone) ? $this->phone : null,
        ]);

        if (!empty($this->password)) {
            $user->update([
                'password' => Hash::make($this->password)
            ]);
            $this->password = '';
            $this->password_confirmation = '';
        }

        session()->flash('success', 'Your profile settings have been updated successfully.');
    }

    public function render()
    {
        return view('Dashboard::livewire.profile-settings')
            ->layout('Dashboard::admin-layout');
    }
}

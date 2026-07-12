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

    // Security confirmation properties
    public $showCodeConfirmation = false;
    public $enteredCode = '';

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

        // Security check: if Name, Email, or Password changes, require code confirmation
        $isChangingName = $this->name !== $user->name;
        $isChangingEmail = $this->email !== $user->email;
        $isChangingPassword = !empty($this->password);

        if ($isChangingName || $isChangingEmail || $isChangingPassword) {
            $code = strval(rand(100000, 999999));

            session([
                'profile_confirmation_code' => $code,
                'profile_confirmation_expires' => now()->addMinutes(15),
                'pending_profile_update' => [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => !empty($this->phone) ? $this->phone : null,
                    'password' => !empty($this->password) ? Hash::make($this->password) : null,
                ]
            ]);

            // Dispatch verification code via custom configurable mail
            try {
                $subject = "Confirm Profile Changes - Metrica Polls";
                $body = "We received a request to update critical security information (username/name or password) on your Metrica Polls profile. Use the following 6-digit confirmation code to approve this update:\n\n**{$code}**\n\nIf you did not request this update, please ignore this email and secure your account.";
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\CustomConfigurableMail($subject, $body, [
                    'name' => $user->name,
                ]));

                logger("Profile update confirmation code for {$user->email} is: {$code}");
                session()->flash('info', "A 6-digit confirmation code has been sent to your email. (For demo purposes, the code is: {$code})");
            } catch (\Throwable $e) {
                logger("Profile email confirmation failure: " . $e->getMessage());
                session()->flash('info', "Security code generated. (For demo purposes, the code is: {$code})");
            }

            $this->showCodeConfirmation = true;
            return;
        }

        // Otherwise (only non-security fields like phone changed), save directly
        $user->update([
            'phone' => !empty($this->phone) ? $this->phone : null,
        ]);

        session()->flash('success', 'Your profile settings have been updated successfully.');
    }

    public function confirmSave()
    {
        $this->validate([
            'enteredCode' => 'required|string|size:6',
        ]);

        $sessionCode = session('profile_confirmation_code');
        $sessionExpires = session('profile_confirmation_expires');
        $pending = session('pending_profile_update');

        if (!$sessionCode || !$pending || now()->greaterThan($sessionExpires)) {
            $this->addError('enteredCode', 'The confirmation session has expired. Please try saving again.');
            return;
        }

        if ($this->enteredCode !== $sessionCode) {
            $this->addError('enteredCode', 'The confirmation code you entered is incorrect.');
            return;
        }

        $user = auth()->user();

        // Apply critical changes to the user database record
        $user->update([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'phone' => $pending['phone'],
        ]);

        if (!empty($pending['password'])) {
            $user->update([
                'password' => $pending['password']
            ]);
        }

        // Reset state and clear session variables
        session()->forget(['profile_confirmation_code', 'profile_confirmation_expires', 'pending_profile_update']);
        
        $this->showCodeConfirmation = false;
        $this->enteredCode = '';
        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('success', 'Your profile settings have been updated successfully.');
    }

    public function cancelConfirmation()
    {
        session()->forget(['profile_confirmation_code', 'profile_confirmation_expires', 'pending_profile_update']);
        $this->showCodeConfirmation = false;
        $this->enteredCode = '';
        
        // Revert form fields to original database state
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function render()
    {
        return view('Dashboard::livewire.profile-settings')
            ->layout('Dashboard::admin-layout');
    }
}

<?php

namespace App\Modules\Authentication\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;

#[Title('Create Account - Metrica Polls')]
class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function register()
    {
        $this->validate();

        $throttleKey = 'register:' . request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Too many registration requests. Please try again in ' . ceil($seconds / 60) . ' minutes.');
            return;
        }

        // Hit the rate limit
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 3600); // 1 hour decay

        // Generate 6-digit OTP code
        $otpCode = strval(rand(100000, 999999));
        $otpExpiresAt = now()->addMinutes(15);

        // Create the user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'status' => 'pending',
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt,
        ]);

        // Assign default Panelist role
        $user->assignRole('Panelist');

        // Log the OTP code (or simulate sending email)
        logger("OTP code for user {$user->email} is: {$otpCode}");
        session()->flash('info', "An OTP code has been generated. For demo purposes, use code: {$otpCode}");

        return redirect()->route('auth.verify-otp', ['email' => $user->email]);
    }

    public function loginWithGoogle($email, $name = 'Google User')
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Register as Panelist on the fly
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(rand(100000, 999999)),
                'status' => 'active',
            ]);
            $user->assignRole('Panelist');
            
            // Create Panelist Profile
            \App\Modules\Wallet\Models\PanelistProfile::create([
                'user_id' => $user->id,
                'points_balance' => 0,
                'is_verified' => false,
            ]);
        }

        if ($user->status === 'suspended') {
            $this->addError('email', 'This account has been suspended by an administrator.');
            return;
        }

        // Login instantly bypassing OTP
        auth()->login($user, true);

        return redirect()->route('dashboard.index');
    }

    public function render()
    {
        return view('Authentication::livewire.register')
            ->layout('Corporate::layout');
    }
}

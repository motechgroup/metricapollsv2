<?php

namespace App\Modules\Authentication\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;

#[Title('Forgot Password - Metrica Polls')]
class ForgotPassword extends Component
{
    public $email = '';

    protected $rules = [
        'email' => 'required|email|exists:users,email',
    ];

    public function sendResetLink()
    {
        $this->validate();

        $throttleKey = 'password-reset:' . request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Too many password reset requests. Please try again in ' . ceil($seconds / 60) . ' minutes.');
            return;
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 3600); // 1 hour decay

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            [
                'token' => bcrypt($token),
                'created_at' => now()
            ]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $this->email]);

        logger("Password reset link for {$this->email}: {$resetLink}");
        session()->flash('success', "A password reset link has been generated. For demo purposes: {$resetLink}");
    }

    public function render()
    {
        return view('Authentication::livewire.forgot-password')
            ->layout('Corporate::layout');
    }
}

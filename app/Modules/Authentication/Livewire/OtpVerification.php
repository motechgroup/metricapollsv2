<?php

namespace App\Modules\Authentication\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Verify OTP - Metrica Polls')]
class OtpVerification extends Component
{
    #[Url]
    public $email = '';

    #[Url]
    public $remember = 0;

    public $otp = '';

    protected $rules = [
        'email' => 'required|email',
        'otp' => 'required|string|len:6', // Custom validation or simple check
    ];

    public function mount()
    {
        if (empty($this->email)) {
            return redirect()->route('login');
        }
    }

    public function verify()
    {
        $this->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $throttleKey = 'otp:' . request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('otp', 'Too many OTP verification attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.');
            return;
        }

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 3600);
            $this->addError('otp', 'User account not found.');
            return;
        }

        if (empty($user->otp_code) || $user->otp_code !== $this->otp) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 3600);
            $this->addError('otp', 'The provided OTP code is invalid.');
            return;
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 3600);
            $this->addError('otp', 'This OTP code has expired. Please request a new one.');
            return;
        }

        // Clear OTP rate limit on successful verification
        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        // Clear OTP code
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'status' => 'active', // Set active on successful verification
        ]);

        // Login user
        Auth::login($user, $this->remember == 1);

        // Clear session flash
        session()->forget('info');

        // Redirect to dashboard
        return redirect()->route('dashboard.index');
    }

    public function resend()
    {
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Account not found.');
            return;
        }

        // Rate limiting checks are handled by the route throttle middleware,
        // but we generate a new OTP
        $otpCode = strval(rand(100000, 999999));
        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        logger("Resent OTP code for user {$user->email} is: {$otpCode}");
        session()->flash('info', "A new OTP code has been generated. Use code: {$otpCode}");
    }

    public function render()
    {
        return view('Authentication::livewire.otp-verification')
            ->layout('Corporate::layout');
    }
}

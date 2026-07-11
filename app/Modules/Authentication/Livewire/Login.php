<?php

namespace App\Modules\Authentication\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;

#[Title('Sign In - Metrica Polls')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $isAdminLogin = false;
    public $login_google_enabled = true;
    public $login_email_enabled = true;
    public $login_sms_enabled = true;
    public $phone = '';

    protected $rules = [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ];

    public function mount()
    {
        if (request()->routeIs('admin.login') || request()->is('admin/login') || request()->is('admin/login/*')) {
            $this->isAdminLogin = true;
        }
        $this->login_google_enabled = \App\Models\Setting::getValue('login_google_enabled', '1') === '1';
        $this->login_email_enabled = \App\Models\Setting::getValue('login_email_enabled', '1') === '1';
        $this->login_sms_enabled = \App\Models\Setting::getValue('login_sms_enabled', '1') === '1';
    }

    public function login()
    {
        // Enforce active login type settings for public portal
        if (!$this->isAdminLogin && !$this->login_email_enabled) {
            $this->addError('email', 'Email login is currently disabled. Please use an active sign in method.');
            return;
        }

        $this->validate();

        $throttleKey = 'login:' . request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.');
            return;
        }

        $user = User::where('email', $this->email)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 900); // 15 mins decay
            $this->addError('email', __('auth.failed'));
            return;
        }

        if ($user->status === 'suspended') {
            $this->addError('email', 'This account has been suspended by an administrator.');
            return;
        }

        // Clear rate limit on successful credentials match
        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        // Staff login route: bypass OTP and log in directly
        if ($this->isAdminLogin) {
            if ($user->hasAnyRole(['Super Admin', 'Admin', 'Manager'])) {
                auth()->login($user, $this->remember);
                return redirect()->route('dashboard.index');
            }

            $this->addError('email', 'Access denied. Only administrators and staff members can access this workspace.');
            return;
        }

        // Regular login route: enforce email login is enabled for panelists
        if ($user->hasRole('Panelist') && !$this->login_email_enabled) {
            $this->addError('email', 'Email login is currently disabled.');
            return;
        }

        // Generate 6-digit OTP code for secure login/2FA verification (fallback)
        $otpCode = strval(rand(100000, 999999));
        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        logger("Login OTP code for user {$user->email} is: {$otpCode}");
        session()->flash('info', "Secure login OTP generated. For demo purposes, use code: {$otpCode}");

        return redirect()->route('auth.verify-otp', [
            'email' => $user->email,
            'remember' => $this->remember ? 1 : 0
        ]);
    }

    public function loginWithPhone()
    {
        $this->validate(['phone' => 'required|string']);

        // Check if SMS login is enabled
        if (!$this->isAdminLogin && !$this->login_sms_enabled) {
            $this->addError('phone', 'SMS OTP sign in is currently disabled.');
            return;
        }

        $throttleKey = 'login-phone:' . request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('phone', 'Too many attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.');
            return;
        }

        $user = User::where('phone', $this->phone)->first();

        if (!$user) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 900);
            $this->addError('phone', 'No registered account found with this phone number.');
            return;
        }

        if ($user->status === 'suspended') {
            $this->addError('phone', 'This account has been suspended by an administrator.');
            return;
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        // Generate 6-digit OTP code for secure login/2FA verification
        $otpCode = strval(rand(100000, 999999));
        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        try {
            $smsMsg = "Your Metrica Polls verification code is: {$otpCode}. Expires in 15 minutes.";
            \App\Services\TextSmsService::send($user->phone, $smsMsg);
        } catch (\Throwable $e) {
            logger("SMS dispatch failure: " . $e->getMessage());
        }

        logger("Phone login OTP code for user {$user->phone} is: {$otpCode}");
        session()->flash('info', "Secure login OTP generated. For demo purposes, use code: {$otpCode}");

        return redirect()->route('auth.verify-otp', [
            'email' => $user->email,
            'remember' => $this->remember ? 1 : 0
        ]);
    }

    public function loginWithGoogle($email, $name = 'Google User')
    {
        if (!$this->login_google_enabled) {
            $this->addError('email', 'Google sign in is currently disabled by the administrator.');
            return;
        }
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

            // Send notifications
            try {
                $subject = \App\Models\Setting::getValue('mail_template_welcome_subject', 'Welcome to Metrica Polls Panel!');
                $body = \App\Models\Setting::getValue('mail_template_welcome_body', '');
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\CustomConfigurableMail($subject, $body, [
                    'name' => $user->name,
                ]));

                $smsTemplate = "Welcome to Metrica Polls, {name}! Please undertake training qualification tests to unlock surveys.";
                $smsMsg = str_replace('{name}', $user->name, $smsTemplate);
                \App\Services\TextSmsService::send($user->phone ?? '254700000000', $smsMsg);
            } catch (\Throwable $e) {
                logger("Welcome notification failure: " . $e->getMessage());
            }
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
        return view('Authentication::livewire.login')
            ->layout('Corporate::layout');
    }
}

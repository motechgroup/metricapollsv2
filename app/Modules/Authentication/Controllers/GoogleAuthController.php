<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Modules\Wallet\Models\PanelistProfile;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $enabled = Setting::getValue('login_google_enabled', '1') === '1';
        $clientId = Setting::getValue('google_client_id', '');

        if (!$enabled || empty($clientId)) {
            return redirect()->route('login')->with('error', 'Google Sign In is not configured or disabled by the administrator.');
        }

        // Dynamically override config values at redirect time to guarantee they are loaded
        config([
            'services.google.client_id' => Setting::getValue('google_client_id'),
            'services.google.client_secret' => Setting::getValue('google_client_secret'),
            'services.google.redirect' => Setting::getValue('google_redirect_url'),
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        // Dynamically override config values at callback time to guarantee they are loaded
        config([
            'services.google.client_id' => Setting::getValue('google_client_id'),
            'services.google.client_secret' => Setting::getValue('google_client_secret'),
            'services.google.redirect' => Setting::getValue('google_redirect_url'),
        ]);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google Authentication failed: ' . $e->getMessage());
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Register new Panelist
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Google User',
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(strval(rand(100000, 999999))),
                'status' => 'active',
            ]);
            $user->assignRole('Panelist');

            PanelistProfile::create([
                'user_id' => $user->id,
                'points_balance' => 0,
                'is_verified' => false,
            ]);

            // Send welcome notifications
            try {
                $subject = Setting::getValue('mail_template_welcome_subject', 'Welcome to Metrica Polls Panel!');
                $body = Setting::getValue('mail_template_welcome_body', '');
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\CustomConfigurableMail($subject, $body, [
                    'name' => $user->name,
                ]));
            } catch (\Throwable $e) {
                logger("Welcome notification failure: " . $e->getMessage());
            }
        }

        if ($user->status === 'suspended') {
            return redirect()->route('login')->with('error', 'This account has been suspended by an administrator.');
        }

        auth()->login($user, true);

        return redirect()->route('dashboard.index');
    }
}

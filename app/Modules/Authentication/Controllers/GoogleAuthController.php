<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Wallet\Models\PanelistProfile;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $enabled = Setting::getValue('login_google_enabled', '1') === '1';
        $clientId = Setting::getValue('google_client_id', '');
        $redirectUrl = Setting::getValue('google_redirect_url', '');

        if (!$enabled || empty($clientId) || empty($redirectUrl)) {
            return redirect()->route('login')->with('error', 'Google Sign In is not configured or is disabled by the administrator.');
        }

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUrl,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => csrf_token(),
        ]);

        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback()
    {
        $enabled = Setting::getValue('login_google_enabled', '1') === '1';
        $clientId = Setting::getValue('google_client_id', '');
        $clientSecret = Setting::getValue('google_client_secret', '');
        $redirectUrl = Setting::getValue('google_redirect_url', '');

        if (!$enabled || empty($clientId) || empty($clientSecret) || empty($redirectUrl)) {
            return redirect()->route('login')->with('error', 'Google Sign In is not configured.');
        }

        $code = request('code');

        if (!$code) {
            return redirect()->route('login')->with('error', 'Google authentication canceled or code missing.');
        }

        try {
            // Exchange authorization code for Access Token natively via HTTP POST
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUrl,
                'grant_type' => 'authorization_code',
            ]);

            if (!$response->successful()) {
                logger("Google Token Exchange failure: " . $response->body());
                return redirect()->route('login')->with('error', 'Failed to retrieve access token from Google.');
            }

            $data = $response->json();
            $accessToken = $data['access_token'] ?? null;

            if (!$accessToken) {
                return redirect()->route('login')->with('error', 'Google token response structure invalid.');
            }

            // Fetch Google user profile info natively
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (!$userResponse->successful()) {
                logger("Google UserInfo request failure: " . $userResponse->body());
                return redirect()->route('login')->with('error', 'Failed to fetch user information from Google.');
            }

            $userData = $userResponse->json();
            $email = $userData['email'] ?? null;
            $name = $userData['name'] ?? 'Google User';

            if (!$email) {
                return redirect()->route('login')->with('error', 'Could not obtain email address from Google account.');
            }
        } catch (\Throwable $e) {
            logger("Google OAuth exception occurred: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'OAuth error: ' . $e->getMessage());
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            // Register new Panelist
            $user = User::create([
                'name' => $name,
                'email' => $email,
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

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom Rate Limiters
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(15, 5)->by($request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        RateLimiter::for('otp', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Bind dynamic configurations from settings database
        try {
            if (class_exists(\App\Models\Setting::class) && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $host = \App\Models\Setting::getValue('mail_host');

                // Clear broken OpenSSL cafile paths dynamically to prevent connection crashes on shared hosting
                @ini_set('openssl.cafile', '');
                @ini_set('openssl.capath', '');

                config([
                    'mail.default' => !empty($host) ? 'smtp' : config('mail.default'),
                    'mail.mailers.smtp.host' => $host ?: config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => \App\Models\Setting::getValue('mail_port', config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.username' => \App\Models\Setting::getValue('mail_username', config('mail.mailers.smtp.username')),
                    'mail.mailers.smtp.password' => \App\Models\Setting::getValue('mail_password', config('mail.mailers.smtp.password')),
                    'mail.mailers.smtp.encryption' => \App\Models\Setting::getValue('mail_encryption', config('mail.mailers.smtp.encryption')),
                    'mail.from.address' => \App\Models\Setting::getValue('mail_from_address', config('mail.from.address', 'noreply@metricapolls.com')),
                    'mail.from.name' => \App\Models\Setting::getValue('mail_from_name', config('mail.from.name', 'Metrica Polls')),

                    // Google OAuth credentials
                    'services.google.client_id' => \App\Models\Setting::getValue('google_client_id', config('services.google.client_id')),
                    'services.google.client_secret' => \App\Models\Setting::getValue('google_client_secret', config('services.google.client_secret')),
                    'services.google.redirect' => \App\Models\Setting::getValue('google_redirect_url', config('services.google.redirect')),
                ]);
            }
        } catch (\Throwable $e) {
            // Ignore database table missing or unit testing exceptions
        }
    }
}

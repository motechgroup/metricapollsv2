<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoLocationService
{
    public static function getCountryFromIp($ip)
    {
        // Fallback for local development or private ranges
        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            // Check if there is a mocked country in the session for testing
            return session('mock_geo_country', 'Kenya');
        }

        return Cache::remember('geoip_' . $ip, 86400, function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['status']) && $data['status'] === 'success') {
                        $country = $data['country'] ?? 'Kenya';
                        
                        // Check if it's one of the 5 allowed countries, else flag
                        $allowed = ['Kenya', 'Rwanda', 'Tanzania', 'Uganda', 'Nigeria'];
                        if (in_array($country, $allowed)) {
                            return $country;
                        }
                    }
                }
            } catch (\Throwable $e) {
                logger("GeoIP API error: " . $e->getMessage());
            }

            return 'Kenya';
        });
    }

    public static function isAllowedCountry($country)
    {
        return in_array($country, ['Kenya', 'Rwanda', 'Tanzania', 'Uganda', 'Nigeria']);
    }

    public static function getCurrencyForCountry($country)
    {
        $map = [
            'Kenya' => ['code' => 'KES', 'symbol' => 'KES', 'rate' => 100], // 100 pts = 100 KES
            'Rwanda' => ['code' => 'RWF', 'symbol' => 'FRw', 'rate' => 1250], // 100 pts = 1,250 RWF
            'Tanzania' => ['code' => 'TZS', 'symbol' => 'TSh', 'rate' => 2600], // 100 pts = 2,600 TZS
            'Uganda' => ['code' => 'UGX', 'symbol' => 'USh', 'rate' => 3700], // 100 pts = 3,700 UGX
            'Nigeria' => ['code' => 'NGN', 'symbol' => '₦', 'rate' => 1500], // 100 pts = 1,500 NGN
        ];

        return $map[$country] ?? $map['Kenya'];
    }

    public static function getPayoutMethodsForCountry($country)
    {
        if ($country === 'Nigeria') {
            return [
                'bank_transfer' => 'Bank Transfer (NGN)',
                'airtime' => 'Airtime Top-up'
            ];
        }

        return [
            'mobile_money' => 'Mobile Money (MTN / Airtel / M-Pesa)',
            'airtime' => 'Airtime Top-up'
        ];
    }
}

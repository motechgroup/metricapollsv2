<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class TextSmsService
{
    /**
     * Send SMS via textsms.co.ke API gateway.
     *
     * @param string $mobile Phone number in format 2547XXXXXXXX or 07XXXXXXXX
     * @param string $message The message content
     * @return array
     */
    public static function send($mobile, $message)
    {
        // Format phone number to international format 254...
        $mobile = self::formatMobile($mobile);

        $url = Setting::getValue('sms_gateway_url', 'https://sms.textsms.co.ke/api/services/sendsms/');
        $apiKey = Setting::getValue('sms_api_key', '');
        $partnerId = Setting::getValue('sms_partner_id', '');
        $senderId = Setting::getValue('sms_sender_id', 'METRICA');

        // Log outgoing SMS in logs
        Log::info("TextSMS Outgoing: To: {$mobile}, Msg: {$message}");

        // If in unit tests or no credentials are set, simulate success
        if (app()->runningUnitTests() || empty($apiKey) || empty($partnerId) || str_contains($apiKey, 'mock')) {
            return [
                'status' => 'simulated_success',
                'response-code' => 200,
                'messageid' => 'sim_' . uniqid(),
            ];
        }

        try {
            $response = Http::post($url, [
                'apikey' => $apiKey,
                'partnerID' => $partnerId,
                'shortcode' => $senderId,
                'mobile' => $mobile,
                'message' => $message,
            ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error("TextSMS gateway failure: " . $e->getMessage());
            return [
                'status' => 'failed',
                'response-code' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Normalize phone number to 254... format.
     */
    private static function formatMobile($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '254' . $phone;
        }

        return $phone;
    }
}

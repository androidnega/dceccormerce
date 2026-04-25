<?php

namespace App\Services;

use App\Support\GhanaPhone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function sendSms(string $phone, string $message): bool
    {
        try {
            $e164 = GhanaPhone::toE164($phone);
            if ($e164 === null) {
                Log::warning('SMS skipped: could not normalize phone number.', ['input' => $phone]);

                return false;
            }

            $provider = sms_provider_resolved();

            return match ($provider) {
                'arkesel' => $this->sendViaArkesel($e164, $message),
                'hubtel' => $this->sendViaHubtel($e164, $message),
                'log' => $this->sendViaLog($e164, $message),
                default => $this->sendViaLog($e164, $message),
            };
        } catch (\Throwable $e) {
            Log::error('SMS send failed.', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function isReady(): bool
    {
        return match (sms_provider_resolved()) {
            'arkesel' => sms_api_key_resolved() !== '' && trim(sms_sender_resolved()) !== '',
            'hubtel' => (string) config('sms.hubtel.client_id') !== '' && (string) config('sms.hubtel.client_secret') !== '',
            'log' => true,
            default => true,
        };
    }

    private function sendViaLog(string $e164, string $message): bool
    {
        Log::channel('single')->info('[SMS / log]', [
            'to' => $e164,
            'body' => $message,
        ]);

        return true;
    }

    private function sendViaArkesel(string $e164, string $message): bool
    {
        $key = sms_api_key_resolved();
        if ($key === '') {
            Log::warning('SMS (Arkesel): API key is empty (admin or SMS_API_KEY).');

            return false;
        }

        $to = substr($e164, 1);
        $response = Http::timeout(20)
            ->acceptJson()
            ->get(sms_arkesel_url_resolved(), [
                'action' => 'send-sms',
                'api_key' => $key,
                'to' => $to,
                'from' => sms_sender_resolved(),
                'sms' => $message,
            ]);

        if (! $response->successful()) {
            Log::warning('SMS (Arkesel): HTTP error.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }

    private function sendViaHubtel(string $e164, string $message): bool
    {
        $id = (string) config('sms.hubtel.client_id');
        $secret = (string) config('sms.hubtel.client_secret');
        if ($id === '' || $secret === '') {
            Log::warning('SMS (Hubtel): client id/secret missing.');

            return false;
        }

        $response = Http::timeout(20)
            ->withBasicAuth($id, $secret)
            ->acceptJson()
            ->post((string) config('sms.hubtel.send_url'), [
                'From' => sms_sender_resolved(),
                'To' => $e164,
                'Content' => $message,
                'RegisteredDelivery' => true,
            ]);

        if (! $response->successful()) {
            Log::warning('SMS (Hubtel): HTTP error.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}

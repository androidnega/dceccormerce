<?php

namespace App\Services;

use App\Support\GhanaPhone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendWhatsApp(string $phone, string $message): bool
    {
        try {
            if (! config('whatsapp.enabled')) {
                return false;
            }

            $sid = (string) config('whatsapp.account_sid');
            $token = (string) config('whatsapp.auth_token');
            $from = (string) config('whatsapp.from');

            if ($sid === '' || $token === '' || $from === '') {
                return false;
            }

            $e164 = GhanaPhone::toE164($phone);
            if ($e164 === null) {
                Log::warning('WhatsApp skipped: could not normalize phone number.', ['input' => $phone]);

                return false;
            }

            $to = 'whatsapp:'.$e164;
            $fromWa = str_starts_with($from, 'whatsapp:') ? $from : 'whatsapp:'.$from;

            $url = sprintf(
                'https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json',
                $sid
            );

            $response = Http::timeout(25)
                ->withBasicAuth($sid, $token)
                ->asForm()
                ->post($url, [
                    'From' => $fromWa,
                    'To' => $to,
                    'Body' => $message,
                ]);

            if (! $response->successful()) {
                Log::warning('WhatsApp (Twilio): HTTP error.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed.', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function isReady(): bool
    {
        if (! config('whatsapp.enabled')) {
            return false;
        }

        return (string) config('whatsapp.account_sid') !== ''
            && (string) config('whatsapp.auth_token') !== ''
            && (string) config('whatsapp.from') !== '';
    }
}

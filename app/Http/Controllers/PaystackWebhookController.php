<?php

namespace App\Http\Controllers;

use App\Services\PaystackFinalizeService;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        private readonly PaystackService $paystack,
        private readonly PaystackFinalizeService $finalize,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (! $this->paystack->verifyWebhookSignature($payload, $signature)) {
            Log::warning('paystack_webhook_bad_signature');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $decoded = json_decode($payload, true);
        if (! is_array($decoded)) {
            Log::warning('paystack_webhook_invalid_json');

            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $event = (string) ($decoded['event'] ?? '');
        $data = is_array($decoded['data'] ?? null) ? $decoded['data'] : [];
        $reference = (string) ($data['reference'] ?? '');
        $amount = $data['amount'] ?? null;

        Log::info('paystack_webhook_event', [
            'event' => $event,
            'reference' => $reference,
            'amount' => $amount,
        ]);

        if ($event !== 'charge.success') {
            return response()->json(['message' => 'OK'], 200);
        }

        if ($reference === '') {
            Log::warning('paystack_webhook_finalize_failed', ['error' => 'no_reference']);

            return response()->json(['message' => 'Missing reference'], 400);
        }

        $result = $this->finalize->finalizePaidOrder($reference, true);
        if ($result['ok'] && $result['order'] !== null) {
            return response()->json(['message' => 'OK'], 200);
        }

        $err = (string) ($result['error'] ?? 'unknown');
        Log::error('paystack_webhook_finalize_failed', [
            'error' => $err,
            'reference' => $reference,
        ]);

        $status = match ($err) {
            'no_pending', 'verify_failed', 'bad_payload', 'empty_reference' => 400,
            'exception' => 500,
            default => 500,
        };

        return response()->json(['message' => 'Finalize failed'], $status);
    }
}

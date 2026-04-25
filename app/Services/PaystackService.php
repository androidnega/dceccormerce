<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackService
{
    private const BASE_URL = 'https://api.paystack.co';

    /**
     * GHS amount to smallest unit (pesewas). Paystack for Ghana uses pesewas.
     */
    public function amountToPesewas(float $ghs): int
    {
        return (int) round($ghs * 100.0);
    }

    /**
     * @return array{reference: string, amount_pesewas: int, authorization_url: string}
     */
    public function beginCheckout(
        string $email,
        int $amountPesewas,
        string $callbackUrl,
        array $metadata = []
    ): array {
        if ($amountPesewas < 1) {
            throw new \InvalidArgumentException('Amount is too small.');
        }

        $secret = paystack_secret_key();
        if ($secret === '') {
            throw new \RuntimeException('Paystack is not configured.');
        }

        $reference = 'DCA-'.strtoupper(str_replace('-', '', (string) Str::uuid()));

        $body = [
            'email' => $email,
            'amount' => $amountPesewas,
            'currency' => 'GHS',
            'callback_url' => $callbackUrl,
            'reference' => $reference,
            'metadata' => array_merge($metadata, [
                'source' => 'dcapple_checkout',
            ]),
        ];

        $response = Http::timeout(30)
            ->withToken($secret)
            ->acceptJson()
            ->post(self::BASE_URL.'/transaction/initialize', $body);

        if (! $response->successful()) {
            Log::warning('Paystack initialize failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $response->throw();
        }

        $data = $response->json('data');
        if (! is_array($data)) {
            throw new \RuntimeException('Invalid Paystack response.');
        }

        $authUrl = (string) ($data['authorization_url'] ?? '');
        if ($authUrl === '') {
            throw new \RuntimeException('Paystack did not return an authorization URL.');
        }

        return [
            'reference' => $reference,
            'amount_pesewas' => $amountPesewas,
            'authorization_url' => $authUrl,
        ];
    }

    /**
     * @return null|array{status: string, amount: int, currency: string, customer: mixed, metadata: mixed}
     */
    public function verifyReference(string $reference): ?array
    {
        $reference = trim($reference);
        if ($reference === '') {
            return null;
        }

        $secret = paystack_secret_key();
        if ($secret === '') {
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->withToken($secret)
                ->acceptJson()
                ->get(self::BASE_URL.'/transaction/verify/'.rawurlencode($reference));
        } catch (RequestException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json('data');
        if (! is_array($data)) {
            return null;
        }

        $status = (string) ($data['status'] ?? '');
        if ($status !== 'success') {
            return null;
        }

        $amount = (int) ($data['amount'] ?? 0);
        if ($amount < 1) {
            return null;
        }

        return [
            'status' => $status,
            'amount' => $amount,
            'currency' => (string) ($data['currency'] ?? 'GHS'),
            'customer' => $data['customer'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ];
    }

    /**
     * Full (or exact-amount) refund in GHS. Amount is in pesewas, matching Paystack charges.
     *
     * @return null|array{paystack_refund_id: int|string, raw: array}
     */
    public function createRefund(string $transactionReference, int $amountPesewas, string $merchantNote = 'Automatic refund: order could not be fulfilled.'): ?array
    {
        $transactionReference = trim($transactionReference);
        if ($transactionReference === '' || $amountPesewas < 1) {
            return null;
        }

        $secret = paystack_secret_key();
        if ($secret === '') {
            return null;
        }

        $body = [
            'transaction' => $transactionReference,
            'amount' => $amountPesewas,
            'currency' => 'GHS',
            'merchant_note' => $merchantNote,
        ];

        $response = Http::timeout(45)
            ->withToken($secret)
            ->acceptJson()
            ->post(self::BASE_URL.'/refund', $body);

        if (! $response->successful()) {
            Log::warning('Paystack refund request failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $data = $response->json('data');
        if (! is_array($data)) {
            return null;
        }

        $id = $data['id'] ?? null;
        if ($id === null) {
            return null;
        }

        return [
            'paystack_refund_id' => (string) $id,
            'raw' => $data,
        ];
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if ($signature === null || trim($signature) === '') {
            return false;
        }

        $secret = paystack_secret_key();
        if ($secret === '') {
            return false;
        }

        $computed = hash_hmac('sha512', $payload, $secret);

        return hash_equals($computed, $signature);
    }
}

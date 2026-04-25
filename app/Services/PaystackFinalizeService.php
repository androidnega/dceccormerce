<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaystackPendingCheckout;
use Illuminate\Support\Facades\Log;

class PaystackFinalizeService
{
    public function __construct(
        private readonly PaystackService $paystack,
        private readonly OrderPersistenceService $orderPersistence,
        private readonly OrderNotificationService $orderNotifications,
    ) {}

    /**
     * Create the paid order if not already created (Paystack callback or webhook).
     *
     * @return array{ok: bool, order: ?Order, error: ?string}
     */
    public function finalizePaidOrder(string $reference, bool $notify): array
    {
        $reference = trim($reference);
        if ($reference === '') {
            return ['ok' => false, 'order' => null, 'error' => 'empty_reference'];
        }

        $existing = Order::query()->where('paystack_reference', $reference)->first();
        if ($existing !== null) {
            PaystackPendingCheckout::query()
                ->where('reference', $reference)
                ->whereNull('processed_at')
                ->update([
                    'processed_at' => now(),
                    'order_id' => $existing->id,
                ]);
            Log::info('paystack_finalize_duplicate', ['reference' => $reference, 'order_id' => $existing->id]);

            return ['ok' => true, 'order' => $existing, 'error' => null];
        }

        $pending = PaystackPendingCheckout::query()
            ->where('reference', $reference)
            ->whereNull('processed_at')
            ->first();

        if ($pending === null) {
            Log::warning('paystack_finalize_no_pending', ['reference' => $reference]);

            return ['ok' => false, 'order' => null, 'error' => 'no_pending'];
        }

        $verified = $this->paystack->verifyReference($reference);
        if ($verified === null || (int) ($verified['amount'] ?? 0) !== (int) $pending->expected_amount_pesewas) {
            Log::warning('paystack_finalize_verify_failed', ['reference' => $reference]);

            return ['ok' => false, 'order' => null, 'error' => 'verify_failed'];
        }

        $validated = is_array($pending->validated_payload) ? $pending->validated_payload : [];
        $cart = is_array($pending->cart_payload) ? $pending->cart_payload : [];

        if ($validated === [] || $cart === []) {
            Log::warning('paystack_finalize_bad_payload', ['reference' => $reference]);

            return ['ok' => false, 'order' => null, 'error' => 'bad_payload'];
        }

        try {
            $order = $this->orderPersistence->persist(
                $validated,
                $cart,
                $pending->user_id,
                'paid',
                $reference,
                true
            );
        } catch (\Throwable $e) {
            Log::error('paystack_finalize_exception', ['reference' => $reference, 'message' => $e->getMessage()]);

            return ['ok' => false, 'order' => null, 'error' => 'exception'];
        }

        $pending->forceFill([
            'processed_at' => now(),
            'order_id' => $order->id,
        ])->save();

        if ($notify) {
            try {
                $this->orderNotifications->notifyOrderPlaced($order->fresh(['address']));
            } catch (\Throwable) {
                //
            }
        }

        return ['ok' => true, 'order' => $order, 'error' => null];
    }
}

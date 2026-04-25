<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderPaystackRefundService
{
    public function __construct(
        private readonly PaystackService $paystack,
    ) {}

    /**
     * When delivery is marked failed, automatically refund Paystack (Mobile Money / card) payments.
     * Idempotent: only runs when {@see Order::refund_status} is "none".
     */
    public function autoRefundIfPaystackPaidOrderFailed(Order $order): void
    {
        $order->refresh();

        if ($order->delivery_status !== 'failed') {
            return;
        }

        if ($order->payment_method !== 'momo' || $order->payment_status !== 'paid') {
            return;
        }

        $ref = (string) ($order->paystack_reference ?? '');
        if ($ref === '') {
            return;
        }

        if (($order->refund_status ?? 'none') !== 'none') {
            return;
        }

        if (! paystack_ready()) {
            Log::warning('Auto refund skipped: Paystack not configured.', ['order_id' => $order->id]);

            return;
        }

        $pesewas = $this->paystack->amountToPesewas((float) $order->total_amount);
        if ($pesewas < 1) {
            return;
        }

        $order->update(['refund_status' => 'processing']);

        $result = $this->paystack->createRefund(
            $ref,
            $pesewas,
            'Automatic full refund: delivery could not be completed (order '.$order->order_number.').',
        );

        if ($result === null) {
            $order->update(['refund_status' => 'failed']);
            Log::error('Paystack auto refund failed.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'paystack_reference' => $ref,
            ]);

            return;
        }

        $order->update([
            'refund_status' => 'completed',
            'paystack_refund_id' => $result['paystack_refund_id'],
            'refunded_at' => now(),
            'payment_status' => 'refunded',
        ]);
    }
}

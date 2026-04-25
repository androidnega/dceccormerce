<?php

namespace App\Services;

use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\Rider;
use Illuminate\Support\Facades\DB;

class OrderDeliveryService
{
    public function __construct(
        private readonly OrderNotificationService $orderNotifications,
        private readonly OrderPaystackRefundService $orderPaystackRefund,
    ) {}

    /**
     * @return array{ok: bool, error?: string}
     */
    public function markOnTheWay(Order $order): array
    {
        if (! $order->rider_id && ! $order->delivery_agent_id) {
            return ['ok' => false, 'error' => 'Assign a delivery agent before moving order to on_the_way.'];
        }

        if (! $order->canTransitionTo('on_the_way')) {
            return ['ok' => false, 'error' => 'Order cannot be moved to on_the_way from its current delivery status.'];
        }

        $order->update([
            'status' => 'on_the_way',
            'delivery_status' => 'on_the_way',
        ]);

        try {
            $this->orderNotifications->notifyOnTheWay($order->fresh(['address']));
        } catch (\Throwable) {
            //
        }

        return ['ok' => true];
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function markDelivered(Order $order): array
    {
        if (! $order->canTransitionTo('delivered')) {
            return ['ok' => false, 'error' => 'Order cannot be delivered from its current delivery status.'];
        }

        DB::transaction(function () use ($order): void {
            $payload = [
                'status' => 'delivered',
                'delivery_status' => 'delivered',
            ];

            if ($order->payment_method === 'cod') {
                $payload['payment_status'] = 'paid';
            }

            $order->update($payload);

            $this->releaseRiderForOrder($order);
            $this->releaseDeliveryAgentForOrder($order);
        });

        try {
            $this->orderNotifications->notifyDelivered($order->fresh(['address']));
        } catch (\Throwable) {
            //
        }

        return ['ok' => true];
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function markFailed(Order $order): array
    {
        if (! $order->canTransitionTo('failed')) {
            return ['ok' => false, 'error' => 'Order cannot be marked as failed from its current delivery status.'];
        }

        DB::transaction(function () use ($order): void {
            $previous = $order->delivery_status;

            $order->update([
                'failed_previous_status' => $previous,
                'status' => 'failed',
                'delivery_status' => 'failed',
            ]);

            $this->releaseRiderForOrder($order);
            $this->releaseDeliveryAgentForOrder($order);
        });

        $order->refresh();
        $this->orderPaystackRefund->autoRefundIfPaystackPaidOrderFailed($order);

        return ['ok' => true];
    }

    public function releaseRiderForOrder(Order $order): void
    {
        if (! $order->rider_id) {
            return;
        }

        Rider::query()->whereKey($order->rider_id)->update(['is_available' => true]);
    }

    public function releaseDeliveryAgentForOrder(Order $order): void
    {
        if (! $order->delivery_agent_id) {
            return;
        }

        DeliveryAgent::query()->whereKey($order->delivery_agent_id)->update(['status' => 'available']);
    }
}

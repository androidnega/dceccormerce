<?php

namespace App\Support;

use App\Models\Order;

/**
 * Single place for fulfillment channel detection and step timelines (pickup, manual, courier).
 */
final class OrderDeliveryPipeline
{
    public const CHANNEL_PICKUP = 'pickup';

    public const CHANNEL_MANUAL = 'manual';

    public const CHANNEL_COURIER = 'courier';

    public static function resolveChannel(Order $order): string
    {
        $method = strtolower(trim((string) ($order->delivery_method ?? '')));
        $option = strtolower(trim((string) ($order->delivery_option ?? '')));

        if ($method === 'pickup' || $option === 'pickup') {
            return self::CHANNEL_PICKUP;
        }

        if ($method === 'manual') {
            return self::CHANNEL_MANUAL;
        }

        return self::CHANNEL_COURIER;
    }

    public static function channelLabel(string $channel): string
    {
        return match ($channel) {
            self::CHANNEL_PICKUP => 'Store pickup',
            self::CHANNEL_MANUAL => 'Manual / in-house dispatch',
            self::CHANNEL_COURIER => 'Courier delivery',
            default => 'Delivery',
        };
    }

    public static function channelDescription(string $channel): string
    {
        return match ($channel) {
            self::CHANNEL_PICKUP => 'Customer collects at the store when the order is ready.',
            self::CHANNEL_MANUAL => 'Your team fulfills this order without the courier workflow (assign / on the way).',
            self::CHANNEL_COURIER => 'A delivery agent or rider takes the package to the customer address.',
            default => '',
        };
    }

    /**
     * Steps for buyer-facing UI (tracking, account order).
     *
     * @return list<array{key: string, title: string, buyer: string}>
     */
    public static function customerSteps(Order $order): array
    {
        $ch = self::resolveChannel($order);

        if ($ch === self::CHANNEL_PICKUP) {
            return [
                ['key' => 'pending', 'title' => 'Order placed', 'buyer' => 'We received your order.'],
                ['key' => 'confirmed', 'title' => 'Confirmed', 'buyer' => 'The store confirmed your order.'],
                ['key' => 'prepared', 'title' => 'Ready for pickup', 'buyer' => 'Your items are packed and waiting at the store.'],
                ['key' => 'delivered', 'title' => 'Collected', 'buyer' => 'Pickup completed. Thank you!'],
            ];
        }

        if ($ch === self::CHANNEL_MANUAL) {
            return [
                ['key' => 'pending', 'title' => 'Order placed', 'buyer' => 'We received your order.'],
                ['key' => 'confirmed', 'title' => 'Confirmed', 'buyer' => 'The store confirmed your order.'],
                ['key' => 'prepared', 'title' => 'Being prepared', 'buyer' => 'Your order is being prepared for handoff.'],
                ['key' => 'delivered', 'title' => 'Completed', 'buyer' => 'Your order was handed off. Thank you!'],
            ];
        }

        return [
            ['key' => 'pending', 'title' => 'Order placed', 'buyer' => 'We received your order.'],
            ['key' => 'confirmed', 'title' => 'Confirmed', 'buyer' => 'The store confirmed your order.'],
            ['key' => 'prepared', 'title' => 'Packed', 'buyer' => 'Items are packed and ready for dispatch.'],
            ['key' => 'assigned', 'title' => 'Courier assigned', 'buyer' => 'A delivery partner is assigned.'],
            ['key' => 'on_the_way', 'title' => 'On the way', 'buyer' => 'The package is heading to you.'],
            ['key' => 'delivered', 'title' => 'Delivered', 'buyer' => 'Delivery completed. Thank you!'],
        ];
    }

    /**
     * Steps for manager/admin (same keys, operational wording).
     *
     * @return list<array{key: string, title: string, staff: string}>
     */
    public static function staffSteps(Order $order): array
    {
        $ch = self::resolveChannel($order);

        if ($ch === self::CHANNEL_PICKUP) {
            return [
                ['key' => 'pending', 'title' => 'Placed', 'staff' => 'New order.'],
                ['key' => 'confirmed', 'title' => 'Confirmed', 'staff' => 'You confirmed the order.'],
                ['key' => 'prepared', 'title' => 'Ready', 'staff' => 'Packed; customer can collect.'],
                ['key' => 'delivered', 'title' => 'Closed', 'staff' => 'Mark when customer collected.'],
            ];
        }

        if ($ch === self::CHANNEL_MANUAL) {
            return [
                ['key' => 'pending', 'title' => 'Placed', 'staff' => 'New order.'],
                ['key' => 'confirmed', 'title' => 'Confirmed', 'staff' => 'Confirmed with customer.'],
                ['key' => 'prepared', 'title' => 'Prepared', 'staff' => 'Ready for your own handoff.'],
                ['key' => 'delivered', 'title' => 'Completed', 'staff' => 'Handoff done.'],
            ];
        }

        return [
            ['key' => 'pending', 'title' => 'Placed', 'staff' => 'New order.'],
            ['key' => 'confirmed', 'title' => 'Confirmed', 'staff' => 'Ready to prepare.'],
            ['key' => 'prepared', 'title' => 'Prepared', 'staff' => 'Ready to assign agent.'],
            ['key' => 'assigned', 'title' => 'Assigned', 'staff' => 'Agent / rider linked.'],
            ['key' => 'on_the_way', 'title' => 'Out', 'staff' => 'In transit.'],
            ['key' => 'delivered', 'title' => 'Delivered', 'staff' => 'Closed successfully.'],
        ];
    }

    /**
     * @return array{
     *   channel: string,
     *   channel_label: string,
     *   delivery_status: string,
     *   is_failed: bool,
     *   is_delivered: bool,
     *   current_index: int|null,
     *   steps: list,
     *   failed_previous_status: ?string
     * }
     */
    public static function progressForCustomer(Order $order): array
    {
        $channel = self::resolveChannel($order);
        $steps = self::customerSteps($order);
        $keys = array_column($steps, 'key');
        $ds = (string) ($order->delivery_status ?? 'pending');
        $failed = $ds === 'failed';
        $cancelled = $ds === 'cancelled';
        $delivered = $ds === 'delivered';

        $effective = $failed ? (string) ($order->failed_previous_status ?? 'pending') : $ds;
        $currentIndex = array_search($effective, $keys, true);
        if ($currentIndex === false) {
            $currentIndex = 0;
        }

        return [
            'channel' => $channel,
            'channel_label' => self::channelLabel($channel),
            'delivery_status' => $ds,
            'is_failed' => $failed || $cancelled,
            'is_delivered' => $delivered,
            'current_index' => ($failed || $cancelled) ? null : (int) $currentIndex,
            'steps' => $steps,
            'failed_previous_status' => $failed ? (string) ($order->failed_previous_status ?? '') : null,
        ];
    }

    /**
     * @return array{channel: string, channel_label: string, steps: list, current_index: int|null, delivery_status: string, is_failed: bool}
     */
    public static function progressForStaff(Order $order): array
    {
        $channel = self::resolveChannel($order);
        $steps = self::staffSteps($order);
        $keys = array_column($steps, 'key');
        $ds = (string) ($order->delivery_status ?? 'pending');
        $failed = $ds === 'failed';
        $cancelled = $ds === 'cancelled';

        $effective = $failed ? (string) ($order->failed_previous_status ?? 'pending') : $ds;
        $currentIndex = array_search($effective, $keys, true);
        if ($currentIndex === false) {
            $currentIndex = 0;
        }

        return [
            'channel' => $channel,
            'channel_label' => self::channelLabel($channel),
            'channel_description' => self::channelDescription($channel),
            'steps' => $steps,
            'current_index' => ($failed || $cancelled) ? null : (int) $currentIndex,
            'delivery_status' => $ds,
            'is_failed' => $failed || $cancelled,
            'is_delivered' => $ds === 'delivered',
        ];
    }
}

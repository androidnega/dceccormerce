<?php

namespace App\Services;

use App\Models\Order;
use App\Support\GhanaPhone;
use Illuminate\Support\Facades\Log;

class OrderNotificationService
{
    public function __construct(
        private readonly SmsService $sms,
        private readonly WhatsAppService $whatsapp,
    ) {}

    public function notifyOrderPlaced(Order $order): void
    {
        $order->loadMissing('address');
        $name = trim((string) ($order->address?->full_name ?? 'Customer'));
        $phone = (string) ($order->address?->phone ?? '');
        $msg = "Hello {$name}, your order {$order->order_number} has been received. We will contact you shortly.";

        $this->dispatch($phone, $msg);
    }

    public function notifyOrderConfirmed(Order $order): void
    {
        $order->loadMissing('address');
        $phone = (string) ($order->address?->phone ?? '');
        $msg = "Your order {$order->order_number} has been confirmed.";

        $this->dispatch($phone, $msg);
    }

    public function notifyRiderAssigned(Order $order): void
    {
        $order->loadMissing('address', 'rider', 'deliveryAgent');
        $phone = (string) ($order->address?->deliveryRecipientPhone() ?? '');

        if ($order->rider) {
            $riderName = trim((string) $order->rider->name);
            $masked = GhanaPhone::maskLocal($order->rider->phone);
            $msg = "Rider {$riderName} ({$masked}) will deliver your order.";
        } elseif ($order->deliveryAgent) {
            $name = trim((string) $order->deliveryAgent->name);
            $masked = GhanaPhone::maskLocal($order->deliveryAgent->phone);
            $msg = $masked !== ''
                ? "{$name} ({$masked}) will handle your delivery."
                : "{$name} will handle your delivery.";
        } else {
            $msg = 'A delivery partner will handle your order.';
        }

        $this->dispatch($phone, $msg);
    }

    public function notifyOnTheWay(Order $order): void
    {
        $order->loadMissing('address');
        $phone = (string) ($order->address?->deliveryRecipientPhone() ?? '');
        $msg = 'Your order is on the way. Please be available.';

        $this->dispatch($phone, $msg);
    }

    public function notifyDelivered(Order $order): void
    {
        $order->loadMissing('address');
        $phone = (string) ($order->address?->deliveryRecipientPhone() ?? '');
        $msg = 'Order delivered successfully. Thank you!';

        $this->dispatch($phone, $msg);
    }

    private function dispatch(string $phone, string $message): void
    {
        if ($phone === '') {
            Log::warning('Notification skipped: no phone on order address.');

            return;
        }

        if (! sms_notifications_enabled() && ! whatsapp_notifications_enabled()) {
            return;
        }

        if (sms_notifications_enabled()) {
            try {
                $this->sms->sendSms($phone, $message);
            } catch (\Throwable $e) {
                Log::error('Order SMS notification error.', ['message' => $e->getMessage()]);
            }
        }

        if (whatsapp_notifications_enabled()) {
            try {
                $this->whatsapp->sendWhatsApp($phone, $message);
            } catch (\Throwable $e) {
                Log::error('Order WhatsApp notification error.', ['message' => $e->getMessage()]);
            }
        }
    }
}

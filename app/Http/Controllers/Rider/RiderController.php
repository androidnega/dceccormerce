<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rider;
use App\Services\OrderDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function __construct(
        private readonly OrderDeliveryService $orderDelivery,
    ) {}

    public function dashboard(): View
    {
        $rider = $this->rider();
        $orders = Order::query()
            ->where('rider_id', $rider->id)
            ->with(['address'])
            ->latest()
            ->paginate(15);

        return view('rider.dashboard', compact('rider', 'orders'));
    }

    public function showOrder(Order $order): View
    {
        $this->ensureAssigned($order);
        $order->load(['address', 'items.product.images']);

        return view('rider.orders.show', compact('order'));
    }

    public function markOnTheWay(Order $order): RedirectResponse
    {
        $this->ensureAssigned($order);
        $result = $this->orderDelivery->markOnTheWay($order->fresh());
        if (! $result['ok']) {
            return back()->withErrors(['status' => $result['error'] ?? 'Unable to update order.']);
        }

        return back()->with('status', 'Order marked as on the way.');
    }

    public function markDelivered(Order $order): RedirectResponse
    {
        $this->ensureAssigned($order);
        $result = $this->orderDelivery->markDelivered($order->fresh());
        if (! $result['ok']) {
            return back()->withErrors(['status' => $result['error'] ?? 'Unable to update order.']);
        }

        return back()->with('status', 'Order marked as delivered.');
    }

    public function markFailed(Order $order): RedirectResponse
    {
        $this->ensureAssigned($order);
        $result = $this->orderDelivery->markFailed($order->fresh());
        if (! $result['ok']) {
            return back()->withErrors(['status' => $result['error'] ?? 'Unable to update order.']);
        }

        return back()->with('status', 'Order marked as failed.');
    }

    private function rider(): Rider
    {
        $rider = Rider::query()->where('user_id', auth()->id())->first();
        abort_if($rider === null, 403, 'No rider profile is linked to your account. Contact an administrator.');

        return $rider;
    }

    private function ensureAssigned(Order $order): void
    {
        $rider = $this->rider();
        abort_unless((int) $order->rider_id === (int) $rider->id, 403);
    }
}

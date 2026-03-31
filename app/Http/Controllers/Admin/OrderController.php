<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\Rider;
use App\Services\OrderDeliveryService;
use App\Services\OrderNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderNotificationService $orderNotifications,
        private readonly OrderDeliveryService $orderDelivery,
    ) {}

    public function index(): View
    {
        $orders = Order::query()
            ->with(['user', 'rider'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product.images', 'address', 'user', 'rider', 'deliveryAgent.rider']);

        $orderDeliveryMethod = $this->resolveDeliveryMethodForOrder($order);

        $availableAgents = collect();
        if (! in_array($orderDeliveryMethod, ['pickup', 'manual'], true)) {
            $availableAgents = DeliveryAgent::query()
                ->where('type', $orderDeliveryMethod)
                ->where(function ($q) use ($order): void {
                    $q->where('status', 'available');
                    if ($order->delivery_agent_id) {
                        $q->orWhere('id', $order->delivery_agent_id);
                    }
                })
                ->orderBy('name')
                ->get();
        }

        return view('admin.orders.show', compact('order', 'availableAgents', 'orderDeliveryMethod'));
    }

    public function confirmOrder(Order $order): RedirectResponse
    {
        if (! $order->canTransitionTo('confirmed')) {
            return back()->withErrors(['status' => 'Order cannot be confirmed from its current delivery status.']);
        }

        $order->update([
            'status' => 'confirmed',
            'delivery_status' => 'confirmed',
        ]);

        try {
            $this->orderNotifications->notifyOrderConfirmed($order->fresh(['address']));
        } catch (\Throwable) {
            //
        }

        return back()->with('status', 'Order confirmed.');
    }

    public function prepareOrder(Order $order): RedirectResponse
    {
        if (! $order->canTransitionTo('prepared')) {
            return back()->withErrors(['status' => 'Order cannot be prepared from its current delivery status.']);
        }

        $order->update([
            'status' => 'prepared',
            'delivery_status' => 'prepared',
        ]);

        return back()->with('status', 'Order prepared for dispatch.');
    }

    public function assignDeliveryAgent(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_agent_id' => ['required', 'integer', 'exists:delivery_agents,id'],
        ]);

        if (! $order->canTransitionTo('assigned')) {
            return back()->withErrors(['status' => 'Order cannot be assigned at this stage.']);
        }

        $expectedMethod = $this->resolveDeliveryMethodForOrder($order);

        $error = DB::transaction(function () use ($order, $validated, $expectedMethod) {
            $agent = DeliveryAgent::query()->lockForUpdate()->findOrFail($validated['delivery_agent_id']);

            if ($agent->type !== $expectedMethod) {
                return 'method_mismatch';
            }

            if ($agent->status !== 'available' && (int) $order->delivery_agent_id !== (int) $agent->id) {
                return 'unavailable';
            }

            if ($order->delivery_agent_id && (int) $order->delivery_agent_id !== (int) $agent->id) {
                DeliveryAgent::query()->whereKey($order->delivery_agent_id)->update(['status' => 'available']);
            }

            if ($order->rider_id) {
                Rider::query()->whereKey($order->rider_id)->update(['is_available' => true]);
            }

            $order->update([
                'delivery_agent_id' => $agent->id,
                'rider_id' => $agent->rider_id,
                'status' => 'assigned',
                'delivery_status' => 'assigned',
            ]);

            $agent->update(['status' => 'busy']);

            return null;
        });

        if ($error === 'method_mismatch') {
            return back()->withErrors(['delivery_agent_id' => 'Selected agent does not match this order’s delivery method.']);
        }

        if ($error === 'unavailable') {
            return back()->withErrors(['delivery_agent_id' => 'Selected agent is not available.']);
        }

        try {
            $this->orderNotifications->notifyRiderAssigned($order->fresh(['address', 'rider', 'deliveryAgent']));
        } catch (\Throwable) {
            //
        }

        return back()->with('status', 'Delivery agent assigned.');
    }

    private function resolveDeliveryMethodForOrder(Order $order): string
    {
        $method = (string) ($order->delivery_method ?? '');
        if ($method === '') {
            $method = ($order->delivery_option ?? '') === 'pickup' ? 'pickup' : 'rider';
        }

        return $method;
    }

    public function markOnTheWay(Order $order): RedirectResponse
    {
        $result = $this->orderDelivery->markOnTheWay($order);
        if (! $result['ok']) {
            return back()->withErrors(['status' => $result['error'] ?? 'Unable to update order.']);
        }

        return back()->with('status', 'Order marked as on the way.');
    }

    public function markDelivered(Order $order): RedirectResponse
    {
        $result = $this->orderDelivery->markDelivered($order);
        if (! $result['ok']) {
            return back()->withErrors(['status' => $result['error'] ?? 'Unable to update order.']);
        }

        return back()->with('status', 'Order marked as delivered.');
    }

    public function markFailed(Order $order): RedirectResponse
    {
        $result = $this->orderDelivery->markFailed($order);
        if (! $result['ok']) {
            return back()->withErrors(['status' => $result['error'] ?? 'Unable to update order.']);
        }

        return back()->with('status', 'Order marked as failed.');
    }

    public function updateNotes(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update([
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('status', 'Order note updated.');
    }
}

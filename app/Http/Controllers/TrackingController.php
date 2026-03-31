<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\OrderDeliveryPipeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(): View
    {
        return view('tracking.index');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:32'],
        ]);

        $normalized = strtoupper(trim($validated['order_number']));
        $normalized = preg_replace('/\s+/', '', $normalized) ?? $normalized;

        return redirect()->route('orders.track', ['order_number' => $normalized]);
    }

    public function show(string $order_number): View
    {
        $normalized = strtoupper(trim($order_number));
        $normalized = preg_replace('/\s+/', '', $normalized) ?? $normalized;

        $order = Order::query()
            ->whereRaw('UPPER(order_number) = ?', [$normalized])
            ->with(['rider', 'deliveryAgent'])
            ->firstOrFail();

        return view('tracking.show', compact('order'));
    }

    /**
     * Lightweight JSON for live tracking (poll from the public track page).
     */
    public function status(string $order_number): JsonResponse
    {
        $normalized = strtoupper(trim($order_number));
        $normalized = preg_replace('/\s+/', '', $normalized) ?? $normalized;

        $order = Order::query()
            ->whereRaw('UPPER(order_number) = ?', [$normalized])
            ->firstOrFail();

        $p = OrderDeliveryPipeline::progressForCustomer($order);

        return response()->json([
            'order_number' => $order->order_number,
            'delivery_status' => $order->delivery_status,
            'payment_status' => $order->payment_status,
            'channel' => $p['channel'],
            'is_failed' => $p['is_failed'],
            'is_delivered' => $p['is_delivered'],
            'current_index' => $p['current_index'],
        ]);
    }
}

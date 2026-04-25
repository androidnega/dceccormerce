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
            'token' => ['required', 'string', 'max:64'],
        ]);

        $normalized = strtoupper(trim($validated['order_number']));
        $normalized = preg_replace('/\s+/', '', $normalized) ?? $normalized;

        return redirect()->route('orders.track', [
            'order_number' => $normalized,
            'token' => $validated['token'],
        ]);
    }

    public function show(Request $request, string $order_number): View
    {
        $token = trim((string) $request->query('token', ''));
        abort_if($token === '', 403);

        $order = Order::findByOrderNumberAndAccessToken($order_number, $token);
        abort_if($order === null, 403);

        $order->load(['rider', 'deliveryAgent']);

        return view('tracking.show', compact('order'));
    }

    public function status(Request $request, string $order_number): JsonResponse
    {
        $token = trim((string) $request->query('token', ''));
        abort_if($token === '', 403);

        $order = Order::findByOrderNumberAndAccessToken($order_number, $token);
        abort_if($order === null, 403);

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
